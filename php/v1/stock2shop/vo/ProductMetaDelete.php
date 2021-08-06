<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class ProductMetaDelete extends ValueObject
{
    /** @var string $key */
    public $key;

    /**
     * ProductMetaDelete constructor.
     * @param array $data
     */
    function __construct(array $data) {
        $this->key = self::stringFrom($data, "key");
    }

    /**
     * Creates an array of this class
     * @param array $data
     * @return ProductMetaDelete[]
     */
    static function createArray(array $data): array {
        $a = [];
        foreach ($data as $item) {
            $pmd = new ProductMetaDelete((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }
}
