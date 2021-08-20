<?php

namespace tests\e2e;

use PHPUnit\Framework;
use stock2shop\dal\channels;
use stock2shop\vo\ChannelProduct;
use stock2shop\vo\ChannelProductGet;
use stock2shop\vo\MetaItem;
use stock2shop\vo\SyncChannelProducts;

/**
 *
 * This "end to end" test runs through all channel types.
 *
 *
 * @package tests\e2e
 */
final class ChannelTest extends Framework\TestCase
{
    static $creator;
    static $channel;
    static $channelTypes;
    static $channelProductsData;
    static $channelMetaData;
    static $channelOrderData;

    function setUp()
    {
        self::$channelTypes = self::getChannelTypes();
    }

    function testSyncProducts()
    {
        foreach (self::$channelTypes as $type) {

            // load test data and set channel
            self::loadTestData($type);
            self::setChannel($type);

            // sync all products
            $request  = new SyncChannelProducts(
                [
                    "meta"             => self::$channelMetaData,
                    "channel_products" => self::$channelProductsData,
                    "flag_map"         => []
                ]
            );
            $response = self::$channel->syncProducts($request);
            self::verifyProductSync($request, $response);

            // Delete variant
            unset(self::$channelProductsData[1]['variants'][1]);
            $request  = new SyncChannelProducts(
                [
                    "meta"             => self::$channelMetaData,
                    "channel_products" => self::$channelProductsData,
                    "flag_map"         => []
                ]
            );
            $response = self::$channel->syncProducts($request);
            self::verifyProductSync($request, $response);

            // Remove all products
            foreach (self::$channelProductsData as $key => $product) {
                self::$channelProductsData[$key]['delete'] = true;
            }
            $request  = new SyncChannelProducts(
                [
                    "meta"             => self::$channelMetaData,
                    "channel_products" => self::$channelProductsData,
                    "flag_map"         => []
                ]
            );
            $response = self::$channel->syncProducts($request);
            self::verifyProductSync($request, $response);

            // send empty payload
            $request  = new SyncChannelProducts(
                [
                    "meta"             => self::$channelMetaData,
                    "channel_products" => [],
                    "flag_map"         => []
                ]
            );
            $response = self::$channel->syncProducts($request);
            self::verifyProductSync($request, $response);

        }

    }

    function testGetProducts()
    {
        foreach (self::$channelTypes as $type) {

            // load test data and set channel
            self::loadTestData($type);
            self::setChannel($type);

            // sync all test data
            $request  = new SyncChannelProducts(
                [
                    "meta"             => self::$channelMetaData,
                    "channel_products" => self::$channelProductsData,
                    "flag_map"         => []
                ]
            );
            $response = self::$channel->syncProducts($request);

            // fetch all products
            $token           = "";
            $limit           = count(self::$channelProductsData);
            $meta            = MetaItem::createArray(self::$channelMetaData);
            $fetchedProducts = self::$channel->getProducts("", count(self::$channelProductsData), $meta);
            self::verifyGetProducts($token, $limit, $fetchedProducts);

            // Get products using paging (one at a time)
            $token = "";
            $cnt   = 0;
            for ($i = 0; $i < count(self::$channelProductsData); $i++) {
                $fetchedProducts = self::$channel->getProducts($token, 1, $meta);
                self::verifyGetProducts($token, 1, $fetchedProducts);
                $cnt   += count($fetchedProducts);
                $token = $fetchedProducts[0]->token;
            }
            $this->assertEquals(count(self::$channelProductsData), $cnt);
        }
    }

    function testTransformOrder()
    {
        foreach (self::$channelTypes as $type) {

            // load test data and set channel
            self::loadTestData($type);
            self::setChannel($type);

            $channelOrder = self::$channel->transformOrder(
                self::$channelOrderData,
                MetaItem::createArray(self::$channelMetaData)
            );
            $this->verifyTransformOrder($channelOrder);
        }
    }

    function testGetOrders() {
        foreach (self::$channelTypes as $type) {

            // load test data and set channel
            self::loadTestData($type);
            self::setChannel($type);

            // Get orders (return 2)
            $fetchedOrders = self::$channel->getOrders("", 2, MetaItem::createArray(self::$channelMetaData));
            $this->assertEquals(2, count($fetchedOrders));
            foreach ($fetchedOrders as $order) {
                $this->verifyTransformOrder($order);
            }
        }
    }

    function testGetOrdersByCode() {
        foreach (self::$channelTypes as $type) {

            // load test data and set channel
            self::loadTestData($type);
            self::setChannel($type);

            $channelOrder = self::$channel->transformOrder(
                self::$channelOrderData,
                MetaItem::createArray(self::$channelMetaData)
            );

            // Get orders (return 2)
            $fetchedOrders = self::$channel->getOrdersByCode([$channelOrder], MetaItem::createArray(self::$channelMetaData));
            $this->assertEquals(1, count($fetchedOrders));
            foreach ($fetchedOrders as $order) {
                $this->verifyTransformOrder($order);
            }
        }
    }

