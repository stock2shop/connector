<?php

namespace stock2shop\vo;

class ChannelOrderOrder extends Order
{
    /** @var int|null $channel_order_code */
    public $channel_order_code;

    /** @var SystemCustomer $customer */
    public $customer;

    /** @var Fulfillment $fulfillments */
    public $fulfillments;

    /**
     * ChannelOrderOrder constructor
     *
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     * @throws \stock2shop\exceptions\Validation
     */
    function __construct(array $data)
    {
        parent::__construct($data);

        $this->channel_order_code = self::stringFrom($data, 'channel_order_code');
        $this->customer = new SystemCustomer(self::arrayFrom($data, 'customer'));
        $this->fulfillments = Fulfillment::createArray(self::arrayFrom($data, 'fulfillments'));
    }
}
