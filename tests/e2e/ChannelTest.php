<?php

namespace tests\e2e;

use phpDocumentor\Reflection\Types\Void_;
use PHPUnit\Framework;

use stock2shop\dal\channels;
use stock2shop\vo\Meta;
use stock2shop\vo\Channel;


/**
 * This "end to end" test runs through all channel types.
 * @package tests\e2e
 */
final class ChannelTest extends Framework\TestCase
{

    /**
     * FACTORY
     */
    static $creator;

    /**
     * CHANNEL
     */
    static $channel;

    /**
     * VARIABLES
     */
    static $channelTypes;
    static $channelFulfillmentsData;
    static $channelProductsData;
    static $channelMetaData;
    static $channelOrderData;

    /**
     * CHANNEL TYPES
     */
    const CHANNEL_TYPE_PRODUCTS         = "createProducts";
    const CHANNEL_TYPE_FULFILLMENTS     = "createFulfillments";
    const CHANNEL_TYPE_ORDERS           = "createOrders";

    /**
     * SETUP
     */
    function setUp(): void
    {
        self::$channelTypes = self::getChannelTypes();
    }

    /**
     * Test Sync Products
     *
     * This test case evaluates syncing of products in implementation the
     * implementation. The following test workflow is repeated for each
     * channel type configured in this test class.
     *
     * 1. Data.
     * 2. Channel.
     * 3. Run.
     * 4. Verify.
     *
     * @return void
     */
    function testSyncProducts()
    {
        foreach (self::$channelTypes as $type) {

            /**
             * 1. Set channel factory creator.
             */
            self::setChannelFactory($type);

            /**
             * 2. Load test data for channel.
             */
            self::loadTestData($type);

            /**
             * 3. Set channel by type name.
             */
            $channelTypeName = "createProducts";
            $channel = self::$creator->$channelTypeName;

            /**
             * 4. Call sync on the channel.
             */
            $channel->sync(self::$channelProductsData, self::$channel, []);

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
            self::setChannelCreator($type);

            /**
             * 3. Set the custom factory creator.
             */
            self::$channel = self::$creator->createProducts();

            /**
             * 4. Create new channel with the $channelMetaData.
             */
            $channel = new Channel(self::$channelMetaData);

            /**
             * 5. Sync all test data ($channelProductsData) on the $channel.
             */
            $response = self::$channel->sync(
                self::$channelProductsData,
                $channel,
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
     * @param $request
     * @param $response
     */
    function verifyProductSync($request, $response)
    {
        // Check against existing products on channel by fetching them first
        $existingProducts   = self::$channel->getByCode($request);
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
     * @param $type
     * @return void
     */
    function loadTestData($type)
    {
        $channelFulfillmentsJSON       = file_get_contents(__DIR__ . '/data/syncChannelFulfillments.json');
        $channelProductsJSON           = file_get_contents(__DIR__ . '/data/syncChannelProducts.json');
        $channelMetaJSON               = file_get_contents(__DIR__ . '/data/channelMeta.json');
        $channelOrderJSON              = file_get_contents(__DIR__ . '/data/channels/' . $type . '/orderTransform.json');

        self::$channelFulfillmentsData = json_decode($channelFulfillmentsJSON, true);
        self::$channelProductsData     = self::loadValueObjectCollection(json_decode($channelProductsJSON, true), "stock2shop\\vo\\ChannelProduct");
        self::$channelMetaData         = self::loadValueObjectCollection(json_decode($channelMetaJSON, true), "stock2shop\\vo\\Meta");
        self::$channelMetaData         = self::loadValueObjectCollection(json_decode($channelMetaJSON, true), "stock2shop\\vo\\Meta");
//        self::$channelMetaData         = self::loadValueObjectCollection(json_decode($channelMetaJSON, true), "stock2shop\\vo\\");
//        self::$channelMetaData         = self::loadValueObjectCollection(json_decode($channelOrderJSON, true), "stock2shop\\vo\\ChannelOrder");
//        self::$channelOrderData        = json_decode(, true);
    }

    /**
     * Load Value Object Collection
     *
     * This is a helper method to load the raw array data into value objects
     * and return them in an array. (not a 'typed collection').
     *
     * @param array $data
     * @param string $valueObjectClassName
     * @return void
     */
    function loadValueObjectCollection(array $data, string $valueObjectClassName) {
        $returnArray = [];
        if(!empty($data)) {
            foreach($data as $item) {
                $returnArray[] = new $valueObjectClassName($item);
            }
        }
        return $returnArray;
    }

    /**
     * Channel types are directories and classes found in /dal/channels/
     * @return array
     */
    function getChannelTypes(): array
    {
        $channels = [];
        $items    = array_diff(scandir(
            __DIR__ . '/../../www/v1/stock2shop/dal/channels',
            SCANDIR_SORT_ASCENDING
        ), array('..', '.', 'README.md'));
        foreach ($items as $item) {
            $channels[] = $item;
        }

        return $channels;
    }

    /**
     * Set Channel
     *
     * Sets the channel based on the string type passed to
     * this function.
     *
     * @param string $type
     */
    function setChannelCreator(string $type)
    {
        $creatorNameSpace = "stock2shop\\dal\\channels\\" . $type . "\\Creator";
        self::$creator    = new $creatorNameSpace();
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