    /**
     * @param $channelOrder
     * @param $webhook
     */
    function verifyTransformOrder($channelOrder)
    {
        $this->assertInstanceOf("stock2shop\\vo\\ChannelOrder", $channelOrder);
        $this->assertNotEmpty($channelOrder->channel_order_code);
    }

    /**
     * @param string $token
     * @param int $limit
     * @param ChannelProductGet[] $fetchedProducts
     */
    function verifyGetProducts($token, $limit, $fetchedProducts)
    {

        // only return up to limit
        $this->assertLessThanOrEqual($limit, count($limit));
        $currentToken = $token;
        foreach ($fetchedProducts as $product) {

            // return type valid
            $this->assertInstanceOf("stock2shop\\vo\\ChannelProductGet", $product);

            // product token must not be less than token
            $this->assertGreaterThan($token, $product->token);

            // results must be ordered by token
            $this->assertGreaterThan($currentToken, $product->token);
            $currentToken = $product->token;

            // properties should be set
            $this->assertNotEmpty($product->token);
            $this->assertNotEmpty($product->channel_product_code);
            foreach ($product->variants as $variant) {
                $this->assertNotEmpty($variant->channel_variant_code);
                $this->assertNotEmpty($variant->sku);
            }
        }
    }

    /**
     * @param SyncChannelProducts $request
     * @param SyncChannelProducts $response
     */
    function verifyProductSync(SyncChannelProducts $request, SyncChannelProducts $response)
    {
        // Check against existing products on channel by fetching them first
        $existingProducts   = self::$channel->getProductsByCode($request);
        $requestProductCnt  = 0;
        $requestVariantCnt  = 0;
        $existingVariantCnt = 0;
        foreach ($request->channel_products as $key => $product) {
            if (!$product->delete) {
                $requestProductCnt++;
                foreach ($product->variants as $variant) {
                    $requestVariantCnt++;
                }
            }
        }
        foreach ($existingProducts->channel_products as $key => $product) {
            foreach ($product->variants as $variant) {
                $existingVariantCnt++;
            }
        }
        $this->assertEquals($requestProductCnt, count($existingProducts->channel_products), ' sync count');
        $this->assertEquals($requestVariantCnt, $existingVariantCnt, ' sync count');

        // Check response values are set
        $responseProductMap = [];
        $responseVariantMap = [];
        foreach ($response->channel_products as $product) {
            $responseProductMap[$product->channel_product_code] = $product;
            foreach ($product->variants as $variant) {
                $responseVariantMap[$variant->channel_variant_code] = $variant;
            }
        }
        foreach ($existingProducts->channel_products as $key => $existingProduct) {
            $product = $responseProductMap[$existingProduct->channel_product_code];
            $this->assertTrue($product->success, ' success set to true');
            $this->assertNotEmpty($product->synced, ' synced set');
            $this->assertTrue(ChannelProduct::isValidSynced($product->synced), ' success set to true');
            $this->assertNotEmpty($product->channel_product_code, ' channel_product_code set');
            $this->assertEquals(
                count($existingProduct->variants),
                (count($product->variants))
            );
            foreach ($existingProduct->variants as $existingVariant) {
                $variant = $responseVariantMap[$existingVariant->channel_variant_code];
                $this->assertTrue($variant->success, ' success set to true');
                $this->assertNotEmpty($variant->channel_variant_code, ' channel_variant_code set');
            }
        }
    }

    function loadTestData($type)
    {
        $channelProductsJSON       = file_get_contents(__DIR__ . '/data/syncChannelProducts.json');
        $channelMetaJSON           = file_get_contents(__DIR__ . '/data/syncChannelProductsMeta.json');
        $channelOrderJSON          = file_get_contents(__DIR__ . '/data/channels/' . $type . '/orderTransform.json');
        self::$channelProductsData = json_decode($channelProductsJSON, true);
        self::$channelMetaData     = json_decode($channelMetaJSON, true);
        self::$channelOrderData    = json_decode($channelOrderJSON, true);
    }

    /**
     * Channel types are directories and classes found in /dal/channels/
     *
     *
     * @return array
     */
    function getChannelTypes(): array
    {
        $channels = [];
        $items    = array_diff(scandir(
            __DIR__ . '/../../www/v1/stock2shop/dal/channels',
            SCANDIR_SORT_ASCENDING
        ), array('..', '.'));
        foreach ($items as $item) {
            $channels[] = $item;
        }
        return $channels;
    }

    /**
     * @param $type
     */
    function setChannel($type)
    {
        // Create channel class
        $creatorNameSpace = "stock2shop\\dal\\channels\\" . $type . "\\Creator";
        self::$creator    = new $creatorNameSpace();
        self::$channel    = self::$creator->getChannel();
    }


}