<?php

namespace tests\e2e;

use PHPUnit\Framework;
use stock2shop\vo;
use stock2shop\dal\channel;

/**
 * This "end to end" test runs through all channel types.
 * @package tests\e2e
 */
final class ChannelTest extends Framework\TestCase
{

    /** @var stock2shop\dal\channel\Creator $creator */
    static $creator;

    /** @var channel\Products | channel\Orders | channel\Fulfillments $connector */
    static $connector;

    /** @var string[] $channelTypes */
    static $channelTypes;

    /** @var array $channelFulfillmentsData */
    static $channelFulfillmentsData;

    /** @var array $channelProductsData */
    static $channelProductsData;

    /** @var array $channelMetaData */
    static $channelMetaData;

    /** @var array $channelOrderData */
    static $channelOrderData;

    /**
     * Set Up
     * @return void
     */
    function setUp(): void
    {
        self::$channelTypes = self::getChannelTypes();
    }

    /**
     * Test Sync Products
     *
     * This test case evaluates syncing of products for all custom connectors
     * implemented in the 'channels' directory.
     *
     * The following workflow is repeated for each connector type:
     *
     * 1. Data.
     * 2. Channel.
     * 3. Run.
     * 4. Verify.
     *
     * @return void
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    function testSyncProducts(): void
    {

        foreach (self::$channelTypes as $type) {

            self::loadTestData($type);                                                    // load test data from type.
            self::setChannelFactory($type);                                               // instantiate factory from type.

            /**
             * SCENARIO 1:
             */
            $scenarioProducts = vo\ChannelProduct::createArray(self::$channelProductsData);

            $channel = new vo\Channel(json_encode(self::$channelData, true));       // create channel object with config.
            self::$connector = self::$creator->createProducts();                          // create products from connector.

            $syncedProductData = self::$connector->sync(
                $channelProducts,
                $channel,
                []
            );

            /**
             * SCENARIO 2:
             */
            $scenarioProducts = vo\ChannelProduct::createArray(self::$channelProductsData);
            unset($scenarioProducts[1]['variants'][1]);

            $channel = new vo\Channel(json_encode(self::$channelData, true));       // create channel object with config.
            self::setChannelFactory($type);                                               // instantiate factory from type.
            self::loadTestData($type);                                                    // load test data from type.
            self::$connector = self::$creator->createProducts();                          // create products from connector.

            $syncedProductData = self::$connector->sync(
                $channelProducts,
                $channel,
                []
            );


            /**
             * SCENARIO 3:
             */


            /**
             * SCENARIO 4:
             */



        }
    }

            // sync all products
//            $request = new ChannelProductsSync(
//                [
//                    "meta"             => self::$channelMetaData,
//                    "channel_products" => self::$channelProductsData,
//                    "flag_map"         => []
//                ]
//            );
//            self::verifyProductSync($request, $response);

            // ------------------------------------------------

            // Delete variant
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

            // ------------------------------------------------

            // Remove all products
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

            // ------------------------------------------------

            // send empty payload
//            $request  = new ChannelProductsSync(
//                [
//                    "meta"             => self::$channelMetaData,
//                    "channel_products" => [],
//                    "flag_map"         => []
//                ]
//            );
//            $response = self::$channel->syncProducts($request);
//            self::verifyProductSync($request, $response);

            // ------------------------------------------------

//        }

