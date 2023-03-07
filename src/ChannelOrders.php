<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Share;
use Stock2Shop\Share\DTO;

class ChannelOrders implements Share\Channel\ChannelOrdersInterface
{
    public function transform(array $channelOrderWebhooks, DTO\Channel $channel): array
    {
        // TODO: Implement transform.
        return [];
    }
}
