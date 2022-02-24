<?php

namespace tests\v1\stock2shop\dal\channels\example;

use tests;
use stock2shop\vo;
use stock2shop\dal\channels\example;

/**
 * Helper Test
 *
 * All unit test classes must extend the tests\TestCase base class.
 * This class unit tests the functionality in the example\data\Helper
 * class.
 */
class HelperTest extends tests\TestCase
{

    /** @var example\data\Helper $helper */
    public $helper = null;

    /** @var const string CLASS_NAME */
    const CLASS_NAME = "stock2shop\\dal\\channels\\example\\data\\Helper";

    /**
     * Setup
     * @return void
     */
    public function setUp() {

        // Create an instance of the Helper class which is being tested.
        $this->helper = new example\data\Helper();

    }

    /**
     * Test Get JSON Files By Prefix
     */
    public function testGetJSONFilesByPrefix() {

        // We'll loop through 3 times and check each possible storage
        // type in the test: orders, products and fulfillments.
        $_data = [
            ["62713", "products", 5],
            ["1", "orders", 1],
            ["500574586", 'fulfillments', 1]
        ];

        foreach($_data as $_testCase) {

            // Destructure
            $_prefix = $_testCase[0];
            $_type   = $_testCase[1];
            $_count  = $_testCase[2];

            // We are expecting an array of JSON files to be returned.
            $result = $this->helper->getJSONFilesByPrefix($_prefix, $_type);

            $this->assertNotNull($result);
            $this->assertEquals("array", gettype($result));
            $this->assertCount($_count, $result);

        }

    }

    /**
     * Test Get JSON Files
     */
    public function testGetJSONFiles()
    {

        // This test case evaluates whether the getJSONFiles() method
        // of the data\Helper class is working correctly.
        $_type  = "fulfillments";
        $_count = 22;

        // We are expecting an array of JSON files to be returned.
        $result = $this->helper->getJSONFiles($_type);

        $this->assertNotNull($result);
        $this->assertEquals('array', gettype($result));
        $this->assertCount($_count, $result);

    }

}