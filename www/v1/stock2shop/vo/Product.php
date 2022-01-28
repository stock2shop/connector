<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;
use stock2shop\vo\Meta;
use stock2shop\vo\ProductOption;

/**
 * Product
 */
class Product extends ValueObject
{

    /** @var bool $active */
    public $active;

    /** @var string $title */
    public $title;

    /** @var string $body */
    public $body_html;

    /** @var string $collection */
    public $collection;

    /** @var string $productType */
    public $product_type;

    /** @var string $tags */
    public $tags;

    /** @var string $vendor */
    public $vendor;

    /** @var ProductOption[] $options */
    public $options;

    /** @var Meta[] $meta */
    public $meta;

    /**
     * Class Constructor
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data) {
        $this->active = self::boolFrom($data, "active");
        $this->title = self::stringFrom($data, "title");
        $this->body_html = self::stringFrom($data, "body_html");
        $this->collection = self::stringFrom($data, "collection");
        $this->product_type = self::stringFrom($data, "product_type");
        $this->tags = self::stringFrom($data, "tags");
        $this->vendor = self::stringFrom($data, "vendor");
        $this->options = ProductOption::createArray(self::arrayFrom($data, "options"));
        $this->meta = Meta::createArray(self::arrayFrom($data, "meta"));
    }

    /**
     * sort array properties of Product
     */
    public function sort() {
        $this->sortArray($this->options, "name");
        $this->sortArray($this->meta, "key");
    }

    /**
     * Computes a hash of the system product excluding the variants.
     *
     * @return string
     */
    public function computeHash(): string {
        $p = new Product((array)$this);
        $p->sort();
        $json = json_encode($p);
        return md5($json);
    }
}
