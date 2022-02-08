<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

/**
 * Channel Products Sync
 *
 * This class is used to represent a synchronisation action programmatically.
 * Configure this object with the ChannelProducts you are wanting to
 * synchronise with a Channel. You may optionally add Meta objects and
 * Flag objects as required.
 *
 * @see:vo
 */
class ChannelProductsSync extends ValueObject
{
    /** @var Meta[] $meta */
    public $meta;

    /** @var ChannelProduct[] $channel_products */
    public $channel_products;

    /** @var Flag[] $flag_map */
    public $flag_map;

    /**
     * Default Constructor
     *
     * Creates the object with the data provided.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->meta             = Meta::createArray(self::arrayFrom($data, 'meta'));
        $this->channel_products = ChannelProduct::createArray(self::arrayFrom($data, 'channel_products'));
        $this->flag_map         = Flag::createMap(self::arrayFrom($data, 'flags'));
    }

}