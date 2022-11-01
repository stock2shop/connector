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
        $meta = new Meta($channel);
        $map  = $meta->get(Meta::CHANNEL_ORDER_TEMPLATE);

        if ($map) {
            // get base order
            $channelOrders = TransformOrders::getChannelOrdersTemplate($map, $orders);

            // get line items with separate map
            $orderArr = json_decode(json_encode($orders), true);
            foreach ($orderArr as $index => $order) {
                foreach ($order['line_items'] as &$line_item) {
                    $map                                 = $meta->get(Meta::CHANNEL_ORDER_LINE_ITEM_TEMPLATE);
                    $channelOrders[$index]->line_items[] = TransformOrders::getChannelOrdersLineItems($map, $line_item);
                }
            }
        } else {
            $channelOrders = TransformOrders::getChannelOrders($orders);
        }

        // set instruction, add_order if processing or null if anything else
        foreach ($orders as $index => $order) {
            if ($order->state == DTO\ChannelOrder::ORDER_STATE_PROCESSING) {
                $channelOrders[$index]->instruction = DTO\ChannelOrder::INSTRUCTION_ADD_ORDER;
            }
        }
        return $channelOrders;
    }
}
