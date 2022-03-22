<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

/**
 *
 * This is used by many classes.
 * e.g. Customers, Products, Sources, Channels ...
 *
 * Class Meta
 * @package stock2shop\vo
 */
class Meta extends ValueObject
{

    /** @var string|null $key */
    public $key;

    /** @var string|null $value */
    public $value;

    /** @var string|null $template_name */
    public $template_name;

    /**
     * Meta constructor.
     * @param array $data
     */
    function __construct(array $data)
    {
        $this->key           = self::stringFrom($data, "key");
        $this->value         = self::stringFrom($data, "value");
        $this->template_name = self::stringFrom($data, "template_name");
    }

    /**
     * @param array $data
     * @return Meta[]
     */
    static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $pmd = new Meta((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }
}
