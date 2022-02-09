<?php
namespace stock2shop\dal\channel;

use stock2shop\vo;

interface Orders {

    /**
     * TODO TBC
     *
     * @param string $token
     * @param int $limit
     * @param vo\Channel $channel
     * @return array
     */
//    public function getOrders(string $token, int $limit,  vo\Channel $channel): array;

    /**
     *
     * TODO TBC
     *
     * @param ChannelOrder[] $orders
     * @param MetaItem[] $meta
     * @return ChannelOrder[]
     */
//    public function getOrdersByCode(array $orders, array $meta): array;

    /**
     *
     * TODO TBC
     *
     * Transform should convert the order "webhook" sent by the
     * channel into a vo\SystemOrder.
     *
     * @param mixed $webhookOrder
     * @param MetaItem[] $meta
     * @return ChannelOrder
     */
//    public function transformOrder($webhookOrder, array $meta): ChannelOrder;

}

