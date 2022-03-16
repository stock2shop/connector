<?php

namespace tests\e2e;

use PHPUnit\Framework;
use stock2shop\dal;
use stock2shop\vo;
use tests\TestPrinter;

/**
 * This "end to end" test runs through all channel types.
 * @package tests\e2e
 */
final class ChannelTest extends Framework\TestCase
{
    const IGNORE_CHANNEL = 'boilerplate';

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
        self::$channelData = $this->loadJSON('channel.json', $type);
        self::$channelFlagMapData = $this->loadJSON('channelFlagMap.json', $type);
        self::$channelOrderData = $this->loadJSON('orderTransform.json', $type);
        self::$channelProductsData = $this->loadJSON('channelProducts.json', $type);
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
        $path = __DIR__ . '/data/' . $filename;
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
        $channelsFolderPath = '/../../www/v1/stock2shop/dal/channels';
        $channels = [];
        // Check if the channel name override is set.
        $channelName = getenv('S2S_CHANNEL_NAME');
        if ($channelName) {
            return [$channelName];
        }
        $items = array_diff(scandir(
            __DIR__ . $channelsFolderPath,
            SCANDIR_SORT_ASCENDING
        ), array('..', '.', self::IGNORE_CHANNEL));
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
     * This test is a full end-to-end.
     * It syncs products to a channel using Products->sync().
     * To verify the sync is correct it uses Products->getByCodes() to confirm the products
     * are found on the channel.
     *
     * If the environment var S2S_CHANNEL_NAME is set, it will only run the end-to-end test
     * for one channel.
     *
     * The goal of sync() is to synchronise the product data onto a Stock2Shop
     * channel. Synchronisation in this context may refer to:
     *
     * @return void
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
            self::verifyProductSync($request, $response, $connector, $channel, 'TEST CASE 1 - Create All Products On Channel [' . $type . ']');

            // --------------------------------------------------------
            // Delete a single variant from a product.
            // The second product in the test data has two variants
            self::$channelProductsData[1]['variants'][0]['delete'] = true;
            $request = vo\ChannelProduct::createArray(self::$channelProductsData);
            $response = $connector->sync($request, $channel, $flagMap);
            self::verifyProductSync($request, $response, $connector, $channel, 'TEST CASE 2 - Delete A Variant [' . $type . ']');

            // --------------------------------------------------------
            // Delete a single image from a product.
            // The second product in the test data has two images
            self::$channelProductsData[1]['images'][0]['delete'] = true;
            $request = vo\ChannelProduct::createArray(self::$channelProductsData);
            $response = $connector->sync($request, $channel, $flagMap);
            self::verifyProductSync($request, $response, $connector, $channel, 'TEST CASE 3 - Delete A Image [' . $type . ']');

            // --------------------------------------------------------
            // Delete all products from Channel.
            foreach (self::$channelProductsData as $key => $product) {
                self::$channelProductsData[$key]['delete'] = true;
            }
            $request = vo\ChannelProduct::createArray(self::$channelProductsData);
            $response = $connector->sync($request, $channel, $flagMap);
            self::verifyProductSync($request, $response, $connector, $channel, 'TEST CASE 4 - Remove All Products [' . $type . ']');

            // --------------------------------------------------------
            // Synchronize an empty payload of data from Stock2Shop.
            $request = vo\ChannelProduct::createArray([]);
            $response = $connector->sync($request, $channel, $flagMap);
            self::verifyProductSync($request, $response, $connector, $channel, 'TEST CASE 5 - Sync Empty Payload [' . $type . ']');

