<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class Customer extends ValueObject
{
    /** @var bool $accepts_marketing */
    public $accepts_marketing;

    /** @var bool $active */
    public $active;

    /** @var CustomerAddress[] $addresses */
    public $addresses;

    /** @var string $email */
    public $email;

    /** @var string $first_name */
    public $first_name;

    /** @var string $last_name */
    public $last_name;

    /** @var CustomerMetaItem[] $meta */
    public $meta;

    /**
     * Product constructor.
     * @param array $data
     */
    function __construct(array $data)
    {
        $this->accepts_marketing = self::boolFrom($data, "accepts_marketing");
        $this->active            = self::boolFrom($data, "active");
        $this->addresses         = CustomerAddress::createArray(self::arrayFrom($data, "addresses"));
        $this->email             = self::stringFrom($data, "email");
        $this->first_name        = self::stringFrom($data, "first_name");
        $this->last_name         = self::stringFrom($data, "last_name");
        $this->meta              = CustomerMetaItem::createArray(self::arrayFrom($data, "meta"));
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
