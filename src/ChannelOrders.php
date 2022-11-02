<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Share\DTO;
use Stock2Shop\Share;

class ChannelOrders implements Share\Channel\ChannelOrdersInterface
{
    /**
     * @param DTO\ChannelOrderWebhook[] $channelOrderWebhooks
     * @return DTO\ChannelOrder[]
     */
    public function transform(array $channelOrderWebhooks, DTO\Channel $channel): array
    {
        $payload = [];
        foreach ($channelOrderWebhooks as $webhook) {
            $payload[] = json_decode($webhook->payload, true);
        }
        $orders = DemoAPI\Order::createArray($payload);

        // fetch meta
        $meta          = new Meta($channel);
        $channelOrders = TransformOrders::getChannelOrders($orders, $meta->get(Meta::CHANNEL_ORDER_TEMPLATE));

        // set instruction, add_order if processing or null if anything else
        $state = $meta->get(Meta::ADD_ORDER_STATUS);
        foreach ($orders as $index => $order) {
            if (!$state) {
                $channelOrders[$index]->instruction = DTO\ChannelOrder::INSTRUCTION_EMPTY;
                continue;
            }
            if ($order->state == $state) {
                $channelOrders[$index]->instruction = DTO\ChannelOrder::INSTRUCTION_ADD_ORDER;
            }
        }
        return $channelOrders;
    }
}
