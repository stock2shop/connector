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
     * @return void
     */
    public function setUp() {

        // Call creator factory.
        $creator = new example\Creator();

        // Create new connector instance for Products.
        $this->connector = $creator->createProducts();

    }

    /**
     * Save Product
     */
    public function testProductsSaveProduct() {

        // Channel product code
        $productId = "5000";
        $channelProductCode = $productId . ".json";

        // Mock vo\ChannelProduct object.
        $_channelProduct = new vo\ChannelProduct([
            "id" => $productId,
            "channel_product_code" => $channelProductCode,
            "channel_id" => "123",
            "source_product_code" => "source_sku",
            "variants" => [],
            "images" => []
        ]);

        // Instantiate the connector.
        $connector = $this->connector;

        // Call saveProduct().
        $result = $connector->saveProduct($_channelProduct->channel_product_code, $_channelProduct);

        // Assert on response.
        $this->assertTrue($result);

    }

    /**
     * Delete Product
     */
    public function testProductsDeleteProduct() {

        // Channel product code
        $productId = "5000";
        $channelProductCode = $productId . ".json";

        // Instantiate the connector.
        $connector = $this->connector;

        // Call deleteProduct().
        $result = $connector->deleteProduct($channelProductCode);

        // Assert on response.
        $this->assertTrue($result);

    }

    /**
     * Save Variant
     */
    public function testProductsSaveVariant() {

        // Channel product code
        $productId = '5000';
        $channelVariantCode = $productId . "~" . "VARIANT001" .  ".json";

        // Mock vo\ChannelProduct object.
        $_channelVariant = new vo\ChannelVariant([
            'id' => $productId,
            'channel_variant_code' => $channelVariantCode,
            'channel_id' => '123',
            'source_product_code' => 'source_sku',
            'variants' => [],
            'images' => []
        ]);

        // Instantiate the connector.
        $connector = $this->connector;

        // Call saveProduct().
        $result = $connector->saveVariant($_channelVariant->channel_variant_code, $_channelVariant);

        // Assert on response.
        $this->assertTrue($result);

    }

    /**
     * Delete Variant
     */
    public function testProductsDeleteVariant() {

        // Channel product code
        $productId = '5000';
        $channelVariantCode = $productId . "~" . "VARIANT001" . ".json";

        // Instantiate the connector.
        $connector = $this->connector;

        // Call deleteVariant().
        $result = $connector->deleteVariant($channelVariantCode);

        // Assert on response.
        $this->assertTrue($result);

    }

    /**
     * Save Image
     */
    public function testProductsSaveImage() {

        // Channel image code
        $productId = '5000';
        $channelImageCode = $productId . '=' . 'IMAGE001' . '.json';

        // Mock vo\ChannelProduct object.
        $_channelImage = new vo\ChannelImage([
            'id' => $productId,
            'channel_image_code' => $channelImageCode,
            'channel_id' => '123'
        ]);

        // Instantiate the connector.
        $connector = $this->connector;

        // Call saveProduct().
        $result = $connector->saveImage($_channelImage->channel_image_code, $_channelImage);

        // Assert on response.
        $this->assertTrue($result);

    }

    /**
     * Delete Image
     */
    public function testProductsDeleteImage()
    {

        // Channel product code
        $productId = '5000';
        $channelImageCode = $productId . '=' . 'IMAGE001' . '.json';

        // Instantiate the connector.
        $connector = $this->connector;

        // Call deleteVariant().
        $result = $connector->deleteImage($channelImageCode);

        // Assert on response.
        $this->assertTrue($result);

    }

}