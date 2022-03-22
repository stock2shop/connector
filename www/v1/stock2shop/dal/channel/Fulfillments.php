<?php
namespace stock2shop\dal\channel;

use stock2shop\vo;

interface Fulfillments {

    /**
     * Sync
     *
     * This method synchronises Fulfillments from Stock2Shop to the channel.
     *
     * @param ChannelFulfillmentsSync $ChannelFulfillmentsSync
     * @return vo\Fulfillment[]
     */
//    public function sync(vo\Fulfillment $ChannelFulfillments, vo\Channel $channel): array;

    /**
     * Get By Order Code
     *
     * The following properties must be set:-
     * - channel_fulfillment_code
     *
     * @param vo\Fulfillment[] $channelFulfillments
     * @param array $channelOrderCodes
     * @return vo\Fulfillment[]
     */
//    public function getFulfillmentsByOrderCode(array $channelOrderCodes, vo\Channel $channel): array;
}

