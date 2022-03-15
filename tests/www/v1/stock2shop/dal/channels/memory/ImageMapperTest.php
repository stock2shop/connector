<?php

namespace tests\v1\stock2shop\dal\channels\memory;

use stock2shop\dal\channels\memory;
use stock2shop\exceptions\UnprocessableEntity;
use stock2shop\vo;
use tests;

/**
 * Image Mapper Test
 *
 * All unit test classes must extend the tests\TestCase base class.
 */
class ImageMapperTest extends tests\TestCase
{

    /**
     * Test Map
     *
     * This method evaluates the method which maps the product
     * onto a `ExampleImage` object.
     *
     * @return void
     * @throws UnprocessableEntity
     */
    public function testGet()
    {
        $channel_product_code = "4";
        $channel_image_code = "12";
        $url = "https://gm-stock2shop.s3.amazonaws.com/7cd85464cfff0a7eb91a6964cbc17826480ce767.jpg";

        // Mock product.
        $mp = new memory\MemoryProduct([
            'id' => $channel_product_code,
            'name' => 'Product Title',
            'price' => '2222',
            'quantity' => 2,
            'product_group_id' => '62714'
        ]);

        // Create object to map.
        $ci = new vo\ChannelImage(json_decode('{
          "id": 10203,
          "src": "' . $url . '",
          "storage_code": "7cd85464cfff0a7eb91a6964cbc17826480ce767.jpg",
          "src_50x50": "https://gm-stock2shop.s3.amazonaws.com/7cd85464cfff0a7eb91a6964cbc17826480ce767-50x50.jpg",
          "src_160x160": "https://gm-stock2shop.s3.amazonaws.com/7cd85464cfff0a7eb91a6964cbc17826480ce767-160x160.jpg",
          "active": true,
          "channel_image_code": "' . $channel_image_code . '"
        }', true));

        // Call method to test.
        $object = new memory\ImageMapper($ci, $mp);
        $outcome = $object->get();

        // Test/compare.
        $this->assertEquals($channel_image_code, $outcome->id);
        $this->assertEquals($channel_product_code, $outcome->product_id);
        $this->assertEquals($url, $outcome->url);

    }

}