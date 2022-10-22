<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Connector\Config\Environment;
use Stock2Shop\Connector\Config\LoaderDotenv;
use Stock2Shop\Share;

class ChannelCreator extends Share\Channel\ChannelCreator
{
    public function __construct()
    {
        $loader = new LoaderDotenv(__DIR__ . '/../');
        Environment::set($loader);
    }

    public function createChannelProducts(): Share\Channel\ChannelProductsInterface
    {
        return new ChannelProducts();
    }
}
