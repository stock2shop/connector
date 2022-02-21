<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class Order extends ValueObject
{
    /** @var string|null $notes */
    public $notes;

    /** @var float|null $total_discount */
    public $total_discount;

    /** @var OrderItem $instruction */
    public $instruction;

    /** @var Address $billing_address */
    public $billing_address;

    /** @var Address $shipping_address */
    public $shipping_address;

    /** @var OrderItem $line_items */
    public $line_items;

    /** @var OrderItem $shipping_lines */
    public $shipping_lines;

    /**
     * Order constructor
     *
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     * @throws \stock2shop\exceptions\Validation
     */
    function __construct(array $data)
    {
        $this->notes = self::stringFrom($data, 'notes');
        $this->total_discount = self::floatFrom($data, 'total_discount');
        $this->instruction = self::stringFrom($data, 'instruction');
        $this->billing_address = new Address(self::arrayFrom($data, 'billing_address'));
        $this->shipping_address = new Address(self::arrayFrom($data, 'shipping_address'));
        $this->line_items = OrderItem::createArray(self::arrayFrom($data, 'line_items'));
        $this->shipping_lines = OrderItem::createArray(self::arrayFrom($data, 'shipping_lines'));
    }

    /**
     * @param array $data
     * @return ChannelProduct[]
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $cv  = new Order((array)$item);
            $a[] = $cv;
        }
        return $a;
    }

}
