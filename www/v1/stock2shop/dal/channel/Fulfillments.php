<?php
namespace stock2shop\dal\channel;

use stock2shop\vo;

interface Fulfillments {

    /**
     * TODO TBC
     *
     * Syncs fulfillments from S2S to channel
     *
     * @param ChannelFulfillmentsSync $ChannelFulfillmentsSync
     * @return ChannelFulfillmentsSync
     */
//    public function syncFulfillments(ChannelFulfillments $ChannelFulfillments, vo\Channel $channel): ChannelFulfillmentsSync;

    /**
     *
     * TODO TBC
     *
     * The following properties must be set:-
     * - channel_fulfillment_code
     *
     * @param ChannelFulfillmentsSync $ChannelFulfillmentsSync
     * @return ChannelFulfillment[]
     */
//    public function getFulfillmentsByOrderCode(ChannelFulfillmentsSync $ChannelFulfillmentsSync): array;
}

