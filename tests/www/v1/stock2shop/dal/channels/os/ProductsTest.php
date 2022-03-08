<?php

namespace tests\v1\stock2shop\dal\channels\os;

use stock2shop\dal\channels\os;
use stock2shop\vo;
use tests;

/**
 * Products Test
 *
 * All unit test classes must extend the tests\TestCase base class.
 */
class ProductsTest extends tests\TestCase
{

    /**
     * Example unit test
     *
     * Note the naming convention used for testing a function.
     * e.g. test[FunctionName]()
     *
     * The path for the test class must be  similar to the path for the test function.
     *
     * e.g.
     * Testing the below class:
     * ${S2S_PATH}/connector/www/v1/stock2shop/dal/channels/os/Products.php
     *
     * Would mean creating this file
     * ${S2S_PATH}/connector/tests/www/v1/stock2shop/dal/channels/os/ProductsTest.php
     */
    public function testSaveProduct()
    {
        // Create channel product
        $channelProduct = new vo\ChannelProduct([
            "id"                   => '5000',
            "channel_product_code" => 'foo'
        ]);
        $filename              = os\data\Helper::getDataPath() . '/products/' . $channelProduct->id . '.json';

        // Make sure previous test data removed
        unlink($filename);

        // Call method to test
        $osProducts     = new os\Products();
        $osProducts->saveProduct($channelProduct);

        // Test results
        // Does the file exist now, is it valid json and is it named correctly?
        $contents              = file_get_contents($filename);
        $productData           = json_decode($contents, true);
        $fetchedChannelProduct = new vo\ChannelProduct($productData);
        $this->assertEquals($channelProduct->id, $fetchedChannelProduct->id);
        $this->assertEquals($channelProduct->channel_product_code, $fetchedChannelProduct->channel_product_code);
    }
}