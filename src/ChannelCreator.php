<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Share;

class ChannelCreator extends Share\Channel\ChannelCreator
{
    public function createChannelProducts(): Share\Channel\ChannelProductsInterface
    {
        return new ChannelProducts();
    }

    public function createChannelOrders(): Share\Channel\ChannelOrdersInterface
    {
        // TODO: Implement createChannelOrders() method.
    }
}
