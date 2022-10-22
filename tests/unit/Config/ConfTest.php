<?php

declare(strict_types=1);

namespace Stock2Shop\Tests\Connector\unit\Config;

use PHPUnit\Framework\TestCase;
use Stock2Shop\Connector\Config\Environment;
use Stock2Shop\Connector\Config\LoaderArray;
use Stock2Shop\Connector\Config\LoaderDotenv;

class ConfTest extends TestCase
{
    public function testGetByDotenv()
    {
        $_SERVER['X'] = 'hi';
        $_SERVER['Y'] = 'bye';
        $loader       = new LoaderDotenv(__DIR__, 'test.env');
        Environment::set($loader);
        $this->assertEquals('Bar', Environment::get('FOO'));
        $this->assertEquals('hi', Environment::get('X'));
        $this->assertEquals('123', Environment::get('A'));
        $this->assertEquals('bye', Environment::get('Y'));
    }

    public function testGetByArray()
    {
        $_SERVER['X'] = 'hi';
        $_SERVER['Y'] = 'bye';
        $loader       = new LoaderArray([
            'FOO' => 'Bar',
            'X'   => '',
            'A'   => '123',
            'Y'   => 'bye',
        ]);
        Environment::set($loader);
        $this->assertEquals('Bar', Environment::get('FOO'));
        $this->assertEquals('', Environment::get('X'));
        $this->assertEquals('123', Environment::get('A'));
        $this->assertEquals('bye', Environment::get('Y'));
    }
}