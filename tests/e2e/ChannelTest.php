<?php

namespace tests\e2e;

use PHPUnit\Framework;

use stock2shop\vo;
use stock2shop\dal;

/**
 * This "end to end" test runs through all channel types.
 * @package tests\e2e
 */
final class ChannelTest extends Framework\TestCase
{
    /** @var dal\channel\Creator */
    static $creator;

    /** @var vo\Channel $channel */
    static $channel;

    /** @var string[] */
    static $channelTypes;

    /** @var array */
    static $channelFulfillmentsData;

    /** @var array */
    static $channelProductsData;

    /** @var array */
    static $channelMetaData;

    /** @var array */
    static $channelOrderData;

    /**
     * Set Up
     *
     * This function calls the getChannelTypes() function to prepare the test
     * data for this e2e test.
     *
     * @return
     */
    function setUp()
    {
        self::$channelTypes = self::getChannelTypes();
    }

//    function testSyncProducts()
//    {
//        foreach (self::$channelTypes as $type) {
//
//            // load test data and set channel
//            self::loadTestData($type);
//            self::setChannel($type);
//
//            // sync all products
//            $request  = new ChannelProductsSync(
//                [
//                    "meta"             => self::$channelMetaData,
//                    "channel_products" => self::$channelProductsData,
//                    "flag_map"         => []
//                ]
//            );
//            $response = self::$channel->syncProducts($request);
//            self::verifyProductSync($request, $response);
//
//            // Delete variant
//            unset(self::$channelProductsData[1]['variants'][1]);
//            $request  = new ChannelProductsSync(
//                [
//                    "meta"             => self::$channelMetaData,
//                    "channel_products" => self::$channelProductsData,
//                    "flag_map"         => []
//                ]
//            );
//            $response = self::$channel->syncProducts($request);
//            self::verifyProductSync($request, $response);
//
//            // Remove all products
//            foreach (self::$channelProductsData as $key => $product) {
//                self::$channelProductsData[$key]['delete'] = true;
//            }
//            $request  = new ChannelProductsSync(
//                [
//                    "meta"             => self::$channelMetaData,
//                    "channel_products" => self::$channelProductsData,
//                    "flag_map"         => []
//                ]
//            );
//            $response = self::$channel->syncProducts($request);
//            self::verifyProductSync($request, $response);
//
//            // send empty payload
//            $request  = new ChannelProductsSync(
//                [
//                    "meta"             => self::$channelMetaData,
//                    "channel_products" => [],
//                    "flag_map"         => []
//                ]
//            );
//            $response = self::$channel->syncProducts($request);
//            self::verifyProductSync($request, $response);
//
//        }
//
//    }
//
//    function testGetProducts()
//    {
//        foreach (self::$channelTypes as $type) {
//
//            // load test data and set channel
//            self::loadTestData($type);
//            self::setChannel($type);
//
//            // sync all test data
//            $request  = new ChannelProductsSync(
//                [
//                    "meta"             => self::$channelMetaData,
//                    "channel_products" => self::$channelProductsData,
//                    "flag_map"         => []
//                ]
//            );
//            $response = self::$channel->syncProducts($request);
//
//            // fetch all products
//            $token           = "";
//            $limit           = count(self::$channelProductsData);
//            $meta            = MetaItem::createArray(self::$channelMetaData);
//            $fetchedProducts = self::$channel->getProducts("", count(self::$channelProductsData), $meta);
//            self::verifyGetProducts($token, $limit, $fetchedProducts);
//
//            // Get products using paging (one at a time)
//            $token = "";
//            $cnt   = 0;
//            for ($i = 0; $i < count(self::$channelProductsData); $i++) {
//                $fetchedProducts = self::$channel->getProducts($token, 1, $meta);
//                self::verifyGetProducts($token, 1, $fetchedProducts);
//                $cnt   += count($fetchedProducts);
//                $token = $fetchedProducts[0]->token;
//            }
//            $this->assertEquals(count(self::$channelProductsData), $cnt);
//        }
//    }
//
//    function testTransformOrder()
//    {
//        foreach (self::$channelTypes as $type) {
//
//            // load test data and set channel
//            self::loadTestData($type);
//            self::setChannel($type);
//
//            $channelOrder = self::$channel->transformOrder(
//                self::$channelOrderData,
//                MetaItem::createArray(self::$channelMetaData)
//            );
//            $this->verifyTransformOrder($channelOrder);
//        }
//    }
//
//    function testGetOrders()
//    {
//        foreach (self::$channelTypes as $type) {
//
//            // load test data and set channel
//            self::loadTestData($type);
//            self::setChannel($type);
//
//            // Get orders (return 2)
//            $fetchedOrders = self::$channel->getOrders("", 2, MetaItem::createArray(self::$channelMetaData));
//            $this->assertEquals(2, count($fetchedOrders));
//            foreach ($fetchedOrders as $order) {
//                $this->verifyTransformOrder($order);
//            }
//        }
//    }
//
//    function testGetOrdersByCode()
//    {
//        foreach (self::$channelTypes as $type) {
//
//            // load test data and set channel
//            self::loadTestData($type);
//            self::setChannel($type);
//
//            $channelOrder = self::$channel->transformOrder(
//                self::$channelOrderData,
//                MetaItem::createArray(self::$channelMetaData)
//            );
//
//            // Get orders (return 2)
//            $fetchedOrders = self::$channel->getOrdersByCode([$channelOrder], MetaItem::createArray(self::$channelMetaData));
//            $this->assertEquals(1, count($fetchedOrders));
//            foreach ($fetchedOrders as $order) {
//                $this->verifyTransformOrder($order);
//            }
//        }
//    }
//
//    function testSyncFulfillments()
//    {
//        foreach (self::$channelTypes as $type) {
//
//            // load test data and set channel
//            self::loadTestData($type);
//            self::setChannel($type);
//
//            // sync all fulfillments
//            $request  = new ChannelFulfillmentsSync(
//                [
//                    "meta"             => self::$channelMetaData,
//                    "channel_fulfillments" => self::$channelFulfillmentsData
//                ]
//            );
//            $response = self::$channel->syncFulfillments($request);
//            self::verifyFulfillmentSync($request, $response);
//        }
//
//    }
//
//    /**
//     * @param $channelOrder
//     * @param $webhook
//     */
//    function verifyTransformOrder($channelOrder)
//    {
//        $this->assertInstanceOf("stock2shop\\vo\\ChannelOrder", $channelOrder);
//        $this->assertNotEmpty($channelOrder->channel_order_code);
//    }
//
//    /**
//     * @param string $token
//     * @param int $limit
//     * @param ChannelProductGet[] $fetchedProducts
//     */
//    function verifyGetProducts($token, $limit, $fetchedProducts)
//    {
//
//        // only return up to limit
//        $this->assertLessThanOrEqual($limit, count($fetchedProducts));
//        $currentToken = $token;
//        foreach ($fetchedProducts as $product) {
//
//            // return type valid
//            $this->assertInstanceOf("stock2shop\\vo\\ChannelProductGet", $product);
//
//            // product token must not be less than token
//            $this->assertGreaterThan($token, $product->token);
//
//            // results must be ordered by token
//            $this->assertGreaterThan($currentToken, $product->token);
//            $currentToken = $product->token;
//
//            // properties should be set
//            $this->assertNotEmpty($product->token);
//            $this->assertNotEmpty($product->channel_product_code);
//            foreach ($product->variants as $variant) {
//                $this->assertNotEmpty($variant->channel_variant_code);
//                $this->assertNotEmpty($variant->sku);
//            }
//        }
//    }
//
//    /**
//     * @param ChannelProductsSync $request
//     * @param ChannelProductsSync $response
//     */
//    function verifyProductSync(ChannelProductsSync $request, ChannelProductsSync $response)
//    {
//        // Check against existing products on channel by fetching them first
//        $existingProducts   = self::$channel->getProductsByCode($request);
//        $requestProductCnt  = 0;
//        $requestVariantCnt  = 0;
//        $existingVariantCnt = 0;
//        foreach ($request->channel_products as $key => $product) {
//            if (!$product->delete) {
//                $requestProductCnt++;
//                foreach ($product->variants as $variant) {
//                    $requestVariantCnt++;
//                }
//            }
//        }
//        foreach ($existingProducts->channel_products as $key => $product) {
//            foreach ($product->variants as $variant) {
//                $existingVariantCnt++;
//            }
//        }
//        $this->assertEquals($requestProductCnt, count($existingProducts->channel_products), ' sync count');
////        $this->assertEquals($requestVariantCnt, $existingVariantCnt, ' sync count');
//
//        // Check response values are set
//        $responseProductMap = [];
//        $responseVariantMap = [];
//        foreach ($response->channel_products as $product) {
//            $responseProductMap[$product->channel_product_code] = $product;
//            foreach ($product->variants as $variant) {
//                $responseVariantMap[$variant->channel_variant_code] = $variant;
//            }
//        }
//        foreach ($existingProducts->channel_products as $key => $existingProduct) {
//            $product = $responseProductMap[$existingProduct->channel_product_code];
//            $this->assertTrue($product->success, ' success set to true');
//            $this->assertNotEmpty($product->synced, ' synced set');
//            $this->assertTrue(ChannelProduct::isValidSynced($product->synced), ' success set to true');
//            $this->assertNotEmpty($product->channel_product_code, ' channel_product_code set');
////            $this->assertEquals(
////                count($existingProduct->variants),
////                (count($product->variants))
////            );
//            foreach ($existingProduct->variants as $existingVariant) {
//                $variant = $responseVariantMap[$existingVariant->channel_variant_code];
//                $this->assertTrue($variant->success, ' success set to true');
//                $this->assertNotEmpty($variant->channel_variant_code, ' channel_variant_code set');
//            }
//        }
//    }
//
//    function verifyFulfillmentSync(ChannelFulfillmentsSync $request, ChannelFulfillmentsSync $response)
//    {
//        $codes = [];
//        $this->assertInstanceOf("stock2shop\\vo\\ChannelFulfillmentsSync", $response);
//        $this->assertEquals(count($request->channel_fulfillments), count($response->channel_fulfillments));
//        foreach ($response->channel_fulfillments as $f) {
//            $this->assertInstanceOf("stock2shop\\vo\\ChannelFulfillment", $f);
//            $this->assertNotEmpty($f->channel_fulfillment_code);
//            $this->assertNotEmpty($f->channel_synced);
//            $this->assertTrue(ChannelFulfillment::isValidChannelSynced($f->channel_synced));
//            $codes[$f->channel_fulfillment_code] = $f->channel_fulfillment_code;
//        }
//        $current = self::$channel->getFulfillmentsByOrderCode($response);
//        $this->assertEquals(count($request->channel_fulfillments), count($current));
//        foreach ($current as $f) {
//            $this->assertInstanceOf("stock2shop\\vo\\ChannelFulfillment", $f);
//            $this->assertArrayHasKey($f->channel_fulfillment_code, $codes);
//        }
//    }

