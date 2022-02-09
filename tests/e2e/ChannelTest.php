<?php

namespace tests\e2e;

use PHPUnit\Framework;
use stock2shop\vo;
use stock2shop\dal;
use stock2shop\exceptions\UnprocessableEntity;

/**
 * This "end to end" test runs through all channel types.
 * @package tests\e2e
 */
final class ChannelTest extends Framework\TestCase
{
    /** @var dal\channel\Creator The object of the concrete class which extends the dal\channel\Creator factory abstract class. */
    public static $creator;

    /** @var vo\Channel $channel The channel object being tested. */
    public static $channel;

    /** @var string[] $channelTypes The channel types which will be tested. (dal/channels/[type]) */
    public static $channelTypes;

    /** @var array $channelFulfillmentsData The raw testing data for fulfillments. */
    public static $channelFulfillmentsData;

    /** @var array $channelProductsData The raw testing data for products data. */
    public static $channelProductsData;

    /** @var array $channelMetaData The raw testing data for channel meta. */
    public static $channelMetaData;

    /** @var array $channelOrderData The raw testing data for orders. */
    public static $channelOrderData;

    /** @var string $currentChannelType The active channel type being tested. */
    public static $currentChannelType;

    /**
     * Set Up
     *
     * Executes the code which sets up the test before running the test
     * cases. The integrations are loaded into $channelTypes by
     * getChannelTypes().
     */
    public function setUp()
    {
        self::$channelTypes = self::getChannelTypes();
    }

    /**
     * Load Test Data
     *
     * This function gets the test data from the JSON files in the /data directory.
     *
     * @param string $type
     * @return void
     */
    public function loadTestData(string $type)
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
    public function getChannelTypes(): array
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
    public function setFactory($type)
    {
        // Instantiate factory creator object.
        $creatorNameSpace = "stock2shop\\dal\\channels\\" . $type . "\\Creator";
        self::$creator = new $creatorNameSpace();

        // Evaluate whether the object is a valid concrete class
        // implementation of the Creator class.
        $this->assertInstanceOf("stock2shop\\dal\\channel\\Creator", self::$creator);
    }

    /**
     * Test Sync Products
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
     * 4. Sending an empty payload of products onto the channel for processing.
     *
     * The workflow of this test runs through four scenarios - the results of which are
     * deconstructed and asserted on in a separate method called verifyTestSync().
     * You may expect the outcomes to indicate issues, bugs and logic problems in specific
     * areas of code in the integration you are working on.
     */
    public function testSyncProducts()
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
            $channel = new vo\Channel([
                "meta" => self::$channelMetaData
            ]);

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
            $request[1]->variants[1]->delete = true;

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
     * This method verifies whether the synchronization of products on a channel was
     * successful using the custom connector.
     *
     * @param array $request
     * @param array $response
     * @param dal\channel\Products $connector
     * @param $channel
     * @return void
     */
    public function verifyProductSync(array $request, array $response, dal\channel\Products $connector, $channel)
    {

        // Check against existing products on channel by fetching them first
        $existingProducts = $connector->getByCode($request, $channel);
        $this->assertNotNull($existingProducts);

        // Counter variables.
        $requestProductCnt = 0;
        $requestVariantCnt = 0;
        $existingProductsCnt = 0;
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

        // Assert on the product and product variant counts.
        $this->assertEquals(count($requestProductCnt), count($existingProductsCnt), ' sync count');
        $this->assertEquals(count($requestVariantCnt), count($existingVariantCnt), ' sync count');

        // Instantiate the responseProductMap and the responseVariantMap.
        $responseProductMap = [];
        $responseVariantMap = [];

        // Here we are looping through the response products and build a map
        // of key value pairs in $responseProductMap. The same is done for all
        // the product variants in $responseVariantMap.
        foreach ($response as $product) {
            $responseProductMap[$product->channel_product_code] = $product;
            foreach ($product->variants as $variant) {
                $responseVariantMap[$variant->channel_variant_code] = $variant;
            }
        }

        // In this section of code we are looping through the existing products
        // and destructuring each iteration to get the $key and $existingProduct
        // (the actual vo\ChannelProduct item).
        foreach ($existingProducts as $key => $existingProduct) {

            /** @var vo\ChannelProduct $product */
            $product = $responseProductMap[$existingProduct->channel_product_code];

            $this->assertTrue($product->success, ' success set to true');
            $this->assertNotEmpty($product->synced, ' synced set');
            $this->assertTrue($product->valid(), ' success set to true');   // updated since VO changed.
            $this->assertNotEmpty($product->channel_product_code, ' channel_product_code set');

            // Existing and product variants count.
            $tempExistingProductCount = count($existingProduct->variants);
            $tempProductCount = count($product->variants);
            $this->assertEquals(
                $tempExistingProductCount,
                $tempProductCount,
                " The number of existing product variants (" . $tempExistingProductCount . ") does not match the number of variants for this product. (" . $tempProductCount . ")"
            );

            foreach ($existingProduct->variants as $existingVariant) {

                $this->assertArrayHasKey($existingVariant->channel_variant_code, $responseVariantMap, " product variant not found in existing variant map.");
                $variant = $responseVariantMap[$existingVariant->channel_variant_code];
                $this->assertTrue($variant->success, ' success set to true');
                $this->assertNotEmpty($variant->channel_variant_code, ' channel_variant_code set');

            }

        }

    }

