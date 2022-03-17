<?php

namespace tests\v1\stock2shop\dal\channels\memory;

use stock2shop\exceptions\UnprocessableEntity;
use tests;
use stock2shop\vo;
use stock2shop\dal\channels\memory;

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
     * @throws UnprocessableEntity
     */
    public function testGet()
    {

        // Create object to map.
        $channelProduct = new vo\ChannelProduct([
            'id' => '1',
            'channel_product_code' => '62714',
            'source_product_code' => '9876',
            'title' => 'Product Title',
            'variants' => [
                [
                    'id' => '1234',
                    'channel_variant_code' => '4210173',
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
        $template = '{
            "id": "{{ChannelVariant.channel_variant_code}}",
            "name": "{{ChannelProduct.title}}",
            "quantity": "{{ChannelVariant.qty}}",
            "product_group_id": "{{ChannelProduct.channel_product_code}}",
            "price": "{{ChannelVariant.price}}"
          }';

        // Call method to test.
        $object = new memory\ProductMapper($channelProduct, $channelProduct->variants[0], $template);
        $outcome = $object->get();

        // Test/compare.
        $this->assertEquals('62714', $outcome->product_group_id);
        $this->assertEquals('Product Title', $outcome->name);
        $this->assertEquals('2', $outcome->quantity);
        $this->assertEquals('2222', $outcome->price);

    }

}