<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class Image extends ValueObject
{
    /** @var bool $active */
    public $active;

    /** @var string $storage_code */
    public $storage_code;

    /** @var string $src */
    public $src;

    /** @var string $source_image_code */
    public $source_image_code;

    /**
     * Image constructor.
     * @param array $data
     */
    function __construct(array $data) {
        $this->active = self::boolFrom($data, "active");
        $this->storage_code = self::stringFrom($data, "storage_code");
        $this->src = self::stringFrom($data, "src");
        $this->source_image_code = self::stringFrom($data, "source_image_code");
    }

    /**
     * Creates an array of Images
     *
     * @param array $data
     * @return Image[]
     */
    static function createArray(array $data): array {
        $a = [];
        foreach ($data as $item) {
            $pmd = new Image((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }

}