    /**
     * Test Get Products
     *
     * The method evaluates the get() functionality of the Products connector for
     * all the connector implementations in the dal/channels directory.
     *
     * @return void
     * @throws UnprocessableEntity
     */
    public function testGetProducts()
    {
        // Loop through the channel types found in the
        // dal/channels/ directory.
        foreach (self::$channelTypes as $type) {

            // Load test data and set channel
            self::loadTestData($type);
            self::setFactory($type);

            // Instantiate the Creator factory object.
            $creator = self::$creator;

            // Get the products connector object.
            $connector = $creator->createProducts();

            // Instantiate new channel object using the test channel meta data.
            $channel = new vo\Channel([
                "meta" => self::$channelMetaData
            ]);

            // Create empty flag map.
            $flagMap = [];

            // Prepare the request by creating an array of ChannelProducts.
            $request = vo\ChannelProduct::createArray(self::$channelProductsData);
            $this->assertNotNull($request);

            // --------------------------------------------------------

            // Create all products on the channel.
            // Run the sync on the channel.
            $response = $connector->sync($request, $channel, $flagMap);

            // --------------------------------------------------------

            // Provide an empty token.
            // We are expecting to receive all the products in the response from this function call.
            $token = "";
            $limit = count(self::$channelProductsData);
            $meta = vo\Meta::createArray(self::$channelMetaData);
            $fetchedProducts = $connector->get($token, $limit, $channel);

            // We are expecting the connector->get() method to return all the products on the channel.
            self::verifyGetProducts($token, $limit, $fetchedProducts);

            // --------------------------------------------------------

            // Get products using paging (one at a time).
            $cnt = 0;           // zero counter.
            $limit = 1;         // set limit to 1.

            for ($i = 0; $i < count($request); $i++) {
                $fetchedProducts = $connector->get($token, $limit, $channel);
                self::verifyGetProducts($token, $limit, $fetchedProducts);
                $cnt += count($fetchedProducts);
                $token = $fetchedProducts[0]->token;
            }

            // Assert on the product count.
            $this->assertEquals(count($request), $cnt);

        }
    }

    /**
     * Verify Get Products
     *
     * This is a helper test method which is used to evaluate whether the get() method of
     * the dal\channel\Products has been implemented correctly in your connector integration.
     * A successful get() action on a dal\channel\Products will return items which match
     * the following criteria:
     *
     * 1. Each product will be a hydrated vo\ChannelProductGet object.
     * 2. Each product will have a token value smaller than the cursor token.
     * 3. vo\ChannelProductGet items must be sorted by the token value.
     * 4. Check that the Variant has a sku.
     * 5. Check that the Variant has a channel_variant_code.
     *
     * @param string $token
     * @param int $limit
     * @param vo\ChannelProductGet[] $fetchedProducts
     * @return void
     */
    public function verifyGetProducts(string $token, int $limit, array $fetchedProducts)
    {
        // Assert on the limit.
        $this->assertLessThanOrEqual($limit, count($fetchedProducts));
        $currentToken = $token;

        // Loop through the fetched products and check their values.
        foreach ($fetchedProducts as $product) {

            // Each product must be a ChannelProductGet object.
            $this->assertInstanceOf("stock2shop\\vo\\ChannelProductGet", $product);

            // The product token must not be greater than cursor token.
            $this->assertGreaterThan($token, $product->token);

            // The result set must be sorted by the token value.
            $this->assertGreaterThan($currentToken, $product->token);
            $currentToken = $product->token;

            // Check that the token property is set.
            $this->assertNotEmpty($product->token);

            // Check that the channel_product_code property is set.
            $this->assertNotEmpty($product->channel_product_code);

            // Check variants.
            foreach ($product->variants as $variant) {

                // Check that the channel_variant_code has been set for the product variant.
                $this->assertNotEmpty($variant->channel_variant_code);

                // Check that the product variant sku property has been set.
                $this->assertNotEmpty($variant->sku);
            }
        }
    }

