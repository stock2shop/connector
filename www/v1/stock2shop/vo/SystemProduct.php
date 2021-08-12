<?php

namespace stock2shop\vo;

use stock2shop\vo\Product;
use stock2shop\vo\SystemVariant;
use stock2shop\base\ValueObject;

class SystemProduct extends Product
{
    /** @var int $id */
    public $id;

    /** @var string $source_product_code */
    public $source_product_code;

    /** @var SystemVariant[] $variants */
    public $variants;

    /**
     * Creates the data object to spec.
     *
     * @param array $data
     *
     * @return void
     */
    public function __construct(array $data) {
        parent::__construct($data);

        $this->id = static::intFrom($data, 'id');
        $this->source_product_code = static::stringFrom($data, 'source_product_code');
        $this->variants = SystemVariant::createArray(static::arrayFrom($data, 'variants'));
    }

    /**
     * Computes a hash of the SystemProduct
     *
     * @return string
     */
    public function computeHash(): string
    {
        $productHash = parent::computeHash();
        // More properties to include in the hash?
        // Order is important.
        // DO NOT include Stock2Shop DB IDs,
        // auto-increment PK might be replaced by KSUID
        $productHash .= "\nsource_product_code=$this->source_product_code";
        return md5($productHash);
    }
}
