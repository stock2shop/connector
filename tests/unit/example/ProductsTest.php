<?php

namespace tests\v1\stock2shop\dal\channels\example;

use tests;
use stock2shop\vo;
use stock2shop\dal\channels\example;

/**
 * Products Test
 *
 * All unit test classes must extend the tests\TestCase base class.
 * This class unit tests the functionality in the example\Products
 * connector.
 */
class ProductsTest extends tests\TestCase
{

    /** @var example\Products $connector */
    public $connector = null;

    /**
     * Setup
     *
     * Instantiate an object of the connector we are testing.
     * Also make sure runOffline() is called. The tests will
     * not and may not be run without this setting.
     *
     * @return void
     */
    public function setUp() {

        // Call creator factory.
        $creator = new example\Creator();

        // Create new connector instance for Products.
        $this->connector = $creator->createProducts();

        // Set tests to run in 'offline mode'.
        $this->runOffline();

    }

    /**
     * Test Products Instantiate Object
     *
     * Creates a new Products() object and checks the object
     * definition.
     *
     * @return void
     */
    public function testProductsInstantiateObject() {

        // Instantiate connector.
        $connector = new example\Products();

        // Assert on class.
        $this->assertNotNull($object, " instantiating Products object returns null.");
        $this->assertEquals("stock2shop\\dal\\channels\\example\\Products", get_class($object), " invalid class definition for Products.");
        $this->assertEquals("image_separator", $object::CHANNEL_SEPARATOR_IMAGE, " invalid channel separator configured on Products object.");
        $this->assertEquals("variant_separator", $object::CHANNEL_SEPARATOR_VARIANT, " invalid channel separator configured on Products object.");

    }

    /**
     * Sync Products
     */
    public function testProductsSyncProducts() {

        // Prepare test data.
        $_channelId         = 99991;
        $_clientId          = 99992;
        $_sourceProductCode = 99993;

        // Mock the vo\ChannelProducts[] array.
        $channelProducts = [];
        for($i=0; $i!==7; $i++) {
            $channelProducts[] = new vo\ChannelProduct([
                "id" => rand(1,200),
                "channel_product_code" => null,
                "channel_id" => $_channelId,
                "source_product_code" => $_sourceProductCode,
                "variants" => [],
            ]);
        }

        // Mock the vo\Channel object.
        $channel = new vo\Channel([
            "id" => $_channelId,
            "meta" => [
                new vo\Meta([ "key" => "variant_separator", "value" => "~" ]),
                new vo\Meta([ "key" => "image_separator", "value" => "=" ])
            ]
        ]);

        // Check VOs are populated.
        $this->assertNotNull($channelProducts);
        $this->assertInstanceOf("stock2shop\\vo\\ChannelProduct", $channelProducts[0]);
        $this->assertInstanceOf("stock2shop\\vo\\Channel", $channel);
        $this->assertInstanceOf("stock2shop\\vo\\Meta", $channel->meta[0]);

    }

    /**
     * Get Products
     */
    public function testProductsGetProducts() {

        // Prepare the test data.

        // Mock the vo\ChannelProducts[] array.

        // Mock the vo\Channel object.

        // Mock the vo\Flags[] array.

        // Instantiate the connector.

        // Call get().

    }

    /**
     * Save Product
     */
    public function testProductsSaveProduct() {

        // Mock vo\ChannelProduct object.

        // Instantiate the connector.

        // Call saveProduct().

        // Assert on response.

    }

    /**
     * Delete Product
     */
    public function testProductsDeleteProduct() {

        // Mock vo\ChannelProduct object.

        // Instantiate the connector.

        // Call deleteProduct().

        // Assert on response.

    }

    /**
     * Save Variant
     */
    public function testProductsSaveVariant() {

        // Mock vo\ChannelVariant object.

        // Instantiate the connector.

        // Call saveVariant().

        // Assert on response.

    }

    /**
     * Delete Variant
     */
    public function testProductsDeleteVariant() {

        // Mock vo\ChannelVariant object.

        // Instantiate the connector.

        // Call deleteVariant().

        // Assert on response.

    }

    /**
     * Save Image
     */
    public function testProductsSaveImage() {

        // Mock vo\ChannelImage object.

        // Instantiate the connector.

        // Call saveImage().

        // Assert on response.

    }

    /**
     * Delete Image
     */
    public function testProductsDeleteImage() {

        // Mock vo\ChannelImage object.

        // Instantiate the connector.

        // Call deleteImage().

        // Assert on response.

    }

}