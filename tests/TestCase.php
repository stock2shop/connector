<?php

namespace tests;

use PHPUnit\Framework;

class TestCase extends Framework\TestCase
{
    var $S2S_TEST_MODE = null;

    function runOffline()
    {
        return $this->S2S_TEST_MODE === 'offline';
    }

}
