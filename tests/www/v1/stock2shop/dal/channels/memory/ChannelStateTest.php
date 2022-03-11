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

        // Cleanup.
        memory\ChannelState::clean();

        // Create an object to reference the ChannelState class.
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
        $productId = memory\ChannelState::create($product);
        $this->assertNotNull($productId);
        $this->assertEquals("string", gettype($productId));

        // Get the product from channel state.
        $stateProducts = memory\ChannelState::getProductsByIDs([$productId]);
        $this->assertCount(1, $stateProducts);
        $this->assertEquals($product, $stateProducts[0]);

        // Cleanup the state.
        memory\ChannelState::clean();

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
    public function testGetProductsList() {

        // Cleanup.
        memory\ChannelState::clean();

        // Create products.
        $productCount = 11;
        $pageSize = 10;
        $offsets = [];

        for($i=0; $i<$productCount; $i++) {
            $offsets[] = $i;
            memory\ChannelState::create(new memory\MemoryProduct([
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

        // Here we are chunking the indices (iterator position)
        // of the items in the "ChannelState::$products" global
        // array.

        $chunks = array_chunk($offsets, $pageSize);
        foreach($chunks as $chunk) {

            // We then use the indices to simulate paging
            // and only return the products which are on
            // the current page.

            $products = memory\ChannelState::getProductsList($chunk[0], $pageSize);
            $this->assertEquals(count($chunk), count($products));

            // Assert on the request and the response
            // received from the channel state.

            foreach($chunk as $key => $index) {
                $this->assertEquals($index, $products[$key]->id);
            }

        }

    }

    /**
     * Test Update
     * @return void
     */
    public function testUpdate() {

        // Cleanup.
        memory\ChannelState::clean();

        // Setup.
        $id = memory\ChannelState::create(new memory\MemoryProduct([
            'id' => null,
            'product_group_id' => 'cpid1',
            'name' => 'Product Name',
            'price' => '5000.00',
            'quantity' => 5,
            'images' => [
                'http://aws.stock2sho..1',
            ]
        ]));

        $this->assertNotNull($id, "Failed to create product.");

        // Update the item.
        $update = new memory\MemoryProduct([
            'id' => $id,
            'product_group_id' => 'cpid2',
            'name' => 'new name',
            'price' => '1000.00',
            'quantity' => 2,
            'images' => []
        ]);
        $updatedIds = memory\ChannelState::update([$update]);

        // Check updated count is correct.
        $this->assertNotNull($updatedIds, "No updated IDs returned from channel.");
        $this->assertEquals($updatedIds, [$id], "Product ID does not match the updated product's ID.");
        $this->assertEquals($updatedIds, [$id], "Product ID does not match the updated product's ID.");

        // Cleanup.
        memory\ChannelState::clean();

    }

    /**
     * Test Update Images
     * @return void
     */
    public function testUpdateImages() {

        // Cleanup.
        memory\ChannelState::clean();

        // Setup.
        $id = memory\ChannelState::createImage(new memory\MemoryImage([
            'id' => null,
            'url' => 'http://aws.stock2sho..1',
            'product_id' => '1'
        ]));

        $this->assertNotNull($id, 'Failed to create image.');

        // Update the item.
        $update = new memory\MemoryImage([
            'id' => $id,
            'url' => 'http://aws.stock2sho..1',
            'product_id' => '2'
        ]);

        $updatedIds = memory\ChannelState::update([$update], 'images');
        $this->assertNotNull($updatedIds);
        $this->assertCount(1, $updatedIds);
        $this->assertEquals($updatedIds, [$id], "Image ID does not match.");

    }

}