//    }

    /**
     * Test Get Products
     *
     * This test case evaluates syncing products onto the channel from the
     * mock service data for each channel configured in stock2shop\dal\channels.
     *
     * @return void
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    function testGetProducts()
    {

        /**
         * 1. Iterate over self::channelTypes to cover all connectors
         * in the connector\dal\channels directory.
         */
        foreach (self::$channelTypes as $type) {

            /**
             * 2. Load test data using self::loadTestData($type).
             */
            self::loadTestData($type);
            self::setCreator($type);

            /**
             * 3. Create an instance of the products connector.
             */
            self::$connector = self::$creator->createProducts();

            /**
             * 4. Create new channel with the $channelMetaData.
             */
            self::$channel = new Channel(self::$channelMetaData);

            /**
             * 5. Sync all test data ($channelProductsData) on the $channel.
             */
            $response = self::$channel->sync(
                self::$channelProductsData,
                self::$channel,
                []
            );

            /**
             * 6. Check that the sync worked correctly by comparing the $channelProductsData with the $fetchedProducts.
             */
            $cnt             = 0;
            $token           = "";
            $limit           = count(self::$channelProductsData);
            $meta            = Meta::createArray(self::$channelMetaData);
            $fetchedProducts = self::$channel->get($token, count(self::$channelProductsData), $channel);

            /**
             * 7. Loop over the self::$channelProductData and check each product.
             */
            for ($i = 0; $i < count(self::$channelProductsData); $i++) {
                $fetchedProducts = self::$channel->get($token, 1, $channel);
                self::verifyGetProducts($token, 1, $fetchedProducts);
                $cnt += count($fetchedProducts);
                $token = $fetchedProducts[0]->token;
            }

            /**
             * 8. Finally, assert on the counts.
             */
            $this->assertEquals(count(self::$channelProductsData), $cnt);

        }
    }

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
     * @param ChannelProduct[] $fetchedProducts
     */
    function verifyGetProducts($token, $limit, $fetchedProducts)
    {

        // only return up to limit
        $this->assertLessThanOrEqual($limit, count($fetchedProducts));
        $currentToken = $token;

        foreach ($fetchedProducts as $product) {

            // return type valid
            $this->assertInstanceOf("stock2shop\\vo\\ChannelProduct", $product);

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
     * Verify Product Sync
     *
     * This method is used to verify whether the connector is working correctly
     * by comparing the requested product and product variant data with the
     * data received in response from the channel.
     *
     * Product sync is verified by:
     *
     * 1. Fetch products from the channel by use of getByCode().
     *
     * @param $request
     * @param $response
     */
    function verifyProductSync($request, $response)
    {
        // Check against existing products on channel by fetching them first
        $existingProducts = self::$connector->getByCode($request);

        $requestProductCnt  = 0;
        $requestVariantCnt  = 0;
        $existingVariantCnt = 0;

        // Calculate total products in channel.
        // Calculate total product variants in channel.
        foreach ($request->channel_products as $key => $product) {
            if (!$product->delete) {
                $requestProductCnt++;
                foreach ($product->variants as $variant) {
                    $requestVariantCnt++;
                }
            }
        }

        // Calculate total existing products in channel.
        foreach ($existingProducts->channel_products as $key => $product) {
            foreach ($product->variants as $variant) {
                $existingVariantCnt++;
            }
        }

        // Assert on total values.
        // Existing variants count must equal total request variants count.
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

        // Loop through the existing channel products structure and assert on
        // each product being successfully processed.
        foreach ($existingProducts->channel_products as $key => $existingProduct) {

            // Check each existing product in the channel.
            $product = $responseProductMap[$existingProduct->channel_product_code];

            // - success must be true.
            // - synced (timestamp) may not be empty.
            // - isValidSynced() must returned true.
            // - channel_product_code (s2s code) may not be empty.
            // - total variant count must be equal.
            $this->assertTrue($product->success, ' success set to true');
            $this->assertNotEmpty($product->synced, ' synced set');
            $this->assertTrue(ChannelProduct::isValidSynced($product->synced), ' success set to true');
            $this->assertNotEmpty($product->channel_product_code, ' channel_product_code set');
            $this->assertEquals(count($existingProduct->variants), (count($product->variants)));

            // Check existing product variants
            foreach ($existingProduct->variants as $existingVariant) {

                // Check each existing product variant item individually.
                $variant = $responseVariantMap[$existingVariant->channel_variant_code];

                // - success must be set to true.
                // - channel_variant_code may not be empty.
                $this->assertTrue($variant->success, ' success set to true');
                $this->assertNotEmpty($variant->channel_variant_code, ' channel_variant_code set');

            }

        }
    }

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
     * Get the test data from the test data directory and
     * convert JSON strings into PHP arrays for each test
     * data segment.
     *
     * @param string $type
     * @return void
     */
    function loadTestData(string $type): void
    {
        self::$channelFulfillmentsRaw = json_decode(file_get_contents(__DIR__ . '/data/syncChannelFulfillments.json'), true);
        self::$channelProductsRaw     = json_decode(file_get_contents(__DIR__ . '/data/syncChannelProducts.json'), true);
        self::$channelMetaRaw         = json_decode(file_get_contents(__DIR__ . '/data/channelMeta.json'), true);
        self::$channelOrderRaw        = json_decode(file_get_contents(__DIR__ . '/data/channels/' . $type . '/orderTransform.json'), true);
    }

    /**
     * Get Channel Types
     * Channel types are directories and classes found in /dal/channels/
     * @return array
     */
    function getChannelTypes(): array
    {
        $channels = [];

        $items = array_diff(scandir(
            __DIR__ . '/../../www/v1/stock2shop/dal/channels',
            SCANDIR_SORT_ASCENDING
            ), ['..', '.']);

        foreach ($items as $item) {
            if(is_dir($item)) {
                $channels[] = $item;
            }
        }

        return $channels;
    }

    /**
     * Set Channel Factory
     *
     * Initializes the channel Creator factory class.
     *
     * @param string $type
     */
    function setChannelFactory(string $type)
    {
        $creatorNameSpace = "stock2shop\\dal\\channels\\" . $type . "\\Creator";
        self::$creator    = new $creatorNameSpace();
    }

}