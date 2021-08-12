<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class OrderLineItem extends ValueObject
{
    /** @var string $barcode */
    public $barcode;

    /** @var string $code */
    public $code;

    /** @var int $grams */
    public $grams;

    /** @var int $price */
    public $price;

    /** @var int $qty */
    public $qty;

    /** @var string $sku */
    public $sku;

    /** @var OrderLineItemTax[] $tax_lines */
    public $tax_lines;

    /** @var string $title */
    public $title;

    /**
     * Creates the data object to spec.
     *
     * @param array $data
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->barcode   = self::stringFrom($data, 'barcode');
        $this->code      = self::stringFrom($data, 'code');
        $this->grams     = self::intFrom($data, 'grams');
        $this->price     = self::floatFrom($data, 'price');
        $this->qty       = self::floatFrom($data, 'qty');
        $this->sku       = self::stringFrom($data, 'sku');
        $this->tax_lines = OrderLineItemTax::createArray(self::arrayFrom($data, "tax_lines"));
        $this->title     = self::stringFrom($data, 'title');
    }

    /**
     * Creates an array of this class.
     *
     * @param array $data
     *
     * @return OrderLineItem[]
     */
    static function createArray(array $data): array
    {
        $returnable = [];
        foreach ($data as $item) {
            $returnable[] = new OrderLineItem((array)$item);
        }
        return $returnable;
    }
}