    /**
     * Load Test Data
     *
     * This function gets the test data from the JSON files in the /data directory.
     *
     * @param string $type
     * @return void
     */
    function loadTestData(string $type)
    {
        // Get data from JSON files.
        $channelFulfillmentsJSON = file_get_contents(__DIR__ . '/data/syncChannelFulfillments.json');
        $channelProductsJSON = file_get_contents(__DIR__ . '/data/syncChannelProducts.json');
        $channelMetaJSON = file_get_contents(__DIR__ . '/data/channelMeta.json');
        $channelOrderJSON = file_get_contents(__DIR__ . '/data/channels/' . $type . '/orderTransform.json');

        // Decode into arrays and set testing data to class properties.
        self::$channelFulfillmentsData = json_decode($channelFulfillmentsJSON, true);
        self::$channelProductsData = json_decode($channelProductsJSON, true);
        self::$channelMetaData = json_decode($channelMetaJSON, true);
        self::$channelOrderData = json_decode($channelOrderJSON, true);
    }

    /**
     * Get Channel Types
     *
     * Channel types are directories and classes found in /dal/channels/.
     *
     * @return array
     */
    function getChannelTypes(): array
    {
        $channels = [];
        $items = array_diff(scandir(
            __DIR__ . '/../../www/v1/stock2shop/dal/channels',
            SCANDIR_SORT_ASCENDING
        ), array('..', '.'));
        foreach ($items as $item) {
            $channels[] = $item;
        }
        return $channels;
    }

