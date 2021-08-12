<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class OrderLineItemTax extends ValueObject
{
    /** @var string $code */
    public $code;

    /** @var int $price */
    public $price;

    /** @var float $rate */
    public $rate;

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
        $this->code  = self::stringFrom($data, 'code');
        $this->price = self::floatFrom($data, 'price');
        $this->rate  = self::floatFrom($data, 'rate');
        $this->title = self::stringFrom($data, 'title');
    }

    /**
     * Creates an array of this class.
     *
     * @param array $data
     *
     * @return OrderMetaItem[]
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
