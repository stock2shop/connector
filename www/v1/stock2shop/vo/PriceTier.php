<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;
use stock2shop\exceptions\UnprocessableEntity;

/**
 * Price Tier
 *
 * This is the Value Object for the PriceTier items.
 *
 * @package stock2shop\vo
 */
class PriceTier extends ValueObject
{
    /** @var string|null $tier */
    public $tier;

    /** @var float|null $price */
    public $price;

    /**
     * Default Constructor
     *
     * @param array $data
     * @throws UnprocessableEntity
     */
    public function __construct(array $data)
    {
        $this->tier = self::stringFrom($data, "tier");
        $this->price = self::floatFrom($data, "price");
    }

    /**
     * Creqte Array
     *
     * @param array $data
     * @return PriceTier[]
     * @throws UnprocessableEntity
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $pmd = new PriceTier((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }

}