    /**
     * Set Factory
     *
     * Creates a new factory object for a connector integration.
     * This function will break the test if a Creator.php with a
     * valid implementation is not found.
     *
     * @param $type
     */
    function setFactory($type)
    {
        // Instantiate factory creator object.
        $creatorNameSpace = "stock2shop\\dal\\channels\\" . $type . "\\Creator";
        self::$creator = new $creatorNameSpace();

        // Evaluate whether the object is a valid concrete class
        // implementation of the Creator class.
        $this->assertInstanceOf("stock2shop\\dal\\channel\\Creator", self::$creator);
    }

    /**
     * Test Sync
     *
     * This test case evaluates the implementation of the sync() method from
     * the createProducts() factory.
     *
     * The goal of sync() is to synchronise the product data onto a Stock2Shop
     * channel. Synchronisation in this context may refer to:
     *
     * 1. Adding of products, variants, images and other meta information to a channel.
     * 2. Removing of product variants or images from a channel.
     * 3. Removing a product or more than one product from a channel.
     *
     * The workflow of this test runs through four scenarios - the results of which are
     * deconstructed and asserted on in a separate method called verifyTestSync().
     * You may expect the outcomes to indicate issues, bugs and logic problems in specific
     * areas of code in the integration you are working on.
     */
    function testSync()
    {

        // Loop through the channel types found in the
        // dal/channels/ directory.
        foreach (self::$channelTypes as $type) {

            // Load test data and set channel
            self::loadTestData($type);
            self::setFactory($type);

            // Instantiate the Creator factory object.
            /** @var dal\channel\Creator $creator */
            $creator = self::$creator;

            // Get the products connector object.
            $connector = $creator->createProducts();

            // Instantiate new channel object using the test channel meta data.
            $channel = new vo\Channel(self::$channelMetaData);

            // Create empty flag map.
            $flagMap = [];

            // Prepare the request by creating an array of ChannelProducts.
            $request = vo\ChannelProduct::createArray(self::$channelProductsData);
            $this->assertNotNull($request);

            // --------------------------------------------------------

            // Create all products on the channel.
            // Run the sync on the channel.
            $response = $connector->sync($request, $channel, $flagMap);
            $this->assertNotNull($response);

            // Verify the sync.
            self::verifyProductSync($request, $response, $connector, $channel);

            // --------------------------------------------------------

            // Delete a variant from a product.
            unset($request[1]->variants[1]);

            // Run the sync on the channel.
            $response = $connector->sync($request, $channel, $flagMap);
            $this->assertNotNull($response);

            // Verify the sync.
            self::verifyProductSync($request, $response, $connector, $channel);

            // --------------------------------------------------------

            // Remove all products by setting 'delete' to true.
            foreach ($request as $key => $product) {
                $product->delete = true;
            }

            // Run the sync on the channel.
            $response = $connector->sync($request, $channel, $flagMap);
            $this->assertNotNull($response);

            // Verify the sync.
            self::verifyProductSync($request, $response, $connector, $channel);

            // --------------------------------------------------------

            // Send empty payload of ChannelProducts with connector.
            $response = $connector->sync([], $channel, $flagMap);
            $this->assertNotNull($response);

            // Verify the sync.
            self::verifyProductSync($request, $response, $connector, $channel);

        }

    }

