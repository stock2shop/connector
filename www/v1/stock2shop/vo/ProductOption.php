<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class ProductOption extends ValueObject
{
    /** @var string $name */
    public $name;

    /** @var string $value */
    public $value;

    /** @var int $position */
    public $position;

    /**
     * ProductOption constructor.
     * @param array $data
     */
    function __construct(array $data) {
        $this->name = self::stringFrom($data, "name");
        $this->value = self::stringFrom($data, "value");
        $this->position = self::intFrom($data, "position");
    }

    /**
     * Creates an array of this class
     * @param array $data
     * @return ProductOption[]
     */
    static function createArray(array $data): array {
        $a = [];
        foreach ($data as $item) {
            $pmd = new ProductOption((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }
}
