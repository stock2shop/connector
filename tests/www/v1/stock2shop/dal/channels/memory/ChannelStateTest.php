<?php

namespace tests\v1\stock2shop\dal\channels\memory;

use stock2shop\dal\channels\memory;
use stock2shop\exceptions\UnprocessableEntity;
use tests;

/**
 * Channel State Test
 */
class ChannelStateTest extends tests\TestCase
{

    /**
     * Test List Products
     *
     * This method returns a list of products based on
     * the offset and limit passed to it. It is used by
     * the `get()` method in the `memory\Products` class.
     *
     * @return void
     */
    public function testGetProducts()
    {
        memory\ChannelState::clean();

        $productCount = 11;
        $newMemoryProducts = [];
        for ($i = 0; $i < $productCount; $i++) {
            $newMemoryProducts[] = new memory\MemoryProduct([
                'id' => null,
                'product_group_id' => 'cpid1',
                'name' => 'Product Name',
                'price' => '5000.00',
                'quantity' => 5,
                'images' => [
                    'http://aws.stock2sho..1',
                ]
            ]);
        }

        memory\ChannelState::update($newMemoryProducts);
        $this->assertCount(count($newMemoryProducts), memory\ChannelState::getProducts());

        memory\ChannelState::clean();
    }

    /**
     * Test Update Images
     * @return void
     */
    public function testUpdateImages()
    {
        memory\ChannelState::clean();

        $memoryImageOne = new memory\MemoryImage(['id' => null, 'url' => 'http://aws.stock2sho..1', 'product_group_id' => '1']);
        $memoryImageTwo = new memory\MemoryImage(['id' => null, 'url' => 'http://aws.stock2sho..1', 'product_group_id' => '1']);
        memory\ChannelState::updateImages([$memoryImageOne, $memoryImageTwo]);

        $images = memory\ChannelState::getImages();
        foreach($images as $imageKey => $imageItem) {
            $this->assertTrue($imageItem instanceof memory\MemoryImage);
            $this->assertNotNull($imageItem->id);
            $this->assertNotNull($imageItem->url);
            $this->assertNotNull($imageItem->product_group_id);
        }

        memory\ChannelState::clean();
    }

    /**
     * Test Generate ID
     *
     * Evaluates the method which generates a random string ID
     * for use with images and products on the channel.
     *
     * @return void
     */
    public function testGenerateID()
    {
        memory\ChannelState::clean();
        $outcome = memory\ChannelState::generateID();
        $this->assertEquals('string', gettype($outcome));
        $this->assertEquals(50, strlen($outcome));
        memory\ChannelState::clean();
    }

    /**
     * Test Get Images By Group IDs
     *
     * Evaluates that the method returns MemoryImage
     * objects by "product_group_id".
     *
     * @return void
     */
    public function testGetImages()
    {
        memory\ChannelState::clean();

        $groupIDs = ['cpid1', 'cpid2'];
        $groupImages = [];
        for ($i = 0; $i !== 2; $i++) {
            $groupImages[] = new memory\MemoryImage([
                'id' => null,
                'product_group_id' => $groupIDs[$i],
                'url' => 'http://aws.stock2shop.../2'
            ]);
        }
        $groupImages = array_merge($groupImages, [new memory\MemoryImage([
            'id' => null,
            'product_group_id' => $groupIDs[1],
            'url' => 'http://aws.stock2shop.../2'
        ])]);

        memory\ChannelState::updateImages($groupImages);
        $outcome = memory\ChannelState::getImagesByGroupIDs($groupIDs);

        $this->assertNotNull($outcome);
        $this->assertCount(3, $outcome);
        $this->assertEquals('stock2shop\dal\channels\memory\MemoryImage', get_class($outcome[0]));
        $this->assertEquals($groupIDs[0], $outcome[0]->product_group_id);
        $this->assertEquals($groupIDs[1], $outcome[1]->product_group_id);
        $this->assertEquals($groupIDs[1], $outcome[2]->product_group_id);

        memory\ChannelState::clean();
    }

    /**
     * Test Get Product Groups
     *
     * Evaluates the getProductGroups() method.
     * The expected outcome is a map of products
     * keyed on "product_group_id".
     *
     * @return void
     */
    public function testGetProductGroups() {

        memory\ChannelState::clean();

        $groupIDs = ['cpid1', 'cpid2'];

        memory\ChannelState::update([
            new memory\MemoryProduct(['id' => null, 'product_group_id' => $groupIDs[0], 'name' => 'Product Name', 'price' => '5000.00', 'quantity' => 5 ]),
            new memory\MemoryProduct(['id' => null, 'product_group_id' => $groupIDs[0], 'name' => 'Product Name', 'price' => '5000.00', 'quantity' => 5 ]),
            new memory\MemoryProduct(['id' => null, 'product_group_id' => $groupIDs[0], 'name' => 'Product Name', 'price' => '5000.00', 'quantity' => 5 ]),
            new memory\MemoryProduct(['id' => null, 'product_group_id' => $groupIDs[1], 'name' => 'Product Name', 'price' => '5000.00', 'quantity' => 5 ])
        ]);

        $productGroups = memory\ChannelState::getProductGroups();

        $this->assertNotNull($productGroups);
        $this->assertEquals($groupIDs[0], array_keys($productGroups)[0]);
        $this->assertEquals($groupIDs[1], array_keys($productGroups)[1]);
        $this->assertCount(3, array_values($productGroups)[0]);
        $this->assertCount(1, array_values($productGroups)[1]);

        memory\ChannelState::clean();

    }

}