    /**
     * Verify Product Sync
     *
     * Verifies the product sync.
     *
     * @param vo\ChannelProduct[] $request
     * @param vo\ChannelProduct[] $response
     * @param $connector
     * @param $channel
     * @return void
     */
    function verifyProductSync(array $request, array $response, $connector, $channel)
    {

        // Check against existing products on channel by fetching them first
        $existingProducts = $connector->getByCode($request, $channel);
        $this->assertNotNull($existingProducts);

        // Counter variables.
        $requestProductCnt  = 0;
        $requestVariantCnt  = 0;
        $existingVariantCnt = 0;

        // Loop through the request products.
        foreach ($request as $key => $product) {
            if (!$product->delete) {

                // Add to product counter.
                $requestProductCnt++;
                foreach ($product->variants as $variant) {

                    // Add to variant counter.
                    $requestVariantCnt++;
                }
            }
        }

        // Loop through the existing products
        foreach ($existingProducts as $key => $product) {
            foreach ($product->variants as $variant) {

                // Add to existing product variant counter.
                $existingVariantCnt++;
            }
        }

        $this->assertEquals($requestProductCnt, count($existingProducts), ' sync count');
        $this->assertEquals($requestVariantCnt, $existingVariantCnt, ' sync count');

        // Check response values are set
        $responseProductMap = [];
        $responseVariantMap = [];
        foreach ($response as $product) {
            $responseProductMap[$product->channel_product_code] = $product;
            foreach ($product->variants as $variant) {
                $responseVariantMap[$variant->channel_variant_code] = $variant;
            }
        }

        foreach ($existingProducts as $key => $existingProduct) {

            $product = $responseProductMap[$existingProduct->channel_product_code];
            $this->assertTrue($product->success, ' success set to true');
            $this->assertNotEmpty($product->synced, ' synced set');
            $this->assertTrue($product->valid(), ' success set to true');   // updated since VO changed.
            $this->assertNotEmpty($product->channel_product_code, ' channel_product_code set');
            $this->assertEquals(
                count($existingProduct->variants),
                (count($product->variants))
            );

            /** @var vo\ChannelVariant $existingVariant */
            foreach ($existingProduct->variants as $existingVariant) {

                $this->assertArrayHasKey($existingVariant->channel_variant_code, $responseVariantMap, " product variant not found in existing variant map.");

                $variant = $responseVariantMap[$existingVariant->channel_variant_code];
                $this->assertTrue($variant->success, ' success set to true');
                $this->assertNotEmpty($variant->channel_variant_code, ' channel_variant_code set');

            }

        }

    }

    /**
     * Verify Get Products
     *
     * @param string $token
     * @param int $limit
     * @param vo\ChannelProductGet[] $fetchedProducts
     * @return void
     */
    function verifyGetProducts(string $token, int $limit, array $fetchedProducts)
    {

        // only return up to limit
        $this->assertLessThanOrEqual($limit, count($fetchedProducts));
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

}