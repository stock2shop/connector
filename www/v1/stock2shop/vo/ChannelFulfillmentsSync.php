<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class ChannelFulfillmentsSync extends ValueObject
{
    /** @var MetaItem[] $meta */
    public $meta;

    /** @var ChannelFulfillment[] $channel_fulfillments */
    public $channel_fulfillments;

    /**
     * Creates the data object to spec.
     *
     * @param array $data
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->meta                 = MetaItem::createArray(self::arrayFrom($data, 'meta'));
        $this->channel_fulfillments = ChannelFulfillment::createArray(self::arrayFrom($data, 'channel_fulfillments'));
    }

}
