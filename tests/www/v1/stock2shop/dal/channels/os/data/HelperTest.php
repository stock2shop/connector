<?php

namespace tests\v1\stock2shop\dal\channels\os\data;

use stock2shop\dal\channels\os;
use tests;

/**
 * Helper Test
 */
class HelperTest extends tests\TestCase
{

    /**
     * Test Get Data Path
     *
     * Checks whether the getDataPath() method
     * returns the expected path.
     *
     * @return void
     */
    public function testGetDataPath()
    {
        $path = os\data\Helper::getDataPath();
        $this->assertTrue(strpos($path, 'data') !== false);
    }
}