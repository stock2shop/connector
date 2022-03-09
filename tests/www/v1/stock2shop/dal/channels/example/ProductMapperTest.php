<?php

namespace tests\v1\stock2shop\dal\channels\example;

use tests;
use stock2shop\vo;
use stock2shop\dal\channels\example;

/**
 * Product Mapper Test
 *
 * All unit test classes must extend the tests\TestCase base class.
 */
class ProductMapperTest extends tests\TestCase
{

    /**
     * Test Map
     *
     * This method evaluates the method which maps the product
     * onto a `ExampleProduct` object.
     *
     * @return void
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    public function testMap()
    {

        // Create object to map.
        $channelProduct = new vo\ChannelProduct([
            'id' => '1',
            'channel_product_code' => 'foo',
            'source_product_code' => '9876',
            'title' => 'Product Title',
            'variants' => [
                [
                    'id' => '1234',
                    'channel_variant_code' => '12341',
                    'qty' => 2,
                    'price' => '2222',
                    'sku' => 'product title 1'
                ],
            ],
            'images' => [
                ['id'=>'1', 'active'=>true, 'src'=>'http://aws.stock2sho..1', 'channel_image_code'=>'img1'],
                ['id'=>'2', 'active'=>true, 'src'=>'http://aws.stock2sho..2', 'channel_image_code'=>'img2']
            ],
            'meta' => [
                ['key'=>'title','value'=>'Product Title','template_name'=>null],
                ['key'=>'title2','value'=>'Product Title Alt','template_name'=>null],
                ['key'=>'brand','value'=>'Brand Name','template_name'=>null]
            ]
        ]);

        // Template.
        $template = '{"id": "{{ChannelVariant.channel_variant_code}}", "name": "{{ChannelProduct.title}}", "brand": "{{Meta.brand}}", "quantity": "{{ChannelVariant.qty}}", "product_group_id": "{{ChannelProduct.source_product_code}}", "price": "{{ChannelVariant.price}}"}';

        // Call method to test.
        $object = new example\ProductMapper($channelProduct, $channelProduct->variants[0], $template);
        $outcome = $object->get();

        // Now we need to create a ExampleProduct manually
        // and compare the two objects.
        $expected = new example\ExampleProduct();
        $expected->id = "12341";
        $expected->name ="Product Title";
        $expected->price = "2222";
        $expected->quantity = 2;
        $expected->product_group_id = "9876";

        // Test/compare.
        $this->assertEquals($expected->id, $outcome->id);
        $this->assertEquals($expected->name, $outcome->name);
        $this->assertEquals($expected->price, $outcome->price);
        $this->assertEquals($expected->quantity, $outcome->quantity);
        $this->assertEquals($expected->product_group_id, $outcome->product_group_id);

    }


}