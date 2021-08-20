<?php

namespace stock2shop\vo;

/**
 *
 * Class ChannelOrder
 * @package stock2shop\vo
 */
class ChannelFulfillment extends Fulfillment
{

    /** @var string $active */
    public $channel_synced;

    /** @var string $channel_order_code */
    public $channel_order_code;

    /** @var string $channel_fulfillment_code */
    public $channel_fulfillment_code;

    /**
     * Product constructor.
     * @param array $data
     */
    function __construct(array $data)
    {
        parent::__construct($data);
        $this->channel_synced           = self::stringFrom($data, "channel_synced");
        $this->channel_fulfillment_code = self::stringFrom($data, "channel_fulfillment_code");
        $this->channel_order_code       = self::stringFrom($data, "channel_order_code");
    }

    /**
     * @param string $channel_synced
     * @return bool
     */
    static function isValidChannelSynced(string $channel_synced): bool
    {
        $format   = 'Y-m-d H:i:s';
        $d        = \DateTime::createFromFormat($format, $channel_synced);
        $timezone = $d->getTimezone()->getName();
        return $d && ($d->format($format) == $channel_synced) && ($timezone === 'UTC');
    }

    /**
     * Creates an array of this class.
     *
     * @param array $data
     *
     * @return Order[]
     */
    static function createArray(array $data): array
    {
        $returnable = [];
        foreach ($data as $item) {
            $returnable[] = new ChannelFulfillment((array)$item);
        }
        return $returnable;
    }

}
