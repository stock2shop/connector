<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class ChannelOrder extends ValueObject
{
    /** @var array|null $params */
    public $params;

    /** @var ChannelOrderChannel|null $channel */
    public $channel;

    /** @var ChannelOrderOrder $system_order */
    public $system_order;

    /**
     * ChannelOrder constructor
     *
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     * @throws \stock2shop\exceptions\Validation
     */
    function __construct(array $data)
    {
        $this->params = self::arrayFrom($data, 'params');
        $this->channel = self::arrayFrom($data, 'channel');
        $this->system_order = new ChannelOrderOrder(self::arrayFrom($data, 'system_order'));
    }
}
