<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

/**
 * Meta
 *
 * This is used by many classes.
 * e.g. Customers, Products, Sources, Channels.
 *
 * @package stock2shop\vo
 */
class Meta extends ValueObject
{

    /** @var string|null $key */
    public $key;

    /** @var string|null $value */
    public $value;

    /** @var string|null $template_name Meta items may be associated with a template name which groups them categorically. */
    public $template_name;

    /**
     * Default Constructor
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->key           = self::stringFrom($data, "key");
        $this->value         = self::stringFrom($data, "value");
        $this->template_name = self::stringFrom($data, "template_name");
    }

    /**
     * Create Array
     *
     * @param array $data
     * @return Meta[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $pmd = new Meta((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }
}
