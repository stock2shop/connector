<?php

namespace tests\v1\stock2shop\dal\channels\memory;

use stock2shop\dal\channels\memory;
use stock2shop\exceptions\UnprocessableEntity;
use tests;

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
        $memoryProducts = memory\ChannelState::update([$product]);
        $productID = $memoryProducts[0]->id;
        $this->assertNotNull($productID);
        $this->assertEquals("string", gettype($productID));

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
    public function testGetProductsList()
    {

        // Cleanup.
        memory\ChannelState::clean();

        // Create products.
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
        $createdProducts = memory\ChannelState::update($newMemoryProducts);

        // Test getting products off the channel.
        $outcome = memory\ChannelState::getProducts();
        $this->assertCount(count($newMemoryProducts), $createdProducts);

        // Cleanup.
        memory\ChannelState::clean();

    }

    /**
     * Test Update Images
     * @return void
     */
    public function testUpdateImages()
    {

        // Cleanup.
        memory\ChannelState::clean();

        // Setup.
        $IDs = memory\ChannelState::updateImages([new memory\MemoryImage([
            'id' => null,
            'url' => 'http://aws.stock2sho..1',
            'product_id' => '1'
        ])]);

        $this->assertNotEmpty($IDs, 'Failed to create image.');

        // Update the item.
        $update = new memory\MemoryImage([
            'id' => $IDs[0]->id,
            'url' => 'http://aws.stock2sho..1',
            'product_id' => '2'
        ]);

        $updatedIds = memory\ChannelState::updateImages([$update]);
        $this->assertNotNull($updatedIds);
        $this->assertCount(1, $updatedIds);

        // Cleanup the state.
        memory\ChannelState::clean();

    }

    /**
     * Test Create Image
     * @return void
     */
    public function testCreateImage()
    {

        // Cleanup.
        memory\ChannelState::clean();

        // Setup.
        $imageIds = [];

        for ($i = 0; $i !== 6; $i++) {
            $imageIds[] = memory\ChannelState::createImage(new memory\MemoryImage([
                'id' => null,
                'url' => 'http://aws.stock2sho..' . $i,
                'product_id' => '1'
            ]));
        }

        // Assert on outcome.
        $this->assertNotEmpty($imageIds);

        // Cleanup the state.
        memory\ChannelState::clean();

    }

    /**
     * Get Images
     *
     * Tests creating and returning all
     * images from the channel.
     *
     * @return void
     */
    public function _testGetImages()
    {

        // Cleanup.
        memory\ChannelState::clean();

        // Created image IDs.
        $imageIds = [];

        // Setup.
        $numberOfImages = 6;
        $productId = '1';
        for ($i = 0; $i !== $numberOfImages; $i++) {
            $imageIds[] = memory\ChannelState::createImage(new memory\MemoryImage([
                'id' => null,
                'url' => 'http://aws.stock2sho..' . $i,
                'product_id' => $productId
            ]));
        }

        // Test get images by IDs off channel.
        $outcome = memory\ChannelState::getImagesByIDs($imageIds);

        // Assert on outcome.
        $this->assertCount($numberOfImages, $outcome);
        // Return value must be a map.
        $this->assertArrayHasKey($imageIds[0], $outcome);
        // Values must be MemoryImage objects.
        $this->assertInstanceOf('stock2shop\dal\channels\memory\MemoryImage', array_values($outcome)[0]);
        // MemoryImage objects must have their "product_id" properties set to the ID of the product.
        $this->assertEquals($outcome[$numberOfImages - 1]->product_group_id, $productId);

        // Test get images off channel.
        $outcome = memory\ChannelState::getImages();

        // Assert on outcome.
        $this->assertCount($numberOfImages, $outcome);
        // Return value must be a map with string keys.
        $this->assertEquals('string', gettype(array_keys($outcome)[0]));
        // Values must be MemoryImage objects.
        $this->assertInstanceOf('stock2shop\dal\channels\memory\MemoryImage', $outcome[$numberOfImages - 2]);
        // Each object must have its "id" property set to a string value.
        $this->assertEquals('string', gettype(array_values($outcome)[0]->id));

        // Cleanup.
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

        // Cleanup the state.
        memory\ChannelState::clean();

        // Run the method.
        $outcome = memory\ChannelState::generateID();

        // Evaluate outcome.
        $expected = 50;
        $this->assertEquals('string', gettype($outcome));
        $this->assertEquals($expected, strlen($outcome));

        // Cleanup.
        memory\ChannelState::clean();

    }

    /**
     * Test Get Products
     * @return void
     */
    public function testGetProducts()
    {

        // Cleanup.
        memory\ChannelState::clean();

        // Create products.
        $p1 = new memory\MemoryProduct([
            'id' => null,
            'product_group_id' => 'cpid1',
            'name' => 'Product Name',
            'price' => '5000.00',
            'quantity' => 5
        ]);

        $p2 = new memory\MemoryProduct([
            'id' => null,
            'product_group_id' => 'cpid2',
            'name' => 'Product Name',
            'price' => '5000.00',
            'quantity' => 5
        ]);

        // Add products to state.
        $memoryProductIDs = memory\ChannelState::update([$p1, $p2]);

        // We should have two products with different IDs on the channel now.
        // Now test the method which returns all products from the channel.
        $products = memory\ChannelState::getProducts();

        // There must be two "MemoryProducts"s on the channel.
        $this->assertCount(2, $products);
        // Products must be "MemoryProduct" objects.
        $this->assertInstanceOf('stock2shop\dal\channels\memory\MemoryProduct', array_values($products)[1]);

        // Cleanup the state.
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
    public function testGetImagesByGroupIDs()
    {

        // Cleanup.
        memory\ChannelState::clean();

        // Group IDs.
        $groupIDs = ['cpid1', 'cpid2'];

        // Create products.
        $memoryProductIDs = memory\ChannelState::update([
            new memory\MemoryProduct([
                'id' => null,
                'product_group_id' => $groupIDs[0],
                'name' => 'Product Name',
                'price' => '5000.00',
                'quantity' => 5
            ]),
            new memory\MemoryProduct([
                'id' => null,
                'product_group_id' => $groupIDs[1],
                'name' => 'Product Name',
                'price' => '5000.00',
                'quantity' => 5
            ])]);

        // Array of image IDs.
        $imageIds = [];

        // Create one image for p1 and two images for p2.
        $imageIds[] = memory\ChannelState::createImage(new memory\MemoryImage([
            'id' => null,
            'product_group_id' => $groupIDs[0],
            'url' => 'http://aws.stock2shop.../1'
        ]));

        // p2 images
        for ($i = 0; $i !== 2; $i++) {
            memory\ChannelState::createImage(new memory\MemoryImage([
                'id' => null,
                'product_group_id' => $groupIDs[1],
                'url' => 'http://aws.stock2shop.../2'
            ]));
        }

        // Run test.
        $outcome = memory\ChannelState::getImagesByGroupIDs($groupIDs);

        // Assert.
        $this->assertNotNull($outcome);
        $this->assertCount(3, $outcome);
        $this->assertEquals('stock2shop\dal\channels\memory\MemoryImage', get_class($outcome[0]));
        $this->assertEquals($memoryProductIDs[0]->product_group_id, $outcome[0]->product_group_id);
        $this->assertEquals($memoryProductIDs[1]->product_group_id, $outcome[1]->product_group_id);
        $this->assertEquals($memoryProductIDs[1]->product_group_id, $outcome[2]->product_group_id);

        // Cleanup.
        memory\ChannelState::clean();

    }

    /**
     * Test Delete Images By Group IDs
     *
     * This method removes images from the channel
     * by "product_group_id".
     *
     * @return void
     */
    public function testDeleteImagesByGroupIDs()
    {

        // Cleanup.
        memory\ChannelState::clean();

        // Group IDs.
        $groupIDs = ['cpid1', 'cpid2'];

        // Create products.
        $p1 = memory\ChannelState::update([new memory\MemoryProduct([
            'id' => null,
            'product_group_id' => $groupIDs[0],
            'name' => 'Product Name',
            'price' => '5000.00',
            'quantity' => 5
        ])]);

        $p2 = memory\ChannelState::update([new memory\MemoryProduct([
            'id' => null,
            'product_group_id' => $groupIDs[1],
            'name' => 'Product Name',
            'price' => '5000.00',
            'quantity' => 5
        ])]);

        // Array of image IDs.
        $imageIds = [];

        // Create one image for p1 and two images for p2.
        $imageIds[] = memory\ChannelState::createImage(new memory\MemoryImage([
            'id' => null,
            'product_group_id' => $groupIDs[0],
            'url' => 'http://aws.stock2shop.../1'
        ]));

        // p2 images
        for ($i = 0; $i !== 2; $i++) {
            memory\ChannelState::createImage(new memory\MemoryImage([
                'id' => null,
                'product_group_id' => $groupIDs[1],
                'url' => 'http://aws.stock2shop.../2'
            ]));
        }

        // Check state.
        $stateImages = memory\ChannelState::getImagesByGroupIDs($groupIDs);
        $this->assertCount(3, $stateImages);

        // Delete images.
        memory\ChannelState::deleteImagesByGroupIDs($groupIDs);

        // Assert
        $stateImages = memory\ChannelState::getImagesByGroupIDs($groupIDs);
        $this->assertEmpty($stateImages);

        // Cleanup.
        memory\ChannelState::clean();

    }

}