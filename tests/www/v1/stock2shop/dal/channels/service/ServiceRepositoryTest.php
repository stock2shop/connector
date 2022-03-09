<?php

namespace tests\v1\stock2shop\dal\channels\service;

use tests;
use stock2shop\vo;
use stock2shop\dal\channels\service;

/**
 * Service Repository Test
 *
 * All unit test classes must extend the tests\TestCase base class.
 */
class ServiceRepositoryTest extends tests\TestCase
{

    /**
     * Test Get Instance
     *
     * This evaluates whether an object of the class can be
     * correctly instantiated. It also checks whether the
     * state is working correctly by instantiating another
     * object which is expected to reference the same
     * object.
     *
     * @return void
     */
    public function testGetInstance()
    {

        // Instantiate repository.
        $firstRepository = service\ServiceRepository::getInstance();

        // Create ServiceProduct.
        $sp = new service\ServiceProduct();

        $sp->id         = '1';
        $sp->name       = 'Product One';
        $sp->quantity   = '50';
        $sp->price      = '1234';
        $sp->group      = 'Group A';
        $sp->brand      = 'Brand Name';

        // Add product.
        $firstRepository->addProduct($sp);

        // Instantiate repository again.
        $secondRepository = service\ServiceRepository::getInstance();

        // Evaluate state, expecting it to contain one product.
        $state = $secondRepository->getProductsByCode(['1']);

        // Assert on state.
        $this->assertNotNull($state);
        $this->assertEquals($firstRepository, $secondRepository);
        $this->assertEquals($sp, $state[0]);

    }

    /**
     * Test Add Products
     *
     * Tests whether the bulk add products method works
     * as expected.
     *
     * @return void
     */
    public function testSetProducts() {

        // Instantiate the repository.
        $repository = service\ServiceRepository::getInstance();

        // Mock array of ServiceProduct objects.
        $spOne = new service\ServiceProduct();

        $spOne->id         = '1';
        $spOne->name       = 'Product One';
        $spOne->quantity   = '50';
        $spOne->price      = '1234';
        $spOne->group      = 'Group A';
        $spOne->brand      = 'Brand Name';

        $spTwo = new service\ServiceProduct();

        $spTwo->id         = '2';
        $spTwo->name       = 'Product One';
        $spTwo->quantity   = '50';
        $spTwo->price      = '1234';
        $spTwo->group      = 'Group A';
        $spTwo->brand      = 'Brand Name';

        // Add to array.
        $sProducts = [$spOne, $spTwo];

        // Set the products.
        $repository->setProducts($sProducts);

        // Get products on repository.
        $existingProducts = $repository->getProductsByCode(['1','2','3']);

        // Assert on existing products on channel state.
        $this->assertEquals(2, count($existingProducts));
        $this->assertEquals('1', $existingProducts[0]->id);
        $this->assertEquals('2', $existingProducts[1]->id);

    }

    /**
     * Test Add Product
     *
     * Evaluates whether the add product method adds a
     * `ServiceProduct` object to the class' state.
     *
     * @return void
     */
    public function testAddProduct() {

        // Instantiate the repository.
        $repository = service\ServiceRepository::getInstance();

        // Mock `ServiceProduct`.
        $spOne = new service\ServiceProduct();

        $spOne->id         = 'addone';
        $spOne->name       = 'Product One';
        $spOne->quantity   = '50';
        $spOne->price      = '1234';
        $spOne->group      = 'Group A';
        $spOne->brand      = 'Brand Name';

        // Test add product.
        $repository->addProduct($spOne);

        // Get existing products by code.
        $existingProducts = $repository->getProductsByCode(['addone']);

        // Assert on state.
        $this->assertEquals(1, count($existingProducts));
        $this->assertEquals('stock2shop\\dal\\channels\\service\\ServiceProduct', get_class($existingProducts[0]));

    }

    /**
     * Test Unset Product By Code
     *
     * Tests whether the method to remove a product
     * deletes the correct `ServiceProduct` object
     * from the class' state.
     *
     * @return void
     */
    public function testUnsetProductByCode() {

        // Instantiate the repository.
        $repository = service\ServiceRepository::getInstance();

        // Mock a ServiceProduct object.
        $spOne = new service\ServiceProduct();

        $spOne->id         = 'addone';
        $spOne->name       = 'Product One';
        $spOne->quantity   = '50';
        $spOne->price      = '1234';
        $spOne->group      = 'Group A';
        $spOne->brand      = 'Brand Name';

        // Test add product.
        $repository->unsetProductByCode('addone');

        // Get existing products by code.
         $existingProducts = $repository->getProductsByCode(['addone']);

        // Assert on state.
        $this->assertEquals(0, count($existingProducts));

    }


}