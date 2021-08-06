<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class MetaItem extends ValueObject
{
    /** @var string $key */
    public $key;

    /** @var string $value */
    public $value;

    /** @var string $template_name */
    public $template_name;

    /**
     * MetaItem constructor.
     * @param array $data
     */
    function __construct(array $data) {
        $this->key = self::stringFrom($data, "key");
        $this->value = self::stringFrom($data, "value");
        $this->template_name = self::stringFrom($data, "template_name");
    }

    /**
     * Key / value map for meta items
     *
     * @param MetaItem[] $meta
     * @return array
     */
    static function getMap(array $meta): array {
        $map = [];
        foreach ($meta as $item) {
            $map[$item->key] = $item->value;
        }
        return $map;
    }

    /**
     * Creates an array of this class
     * @param array $data
     * @return MetaItem[]
     */
    static function createArray(array $data): array {
        $a = [];
        foreach ($data as $item) {
            $pmd = new MetaItem((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }
}
