<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class PriceTier extends ValueObject
{
    /** @var string|null $tier */
    public $tier;

    /** @var float|null $price */
    public $price;

    /**
     * PriceTier constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    function __construct(array $data)
    {
        $this->tier  = self::stringFrom($data, "tier");
        $this->price = self::floatFrom($data, "price");
    }

    /**
     * @param array $data
     * @return PriceTier[]
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $pmd = new PriceTier((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }
}