    /**
     * Test Sync Fulfillments
     *
     * This method synchronizes the test data for fulfillments onto a channel.
     * verifyFulfillmentsSync() method is called after each test scenario to
     * evaluate whether the sync was successful.
     *
     * Run this test to confirm whether your Fulfillments connector code is
     * working correctly.
     *
     * After running the sync and if the test did not produce any errors, you
     * should see the data in your connector directory fill up with JSON
     * fulfillment records.
     *
     * The testing workflow is:
     *
     * 1. Iterate through the channel types.
     * 2. Load test data for channel type.
     * 3. Create new channel using test metadata.
     * 4. Create connector from channel type and call sync().
     * 5. Pass response and expected result to verifyFulfillmentsSync().
     *
     * @return void
     */
    // TODO: Complete when interface is ready.
//    public function testSyncFulfillments()
//    {
//        foreach (self::$channelTypes as $type) {
//
//            // load test data and set channel
//            self::loadTestData($type);
//            self::setFactory($type);
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
     * Verify Fulfillment Sync
     *
     * This method evaluates whether the request and response of a fulfillment
     * synchronisation are valid.
     *
     * A successful fulfillment sync passes the following criteria:
     *
     * 1. The request and response are both ChannelFulfillmentsSync objects.
     * 2. The number of objects in the channel_fulfillments property of both request and response are equal.
     * 3. The items in the channel_fulfillments property are objects of the ChannelFulfillment class.
     * 4. Each ChannelFulfillment object has its channel_fulfillment_code set.
     * 5. Each ChannelFulfillment object has its channel_synced property set.
     * 6. A call to the getByCode() method to get the current fulfillments returns the same amount of
     *    ChannelFulfillment objects as there are in the $request structure.
     *
     * @param ChannelFulfillmentsSync $request
     * @param ChannelFulfillmentsSync $response
     * @param dal\channel\Fulfillments $connector
     * @param vo\Channel $channel
     * @return void
     */
    // TODO: Complete when interface is ready.
//    public function verifyFulfillmentSync(ChannelFulfillmentsSync $request, ChannelFulfillmentsSync $response, dal\channel\Fulfillments $connector, vo\Channel $channel)
//    {
//        $codes = [];
//
//        // Assert whether the response is a ChannelFulfillmentsSync object.
//        $this->assertInstanceOf("stock2shop\\vo\\ChannelFulfillmentsSync", $response);
//
//        // Assert whether the request is a ChannelFulfillmentsSync object.
//        $this->assertSameSize($request->channel_fulfillments, $response->channel_fulfillments);
//
//        // Loop through channel_fulfillments property items and assert on individual ChannelFulfillment objects.
//        foreach ($response->channel_fulfillments as $f) {
//
//            $this->assertInstanceOf("stock2shop\\vo\\ChannelFulfillment", $f);
//            $this->assertNotEmpty($f->channel_fulfillment_code);
//            $this->assertNotEmpty($f->channel_synced);
//            $this->assertTrue(ChannelFulfillment::isValidChannelSynced($f->channel_synced));
//
//            $codes[$f->channel_fulfillment_code] = $f->channel_fulfillment_code;
//        }
//
//        // Get the current fulfillments from the current connector implementation.
//        $currentFulfillments = $connector->getByCode($response);
//
//        // Check whether the number of ChannelFulfillments on the channel match the number of Fulfillments
//        // from the current connector implementation.
//        $this->assertEquals(count($request->channel_fulfillments), count($currentFulfillments));
//
//        foreach ($currentFulfillments as $f) {
//            $this->assertInstanceOf("stock2shop\\vo\\ChannelFulfillment", $f);
//            $this->assertArrayHasKey($f->channel_fulfillment_code, $codes);
//        }
//
//    }

    /**
     * Test Get Orders
     * @return void
     */
    // TODO: Complete when interface is ready.
//    public function testGetOrders()
//    {
//        foreach (self::$channelTypes as $type) {
//
//            // load test data and set channel
//            self::loadTestData($type);
//
//            // Get orders (return 2)
//            $fetchedOrders = self::$channel->getOrders("", 2, MetaItem::createArray(self::$channelMetaData));
//            $this->assertEquals(2, count($fetchedOrders));
//            foreach ($fetchedOrders as $order) {
//                $this->verifyTransformOrder($order);
//            }
//        }
//    }

    /**
     * Test Get Orders By Code
     *
     * This test case evaluates the get() method from the dal\channel\Orders interface
     * which is implemented in your connector.
     *
     * @return void
     */
    // TODO: Complete when interface is ready.
//    public function testGetOrdersByCode()
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

    /**
     * Test Transform Order
     *
     * This method transforms an order of a specified connector type.
     * It loops through the channel types configured in this e2e test
     * and calls the corresponding method from the connector object.
     *
     * @return void
     */
    // TODO: Complete when interface is ready.
//    public function testTransformOrder()
//    {
//        foreach (self::$channelTypes as $type) {
//
//            // load test data and set channel
//            self::loadTestData($type);
//            self::setChannel($type);
//
//            // Call the method to transform the order.
//            // We are creating an array of vo\Order objects
//            // and an array of vo\Meta objects and passing it
//            // to the connector implementation.
//            $channelOrder = self::$channel->transformOrder(
//                vo\Order::createArray(self::$channelOrderData),
//                vo\Meta::createArray(self::$channelMetaData)
//            );
//
//            // Call the verify method to evaluate the transformation.
//            $this->verifyTransformOrder($channelOrder);
//        }
//    }


    /**
     * Verify Transform Order
     *
     * Verifies the order transformation.
     *
     * @param $channelOrder
     */
    // TODO: Complete when interface is ready.
//    public function verifyTransformOrder($channelOrder)
//    {
//        $this->assertInstanceOf("stock2shop\\vo\\ChannelOrder", $channelOrder);
//        $this->assertNotEmpty($channelOrder->channel_order_code);
//    }

}