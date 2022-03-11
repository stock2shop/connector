<?php

namespace tests\v1\stock2shop\dal\channels\memory;

use stock2shop\exceptions\UnprocessableEntity;
use tests;
use stock2shop\vo;
use stock2shop\dal\channels\memory;

/**
 * Channel State Test
 *
 * This class evaluates the functionality in the
 * channel state singleton.
 */
class ChannelStateTest extends tests\TestCase
{

    /**
     * Test Create Product
     *
     * @return void
     * @throws UnprocessableEntity
     */
    public function testCreate()
    {

        // Create an object to reference the ChannelState class.
        $ref = memory\ChannelState::getInstance();

        // Create product.
        $product = new memory\MemoryProduct([
            // Product ID will not be set yet,
            // because the channel's state must return this.
            'id' => null,
            // This is the ID of the product,
            // called "channel_product_code" on Stock2Shop.
            'product_group_id' => 'cpid1',
            'name' => 'Product Name',
            'price' => '5000.00',
            'quantity' => 5,
            // The images are a string[] property and
            // must be handled in the ChannelState class.
            // Images are added to the $images class property.
            'images' => [
                'http://aws.stock2sho..1',
                'http://aws.stock2sho..2',
                'http://aws.stock2sho..3'
            ]
        ]);

        // Add product to the channel's state.
        $productId = $ref->create($product);
        $this->assertNotNull($productId);
        $this->assertEquals("string", gettype($productId));

        // Get the product from channel state.
        $stateProducts = $ref->getProductsByIDs([$productId]);
        $this->assertCount(1, $stateProducts);
        $this->assertEquals($product, $stateProducts[0]);

        // Cleanup the state.
        $ref->deleteProductsByIDs([$productId]);
        $this->assertCount(0, $ref->getProductsByIDs([$productId]));

    }

    /**
     * Test List Products
     *
     * This method returns a list of products based on
     * the offset and limit passed to it. It is used by
     * the `get()` method in the `memory\Products` class.
     *
     * @return void
     */
    public function testListProducts() {

        // The channel state will be populated with 12 products.
        // Get ChannelState reference.
        $ref = memory\ChannelState::getInstance();

        // Create products on the channel.
        for($i=0; $i<11; $i++) {
            $ref->create(new memory\MemoryProduct([
                'id' => null,
                'product_group_id' => 'cpid1',
                'name' => 'Product Name',
                'price' => '5000.00',
                'quantity' => 5,
                'images' => [
                    'http://aws.stock2sho..1',
                ]
            ]));
        }

        // Get all products up until limit.
        // Expected outcome is 10 MemoryProduct objects for first
        // page and 2 MemoryProduct objects for the second page.

        // IDs will be: 0,1,2,3,4,5,6,7,8,9   [10]
        $p1 = $ref->getProductsList('', 10);

        // IDs will be 10, 11   [2]
        $p2 = $ref->getProductsList('10', 10);

        $this->assertNotNull($p1);
        $this->assertCount(10, $p1);

        $this->assertNotNull($p2);
        $this->assertCount(2, $p2);

    }

}