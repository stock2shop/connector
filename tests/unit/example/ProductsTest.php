<?php

namespace tests\v1\stock2shop\dal\channels\example;

use tests;
use stock2shop\vo;
use stock2shop\dal\channels\example;

/**
 * Products Test
 */
class ProductsTest extends tests\TestCase
{

    /**
     * Test Products Instantiate Object
     *
     * Creates a new Products() object and checks the object
     * definition.
     */
    public function testProductsInstantiateObject() {

        $object = new example\Products();

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
                "id" => function() {
                    return rand(1, 200);
                },
                "channel_product_code" => null,
                "channel_id" => $_channelId,
                "source_product_code" => $_sourceProductCode,
                "variants" => [],
                ""
            ]);
        }

        // Mock the vo\Channel object.
        $channel = new vo\Channel();



    }

    /**
     * Get Products
     */
    public function testProductsGetProducts() {

        // Prepare the test data.

        // Mock the vo\ChannelProducts[] array.

        // Mock the vo\Channel object.

        // Mock the vo\Flags[] array.

    }

    /**
     * Save Product
     */
    public function testProductsSaveProduct() {

    }

    /**
     * Delete Product
     */
    public function testProductsDeleteProduct() {

    }

    /**
     * Save Variant
     */
    public function testProductsSaveVariant() {

    }

    /**
     * Delete Variant
     */
    public function testProductsDeleteVariant() {

    }

    /**
     * Save Image
     */
    public function testProductsSaveImage() {

    }

    /**
     * Delete Image
     */
    public function testProductsDeleteImage() {

    }

}