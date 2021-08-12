<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class Order extends ValueObject
{
    /** @var Address $billing_address */
    public $billing_address;

    /** @var string $channel_order_code */
    public $channel_order_code;

    /** @var Customer $customer */
    public $customer;

    /** @var OrderLineItem[] $line_items */
    public $line_items;

    /** @var OrderMetaItem[] $meta */
    public $meta;

    /** @var string $title */
    public $notes;

    /** @var string $status */
    public $status;

    /** @var Address $shipping_address */
    public $shipping_address;

    /** @var OrderLineItem[] $shipping_lines */
    public $shipping_lines;

    /**
     * Product constructor.
     * @param array $data
     */
    function __construct(array $data)
    {
        $this->billing_address    = new Address(self::arrayFrom($data, "billing_address"));
        $this->channel_order_code = self::stringFrom($data, "channel_order_code");
        $this->customer           = new Customer(self::arrayFrom($data, "customer"));
        $this->line_items         = OrderLineItem::createArray(self::arrayFrom($data, "line_items"));
        $this->meta               = OrderMetaItem::createArray(self::arrayFrom($data, "meta"));
        $this->notes              = self::stringFrom($data, "notes");
        $this->status             = self::stringFrom($data, "status");
        $this->shipping_address   = new Address(self::arrayFrom($data, "shipping_address"));
        $this->shipping_lines     = OrderLineItem::createArray(self::arrayFrom($data, "shipping_lines"));
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
            $returnable[] = new Order((array)$item);
        }
        return $returnable;
    }

}
