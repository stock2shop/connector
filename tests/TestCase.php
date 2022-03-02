<?php

namespace tests;

use PHPUnit\Framework;

class TestCase extends Framework\TestCase
{
    var $S2S_TEST_MODE = null;

    function setUp(): void
    {
        // Some test cases require an internet connection,
        // or they must be run manually for once-off scripts,
        // they can be skipped by setting the S2S_TEST_MODE env to "offline"
        $this->S2S_TEST_MODE = getenv('S2S_TEST_MODE');
    }

    function runOffline()
    {
        return $this->S2S_TEST_MODE === 'offline';
    }

    public static function setUpBeforeClass(): void
    {
    }

    public static function tearDownAfterClass(): void
    {
    }
}
