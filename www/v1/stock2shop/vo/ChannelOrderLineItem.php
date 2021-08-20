<?php

namespace stock2shop\vo;

class ChannelOrderLineItem extends OrderLineItem
{
    /** @var string $channel_variant_code */
    public $channel_variant_code;


    /**
     * Creates the data object to spec.
     *
     * @param array $data
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->channel_variant_code   = self::stringFrom($data, 'channel_variant_code');
    }

    /**
     * Creates an array of this class.
     *
     * @param array $data
     *
     * @return ChannelOrderLineItem[]
     */
    static function createArray(array $data): array
    {
        $returnable = [];
        foreach ($data as $item) {
            $returnable[] = new ChannelOrderLineItem((array)$item);
        }
        return $returnable;
    }
}
