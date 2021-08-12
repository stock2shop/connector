<?php

namespace stock2shop\vo;

use stock2shop\vo\Variant;

class SystemVariant extends Variant
{
    /** @var int $id */
    public $id;

    /** @var int $product_id */
    public $product_id;

    /** @var int $image_id */
    public $image_id;

    /**
    * Creates the data object to spec.
    *
    * @param array $data
    *
    * @return void
    */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->id = static::intFrom($data, 'id');
        $this->product_id = static::intFrom($data, 'product_id');
        $this->image_id = static::intFrom($data, 'image_id');
    }

    /**
     * Computes a hash of the SystemVariant
     *
     * @return string
     */
    public function computeHash(): string
    {
        // Unlike SystemProduct there are no additional properties to include
        return parent::computeHash();
    }

    /**
     * Creates an array of this class.
     *
     * @param array $data
     *
     * @return SystemVariant[]
     */
    static function createArray(array $data): array {
        $returnable = [];

        foreach ($data as $item) {
            $returnable[] = new SystemVariant((array) $item);
        }

        return $returnable;
    }
}
