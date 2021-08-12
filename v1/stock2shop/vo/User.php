<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class User extends ValueObject
{
    /** @var int $customer_id */
    public $customer_id;

    /** @var int $user_id */
    public $user_id;

    /** @var string $price_tier */
    public $price_tier;

    /** @var int $qty_availability */
    public $qty_availability;

    /**
    * Creates the data object to spec.
    *
    * @param array $data
    *
    * @return void
    */
    public function __construct(array $data)
    {
        $this->customer_id = self::intFrom($data, 'customer_id');
        $this->user_id = self::intFrom($data, 'user_id');
        $this->price_tier = self::stringFrom($data, 'price_tier');
        $this->qty_availability = self::intFrom($data, 'qty_availability');
    }
}
