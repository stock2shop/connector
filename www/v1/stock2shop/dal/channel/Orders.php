<?php
namespace stock2shop\dal\channel;

use stock2shop\vo;

/**
 * Interface Orders
 *
 * @package stock2shop\dal\channel
 */
interface Orders {

    /**
     * Get
     *
     * @param string $token
     * @param int $limit
     * @param vo\Channel $channel
     * @return array
     */
    public function get(string $token, int $limit, vo\Channel $channel): array;

    /**
     * Get By Code
     *
     * @param vo\ChannelOrder[] $orders
     * @param vo\Channel $channel
     * @return vo\ChannelOrder[]
     */
    public function getByCode(array $orders, vo\Channel $channel): array;

    /**
     * Transform
     *
     * This method should define the workflow for converting a "webhook" order
     * coming into Stock2Shop into a vo\ChannelOrder object.
     *
     * @param mixed $webhookOrder
     * @param vo\Channel $channel
     * @return vo\ChannelOrder
     */
    public function transform($webhookOrder, vo\Channel $channel): vo\ChannelOrder;

}