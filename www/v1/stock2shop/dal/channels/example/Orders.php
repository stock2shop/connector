<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel\Orders as OrdersInterface;
use stock2shop\vo;

/**
 * Orders
 */
class Orders implements OrdersInterface
{

    /**
     * Get
     *
     * This method returns the products which have been synchronised to the channel.
     *
     * @param string $token
     * @param int $limit
     * @param vo\Channel $channel
     * @return array
     */
    public function get(string $token, int $limit, vo\Channel $channel): array {

        // Get the orders from the channel's storage.
        $currentFiles = data\Helper::getJSONFiles('orders');

        // Keep track of the orders which match the token.
        $channelOrders = [];
        $cnt = 1;

        // Iterate over the files.
        foreach ($currentFiles as $fileName => $obj) {
            if (strcmp($token, $fileName) < 0) {
                if ($cnt > $limit) {
                    break;
                }
                $orderJSON = json_encode($obj);
                $order = json_decode($orderJSON, true);
                $channelOrders[] = $this->transform($order, $meta);
                $cnt++;
            }
        }
        return $channelOrders;

    }

    /**
     * Get Orders By Code
     *
     * @param vo\ChannelOrder[] $orders
     * @param vo\Channel $channel
     * @return vo\ChannelOrder[]
     */
    public function getByCode(array $orders, vo\Channel $channel): array {

        return [];

    }

    /**
     * Transform Order.
     *
     * Transform should convert the order "webhook" sent by the
     * channel into a vo\SystemOrder.
     *
     * @param mixed $webhookOrder
     * @param vo\Channel $channel
     * @return vo\ChannelOrder
     */
    public function transform($webhookOrder, vo\Channel $channel): vo\ChannelOrder {

        // Define new vo\ChannelOrder object.
        $channelOrder = new vo\ChannelOrder();

        // Get the transform from the vo\Channel object.

        //

    }

}