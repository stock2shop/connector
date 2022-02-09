<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;
use stock2shop\exceptions\UnprocessableEntity;

/**
 * Product Option
 *
 * This is the Value Object class for vo\ProductOption items.
 *
 * @package stock2shop\vo
 */
class ProductOption extends ValueObject
{
    /** @var string $name */
    public $name;

    /** @var string $value */
    public $value;

    /** @var int $position */
    public $position;

    /**
     * Default Constructor
     *
     * @param array $data
     * @throws UnprocessableEntity
     */
    public function __construct(array $data)
    {
        $this->name = self::stringFrom($data, "name");
        $this->value = self::stringFrom($data, "value");
        $this->position = self::intFrom($data, "position");
    }

    /**
     * Ceate Array
     *
     * Creates an array of this class.
     *
     * @param array $data
     * @return ProductOption[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $pmd = new ProductOption((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }

}
