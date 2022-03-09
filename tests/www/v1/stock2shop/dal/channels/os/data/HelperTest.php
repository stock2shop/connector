<?php

namespace tests\v1\stock2shop\dal\channels\os\data;

use stock2shop\dal\channels\os;
use tests;

/**
 * Helper Test
 *
 * Example unit test
 *
 * All unit test classes must extend the tests\TestCase base class.
 */
class HelperTest extends tests\TestCase
{

    /**
     * Example unit test
     *
     * Note the naming convention used for testing a function.
     * e.g. test[FunctionName]()
     *
     * The path for the test class must be  similar to the path for the test function.
     *
     * e.g.
     * Testing the below class:
     * ${S2S_PATH}/connector/www/v1/stock2shop/dal/channels/os/data/Helper.php
     *
     * Would mean creating this file
     * ${S2S_PATH}/connector/tests/www/v1/stock2shop/dal/channels/data/HelperTest.php
     */
    public function testGetDataPath()
    {
        $path = os\data\Helper::getDataPath();
        $this->assertTrue(strpos($path, 'data') !== false);
    }
}