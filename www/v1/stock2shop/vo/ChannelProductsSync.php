<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class ChannelProductsSync extends ValueObject
{
    /** @var MetaItem[] $meta */
    public $meta;

    /** @var ChannelProduct[] $channel_products */
    public $channel_products;

    /** @var bool $flag_map */
    public $flag_map;

    /**
     * Creates the data object to spec.
     *
     * @param array $data
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->meta             = MetaItem::createArray(self::arrayFrom($data, 'meta'));
        $this->channel_products = ChannelProduct::createArray(self::arrayFrom($data, 'channel_products'));
        $this->flag_map         = []; // todo
    }

}
