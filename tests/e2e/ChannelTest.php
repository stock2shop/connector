<?php

namespace tests\e2e;

use PHPUnit\Framework;
use tests\TestPrinter;
use stock2shop\dal;
use stock2shop\vo;
use stock2shop\helpers;
use stock2shop\exceptions\NotImplemented;
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

    /** @var array $channelData The raw data used to create a vo\Channel object. */
    public static $channelData;

    /** @var array $channelFlagMapData The raw data used to create an array of vo\Flag objects. */
    public static $channelFlagMapData;

    /** @var TestPrinter $printer The printer object used to output testing data. */
    public static $printer;

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
        self::$channelData             = $this->loadJSON('channel.json', $type);
        self::$channelFlagMapData      = $this->loadJSON('channelFlagMap.json', $type);
        self::$channelOrderData        = $this->loadJSON('orderTransform.json', $type);
        self::$channelProductsData     = $this->loadJSON('channelProducts.json', $type);
        self::$channelFulfillmentsData = $this->loadJSON('channelFulfillments.json', $type);
    }

    /**
     * Tear Down After Class
     *
     * This event hook is used to setup the test printer.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$printer = new TestPrinter();
    }

    /**
     * Checks if custom json file exists for channel otherwise loads default.
     *
     * @param string $filename
     * @param string $type
     * @return array
     */
    private function loadJSON(string $filename, string $type): array
    {
        $custom = __DIR__ . '/data/channels/' . $type . '/' . $filename;
        $path   = __DIR__ . '/data/' . $filename;
        if (file_exists($custom)) {
            $path = $custom;
        }
        return json_decode(file_get_contents($path), true);
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
     * @return void
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
     *
     * @return void
     * @throws UnprocessableEntity
     */
    public function testSyncProducts()
    {
        $channelTypes = self::getChannelTypes();

        // Loop through the channel types found in the dal/channels/directory.
        foreach ($channelTypes as $type) {

            // Load test data and set channel
            self::loadTestData($type);
            self::setFactory($type);

            // Get the Products connector object.
            $connector = self::$creator->createProducts();
            $this->assertInstanceOf("stock2shop\\dal\\channels\\" . $type . "\\Products", $connector);

            // Instantiate new channel object using the test channel meta data.
            $channel = new vo\Channel(self::$channelData);

            // Create flag map array.
            $flagMap = vo\Flag::createArray(self::$channelFlagMapData);

            // --------------------------------------------------------

            // Create all products on the channel from data on Stock2Shop.
            $request = vo\ChannelProduct::createArray(self::$channelProductsData);
            $response = $connector->sync($request, $channel, $flagMap);

            self::verifyProductSync($request, $response, $connector, $channel);
            self::$printer->sendProductsToPrinter($request, $response, 'TEST CASE 1 - Create All Products On Channel [' . $type . ']');

            // --------------------------------------------------------

            // Delete a product variant from Stock2Shop.

            self::$channelProductsData[0]["variants"][0]["delete"] = true;
            $request = vo\ChannelProduct::createArray(self::$channelProductsData);
            foreach($request as $idx => $product) {
                foreach($product->variants as $variant) {
                    $variant->delete = true;
                }
            }

            $response = $connector->sync($request, $channel, $flagMap);

            self::verifyProductSync($request, $response, $connector, $channel);
            self::$printer->sendProductsToPrinter($request, $response, 'TEST CASE 2 - Delete A Variant [' . $type . ']');

            // --------------------------------------------------------

            // Remove all products from Stock2Shop.

            foreach(self::$channelProductsData as $key => $product) {
                self::$channelProductsData[$key]["delete"] = true;
            }

            $request = vo\ChannelProduct::createArray(self::$channelProductsData);
            $response = $connector->sync($request, $channel, $flagMap);


            self::verifyProductSync($request, $response, $connector, $channel);
            self::$printer->sendProductsToPrinter($request, $response, 'TEST CASE 3 - Remove All Products [' . $type . ']');

            // --------------------------------------------------------

            // Synchronize an empty payload of data from Stock2Shop.

            $request = vo\ChannelProduct::createArray([]);
            $response = $connector->sync($request, $channel, $flagMap);

            self::verifyProductSync($request, $response, $connector, $channel);
            self::$printer->sendProductsToPrinter($request, $response, 'TEST CASE 4 - Sync Empty Payload [' . $type . ']');
            self::$printer->print();

        }

    }

    /**
     * Verify Product Sync
     *
     * This method verifies whether the synchronization of products was
     * successful using the custom connectors in the stock2shop/dal/channels.
     * Please note that in the context of this test case, 'existing'
     * ($existingProducts) refers to data which has been persisted on a
     * Stock2Shop channel.
     *
     * The workflow includes:
     *
     * 1. Get the existing products on the channel by calling getByCode().
     *
     * @param vo\ChannelProduct[] $request
     * @param vo\ChannelProduct[] $response
     * @param dal\channel\Products $connector
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[] $response
     */
    public function verifyProductSync(array $request, array $response, dal\channel\Products $connector, vo\Channel $channel)
    {
        $existingProducts = $connector->getByCode($request, $channel);

        // Product, image and variant counters for existing and request products.
        $requestProductCnt  = 0;
        $existingProductCnt = 0;
        $requestVariantCnt  = 0;
        $existingVariantCnt = 0;
        $requestImageCnt    = 0;
        $existingImageCnt   = 0;

        // Loop through the request products and add to productCnt and variantCnt.
        foreach ($request as $key => $product) {
            if(!$product->delete) {
                $requestProductCnt++;
                foreach ($product->variants as $variant) {
                    if(!$variant->delete) {
                        $requestVariantCnt++;
                    }
                }
                foreach($product->images as $image) {
                    if(!$image->delete) {
                        $requestImageCnt++;
                    }
                }
            }
        }

        // Loop through existing products.
        foreach ($existingProducts as $key => $product) {
            $existingProductCnt++;
            foreach ($product->variants as $variant) {
                $existingVariantCnt++;
            }
            foreach($product->images as $image) {
                $existingImageCnt++;
            }
        }

        // -----------------------------------------

        // Assert on totals.
        $this->assertEquals($requestProductCnt, $existingProductCnt);
        $this->assertEquals($requestVariantCnt, $existingVariantCnt);
        $this->assertEquals($requestImageCnt, $existingImageCnt);
//
        // Start building product, variant and image maps.
        $responseProductMap = [];
        $responseVariantMap = [];
        $responseImageMap = [];

        // -----------------------------------------

        foreach ($response as $product) {
            $responseProductMap[$product->channel_product_code] = $product;
            foreach ($product->variants as $variant) {
                $responseVariantMap[$variant->channel_variant_code] = $variant;
            }
            foreach ($product->images as $image) {
                $responseImageMap[$image->channel_image_code] = $image;
            }
        }

        // -----------------------------------------

        foreach ($existingProducts as $existingProduct) {
            $product = $responseProductMap[$existingProduct->channel_product_code];

            // Check product.
            $this->assertTrue($product instanceof vo\ChannelProduct);
//            $this->assertTrue($product->hasSyncedToChannel());

            // -----------------------------------------

            // Check product variant count.
            $this->assertEquals(count($existingProduct->variants), (count($product->variants)));

            // Check existing product variants.
            foreach ($existingProduct->variants as $existingVariant) {
                $variant = $responseVariantMap[$existingVariant->channel_variant_code];
                $this->assertTrue($variant instanceof vo\ChannelVariant);
//                $this->assertTrue($variant->hasSyncedToChannel());
                $this->assertNotEmpty($variant->sku);
            }

            // Check product image count.
            $this->assertEquals(count($existingProduct->images), (count($product->images)));

            // Check product images.
            foreach ($existingProduct->images as $existingImage) {
                $image = $responseImageMap[$existingImage->channel_image_code];
                $this->assertTrue($image instanceof vo\ChannelImage);
                $this->assertTrue($image->hasSyncedToChannel());
            }
        }
        return $response;
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
        $channelTypes = self::getChannelTypes();
        foreach ($channelTypes as $type) {

            if ($type === 'example') continue;

            // Load test data.
            self::loadTestData($type);

            // Set factory.
            self::setFactory($type);

            // Instantiate the Creator factory object.
            $creator = self::$creator;
            $connector = $creator->createProducts();

            // Instantiate new channel object using the test channel meta data.
            $flagMap = vo\Flag::createArray(self::$channelFlagMapData);
            $channel = new vo\Channel(self::$channelData);

            // --------------------------------------------------------

            // Create all products on the channel.

            $request = vo\ChannelProduct::createArray(self::$channelProductsData);
            $this->assertCount(2, $request);
            $connector->sync($request, $channel, $flagMap);

            // --------------------------------------------------------

            // Provide an empty token.
            // We are expecting to receive all the products in the response from this function call.

            $token = "";
            $limit = count($request);

            // --------------------------------------------------------

            // Get Synced Products.

            // After the sync() we need to test that the products we synced are in fact
            // on the channel now. A try-catch is used to handle connector types which
            // do not implement the `get()` method.

            try {
                $channelProductsGetArray = $connector->get($token, $limit, $channel);
            } catch (NotImplemented $e) {
                self::$printer->addHeading('[' . $type . ']' . ' Get - Not Implemented.');
                self::$printer->print();
            }

            self::$printer->addHeading('[' . $type . ']' . ' Get - Return All Products');
            self::verifyGetProducts($token, $limit, $channelProductsGetArray);

            // --------------------------------------------------------

            $cnt = 0;
            $limit = 1;

            // Iterate through the number of products in the $request array.
            // Check each product by getting it from the channel and verifying
            // it using verifyGetProducts.

            self::$printer->addHeading('[' . $type . ']' . ' Get - Return One Product');
            for ($i = 0; $i < count($request); $i++) {
                $fetchedProductGet = $connector->get('', $limit, $channel);
                self::verifyGetProducts('', $limit, $fetchedProductGet);
                $cnt++;
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
     * 4. Check that each variant has a sku and channel_variant_code set.
     * 5. Check that each image has its channel_image_code set.
     *
     * @param string $token
     * @param int $limit
     * @param ChannelProduct[] $fetchedProducts
     * @return void
     */
    public function verifyGetProducts(string $token, int $limit, array $fetchedProducts)
    {
        $this->assertLessThanOrEqual(count($fetchedProducts), $limit);

        /** @var vo\ChannelProduct $product */
        foreach ($fetchedProducts as $product) {
            $this->assertInstanceOf("stock2shop\\vo\\ChannelProduct", $product);
            $this->assertNotEmpty($product->channel_product_code);
            $this->assertGreaterThan($token, $product->channel_product_code);
            foreach ($product->variants as $variant) {
                $this->assertNotEmpty($variant->channel_variant_code);
                $this->assertNotEmpty($variant->sku);
            }
            foreach ($product->images as $image) {
                $this->assertNotEmpty($image->channel_image_code);
            }
        }
        self::$printer->sendProductsToPrinter($fetchedProducts, [], "Verify Get Products");
    }

    /**
     * Test Get Orders
     * @return void
     */
    // TODO: Complete when interface is ready.
//    public function _testGetOrders()
//    {
//        foreach ($channelTypes as $type) {
//
//            // Load test data.
//            self::loadTestData($type);
//
//            // Configure the connector factory.
//            self::setFactory($type);
//
//            // Get orders connector.
//            $connector = self::$creator->createOrders();
//
//            // Create channel object.
//            $channel = new vo\Channel(self::$channelData);
//
//            // Use connector to get the orders.
//            $fetchedOrders = $connector->get("", 2, $channel);
//            $this->assertEquals(2, count($fetchedOrders));
//
//            // Iterate over fetched orders.
//            foreach ($fetchedOrders as $order) {
//
//                // Check each transform.
//                $this->verifyTransformOrder($order);
//
//            }
//
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
//    public function _testTransformOrder()
//    {
//        foreach ($channelTypes as $type) {
//
//            // Load test data
//            self::loadTestData($type);
//
//            // Set up the channel.
//            self::setFactory($type);
//
//            // Set up an object of the connector we are testing.
//            $connector  = self::$creator->createOrders();
//
//            // Prepare the data we are going to be passing to the
//            // transform() method of the Orders connector implementation.
//
//            // Create channel object.
//            $channel = new vo\Channel(self::$channelData);
//
//            // We are creating an array of vo\Order objects
//            // and an array of vo\Meta objects and passing it
//            // to the connector implementation.
//            $channelOrder = $connector->transform(
//                self::$channelOrderData,
//                $channel
//            );
//
//            // Call the verify method to evaluate the transformation.
//            $this->verifyTransformOrder($channelOrder);
//        }
//    }

    /**
     * Test Get Orders By Code
     *
     * This test case evaluates the get() method from the dal\channel\Orders interface.
     * It iterates over the available connectors in the 'dal/channels' directory and
     * evaluates the functionality individually.
     *
     * The connector factory is used to generate the corresponding orders connector using
     * createOrders(). The connector is then used to get the orders by code using
     * getByCode().
     *
     * Each order is passed to the verifyTransformOrder() method.
     *
     * @return void
     * @throws UnprocessableEntity
     */
    // TODO: Complete when interface is ready.
//    public function _testGetOrdersByCode()
//    {
//        foreach ($channelTypes as $type) {
//
//            // Load test data.
//            self::loadTestData($type);
//
//            // Setup factory.
//            self::setFactory($type);
//
//            // Connector.
//            $connector = self::$creator->createOrders();
//
//            // Create channel object.
//            $channel = new vo\Channel(self::$channelData);
//
//            // ----------------------------------------------
//
//            $channelOrders = [];
//
//            $channelOrders[] = new vo\ChannelOrder([
//                "channel" => $channel,
//                "system_order" => new vo\ChannelOrderOrder([
//                    "channel_order_code" => self::$channelOrderData["order_number"]
//                ])
//            ]);
//
//            // ----------------------------------------------
//
//            $existingOrders = $connector->getByCode($channelOrders, $channel);
//
//            $this->assertNotNull($existingOrders);
//            $this->assertCount(1, $existingOrders);
//
//            foreach ($existingOrders as $order) {
//                $this->verifyTransformOrder($order);
//            }
//
//            // ----------------------------------------------
//
//            // Iterate over fetched orders.
//            foreach ($existingOrders as $order) {
//                $this->verifyTransformOrder($order);
//            }
//
//        }
//    }

    /**
     * Verify Transform Order
     *
     * Verifies the order transformation is valid.
     * Criteria for a valid Stock2Shop Channel Orders is:
     *
     * - Must be of vo\ChannelOrder type.
     * - Must have 'channel_order_code' set.
     *
     * @param vo\ChannelOrder $channelOrder
     * @return void
     */
    // TODO: Complete when interface is ready.
//    public function verifyTransformOrder(vo\ChannelOrder $channelOrder)
//    {
//        $this->assertInstanceOf("stock2shop\\vo\\ChannelOrder", $channelOrder);
//        $this->assertNotEmpty($channelOrder->system_order->channel_order_code);
//    }

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
//        foreach ($channelTypes as $type) {
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

}