            // print results
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
     * channel.
     *
     * @param vo\ChannelProduct[] $request
     * @param vo\ChannelProduct[] $response
     * @param dal\channel\Products $connector
     * @param vo\Channel $channel
     * @param string $name
     * @return vo\ChannelProduct[] $response
     */
    public function verifyProductSync(array $request, array $response, dal\channel\Products $connector, vo\Channel $channel, string $name)
    {
        // Get existing products off the channel.
        $existingProducts = $connector->getByCode($request, $channel);
        self::$printer->sendProductsToPrinter($request, $response, $existingProducts, $name);

        // -----------------------------------------

        // Product, image and variant counters for existing and request products.
        $requestProductCnt = 0;
        $existingProductCnt = 0;
        $requestVariantCnt = 0;
        $existingVariantCnt = 0;
        $requestImageCnt = 0;
        $existingImageCnt = 0;

        // Loop through the request products and add to productCnt and variantCnt.
        foreach ($request as $product) {
            if (!$product->delete) {
                $requestProductCnt++;
                foreach ($product->variants as $variant) {
                    if (!$variant->delete) {
                        $requestVariantCnt++;
                    }
                }
                foreach ($product->images as $image) {
                    if (!$image->delete) {
                        $requestImageCnt++;
                    }
                }
            }
        }

        // -----------------------------------------

        // Loop through existing products.
        // These are the products returned by interface method Products->getByCode()
        foreach ($existingProducts as $product) {
            $existingProductCnt++;
            foreach ($product->variants as $variant) {
                $existingVariantCnt++;
            }
            foreach ($product->images as $image) {
                $existingImageCnt++;
            }
        }

        // Assert on totals.
        $this->assertEquals($requestProductCnt, $existingProductCnt);
        $this->assertEquals($requestVariantCnt, $existingVariantCnt);
        $this->assertEquals($requestImageCnt, $existingImageCnt);

        // -----------------------------------------

        // Building product, variant and image maps.
        $responseProductMap = [];
        $responseVariantMap = [];
        $responseImageMap = [];

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
            $this->assertTrue($product->hasSyncedToChannel());

            // Check product variant count.
            $this->assertEquals(count($existingProduct->variants), (count($product->variants)));

            // Check existing product variants.
            foreach ($existingProduct->variants as $existingVariant) {
                $variant = $responseVariantMap[$existingVariant->channel_variant_code];
                $this->assertTrue($variant instanceof vo\ChannelVariant);
                $this->assertTrue($variant->hasSyncedToChannel());
                $this->assertNotEmpty($variant->sku);
            }

            // Check image count.
            $this->assertEquals(count($existingProduct->images), (count($product->images)));

            // Check existing images.
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
     */
    public function testGetProducts()
    {
        $channelTypes = self::getChannelTypes();
        foreach ($channelTypes as $type) {

            // Cleanup.
            if ($type === 'memory') {
                dal\channels\memory\ChannelState::clean();
            }

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
            // Create all products on the channel.
            $channelProducts = vo\ChannelProduct::createArray(self::$channelProductsData);
            $connector->sync($channelProducts, $channel, $flagMap);

            // sort products by channel_product_code
            usort($channelProducts, function (vo\ChannelProduct $p1, vo\ChannelProduct $p2) {
                return strcmp($p1->channel_product_code, $p2->channel_product_code);
            });

            // change from here

//            // sort products by channel_product_code
//            usort($channelProducts, function (vo\ChannelProduct $p1, vo\ChannelProduct $p2) {
//                return strcmp($p1->channel_product_code, $p2->channel_product_code);
//            });

            // return products one at a time.
            $channel_product_code = '';
            foreach ($channelProducts as $product) {
                $existingChannelProducts = $connector->get($channel_product_code, 1, $channel);
                $this->assertCount(1, $existingChannelProducts);
                $this->assertEquals($product->channel_product_code, $existingChannelProducts[0]->channel_product_code);
                $this->assertCount(count($product->variants), $existingChannelProducts[0]->variants);
                $this->assertCount(count($product->images), $existingChannelProducts[0]->images);
                $token = '';
                $retrievedProductsMap = [];
                $retrievedImagesMap = [];
                $retrievedVariantsMap = [];
                for ($i = 0; $i < count($channelProducts); $i++) {
                    $ChannelProductGet = $connector->get($token, 1, $channel);
                    $token = $ChannelProductGet->token;
                    $this->assertCount(1, $ChannelProductGet->ChannelProducts);
                    $channelProduct = $ChannelProductGet->ChannelProducts[0];
                    $retrievedProductsMap[$channelProduct->channel_product_code] = $channelProduct;
//                $this->assertEquals($product->channel_product_code, $existingChannelProducts[0]->channel_product_code);
//                $this->assertCount(count($product->variants), $existingChannelProducts[0]->variants);
//                $this->assertCount(count($product->images), $existingChannelProducts[0]->images);
                    $this->assertTrue($product->success);
                    foreach ($existingChannelProducts[0]->variants as $variant) {
                        $this->assertNotEmpty($variant->channel_variant_code);
                        $this->assertTrue($variant->success);
                    }
                    foreach ($existingChannelProducts[0]->images as $image) {
                        $this->assertNotEmpty($image->channel_image_code);
                        $this->assertTrue($image->success);
                    }
                    $channel_product_code = $product->channel_product_code;

                    // do same map for images and variants
//                foreach ($existingChannelProducts[0]->variants as $variant) {
//                    $this->assertNotEmpty($variant->channel_variant_code);
//                    $this->assertTrue($variant->success);
//                }
//                foreach ($existingChannelProducts[0]->images as $image) {
//                    $this->assertNotEmpty($image->channel_image_code);
//                    $this->assertTrue($image->success);
//                }
//                $channel_product_code = $product->channel_product_code;
                }

                // loop through the products you synced to channel and make sure they exist in maps...
                foreach ($channelProducts as $product) {
                }

                // stop changing

                // delete products from channel
                foreach ($channelProducts as $product) {
                    $product->delete = true;
                }
                $connector->sync($channelProducts, $channel, $flagMap);
                $existingChannelProducts = $connector->get('', 10, $channel);
                $this->assertCount(0, $existingChannelProducts);
            }
        }
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