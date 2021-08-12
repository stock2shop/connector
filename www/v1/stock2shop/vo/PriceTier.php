<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class PriceTier extends ValueObject
{
    /** @var string $tier */
    public $tier;

    /** @var float $price */
    public $price;

    /**
     * PriceTier constructor.
     * @param array $data
     */
    function __construct(array $data) {
        $this->tier = self::stringFrom($data, "tier");
        $this->price = self::floatFrom($data, "price");
    }

    /**
     * Creates an array of this class
     * @param array $data
     * @return PriceTier[]
     */
    static function createArray(array $data): array {
        $a = [];
        foreach ($data as $item) {
            $pmd = new PriceTier((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }
}
