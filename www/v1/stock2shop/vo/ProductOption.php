<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class ProductOption extends ValueObject
{
    /** @var string|null $name */
    public $name;

    /** @var string|null $value */
    public $value;

    /** @var int|null $position */
    public $position;

    /**
     * ProductOption constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    function __construct(array $data)
    {
        $this->name     = self::stringFrom($data, "name");
        $this->value    = self::stringFrom($data, "value");
        $this->position = self::intFrom($data, "position");
    }

    /**
     * @param array $data
     * @return ProductOption[]
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $pmd = new ProductOption((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }
}
