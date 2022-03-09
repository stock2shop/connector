<?php

namespace tests\v1\stock2shop\dal\channels\service;

use tests;
use stock2shop\vo;
use stock2shop\dal\channels\service;

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
     * onto a `ServiceProduct` object.
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
            'variants' => [
                [
                    'id' => '1234',
                    'channel_variant_code' => '12341',
                    'qty' => 2,
                    'price' => '2222',
                    'sku' => 'product title 1'
                ],
                [
                    'id' => '12111235',
                    'channel_variant_code' => '122351',
                    'qty' => 10,
                    'price' => '3333',
                    'sku' => 'product title 2'
                ],
                [
                    'id' => '124435',
                    'channel_variant_code' => '123351',
                    'qty' => 10,
                    'price' => '4444',
                    'sku' => 'product title 3'
                ],
                [
                    'id' => '123335',
                    'channel_variant_code' => '112351',
                    'quantity' => 10,
                    'price' => '5555',
                    'sku' => 'product title 4'
                ],
                [
                    'id' => '12325',
                    'channel_variant_code' => '123551',
                    'quantity' => 10,
                    'price' => '6666',
                    'sku' => 'product title 5'
                ]
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

        // Mock the `vo\Channel` object.
        $channel = new vo\Channel([
            'meta' => [
                [
                    "key"=>"default_channel_product_map",
                    "value" => '{"id": "{{ChannelVariant.channel_variant_code}}", "name": "{{Meta.title}}", "brand": "{{Meta.brand}}", "quantity": "{{ChannelVariant.qty}}", "group": "{{ChannelProduct.id}}", "price": "{{ChannelVariant.price}}"}',
                    "template_name" => null
                ]
            ],
            'images' => []
        ]);

        // Call method to test
        $object = new service\ProductMapper($channel);
        $outcome = $object->map($channelProduct);

        // Now we need to create a ServiceProduct manually
        // and compare the two objects.
        $expected = new service\ServiceProduct();
        $expected->id = "12341";
        $expected->name ="Product Title";
        $expected->price = "2222";
        $expected->quantity = 2;
        $expected->group = "1";
        $expected->images = [
            'http://aws.stock2sho..1',
            'http://aws.stock2sho..2'
        ];

        // Test/compare.
        $this->assertEquals($expected->id, $outcome[0]->id);
        $this->assertEquals($expected->name, $outcome[0]->name);
        $this->assertEquals($expected->price, $outcome[0]->price);
        $this->assertEquals($expected->quantity, $outcome[0]->quantity);
        $this->assertEquals($expected->group, $outcome[0]->group);
        $this->assertEquals($expected->images, $outcome[0]->images);

    }


}