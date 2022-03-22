<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class User extends ValueObject
{
    /** @var int|null $customer_id */
    public $customer_id;

    /** @var int|null $id */
    public $id;

    /** @var Segment[] $segments */
    public $segments;

    /** @var string|null $price_tier */
    public $price_tier;

    /** @var string|null $qty_availability */
    public $qty_availability;

    /**
     * User constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     * @throws \stock2shop\exceptions\Validation
     */
    public function __construct(array $data)
    {
        $this->customer_id      = self::intFrom($data, 'customer_id');
        $this->id               = self::intFrom($data, 'id');
        $this->segments         = Segment::createArray(static::arrayFrom($data, 'segments'));
        $this->price_tier       = self::stringFrom($data, 'price_tier');
        $this->qty_availability = self::stringFrom($data, 'qty_availability');
    }
}
