<?php

namespace tests\v1\stock2shop\dal\channels\example;

use PHPUnit\Framework;
use stock2shop\dal\channels\example;
use stock2shop\vo\ChannelProduct;
use stock2shop\vo\SyncChannelProducts;

/**
 * @package tests\e2e
 */
final class ConnectorTest extends Framework\TestCase
{
    static $creator;
    static $channel;
    static $channelProductsData;
    static $channelProductsMetaData;
    static $channelOrderData;

    function setUp()
    {
        self::$creator                 = new example\Creator();
        self::$channel                 = self::$creator->getChannel();
        $channelProductsJSON           = file_get_contents(__DIR__ . '/data/syncChannelProducts.json');
        $channelProductsMetaJSON       = file_get_contents(__DIR__ . '/data/syncChannelProductsMeta.json');
        $channelOrderJSON              = file_get_contents(__DIR__ . '/data/orderTransform.json');
        self::$channelProductsData     = json_decode($channelProductsJSON, true);
        self::$channelProductsMetaData = json_decode($channelProductsMetaJSON, true);
        self::$channelOrderData        = json_decode($channelOrderJSON, true);
    }

    function testSyncProducts()
    {
        // sync all products
        $request = new SyncChannelProducts(
            [
                "meta"             => self::$channelProductsMetaData,
                "channel_products" => self::$channelProductsData,
                "flag_map"         => []
            ]
        );
        $response = self::$channel->syncProducts($request);
        self::verifyProductSync($request, $response);

        // Delete variant
        unset(self::$channelProductsData[1]['variants'][1]);
        $request = new SyncChannelProducts(
            [
                "meta"             => self::$channelProductsMetaData,
                "channel_products" => self::$channelProductsData,
                "flag_map"         => []
            ]
        );
        $response  = self::$channel->syncProducts($request);
        self::verifyProductSync($request, $response);

        // Remove all products
        foreach (self::$channelProductsData as $key => $product) {
            self::$channelProductsData[$key]['delete'] = true;
        }
        $request = new SyncChannelProducts(
            [
                "meta"             => self::$channelProductsMetaData,
                "channel_products" => self::$channelProductsData,
                "flag_map"         => []
            ]
        );
        $response  = self::$channel->syncProducts($request);
        self::verifyProductSync($request, $response);

        // send empty payload
        $request = new SyncChannelProducts(
            [
                "meta"             => self::$channelProductsMetaData,
                "channel_products" => [],
                "flag_map"         => []
            ]
        );
        $response  = self::$channel->syncProducts($request);
        self::verifyProductSync($request, $response);
    }

    /**
     * @param SyncChannelProducts $request
     * @param SyncChannelProducts $response
     */
    function verifyProductSync(SyncChannelProducts $request, SyncChannelProducts $response)
    {
        // Check against existing products on channel by fetching them first
        $existingProducts = self::$channel->getProductsByCode($request);
        $requestProductCnt = 0;
        $requestVariantCnt = 0;
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


}