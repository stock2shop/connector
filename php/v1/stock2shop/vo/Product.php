<?php

namespace stock2shop\vo;

use stock2shop\vo\MetaItem;
use stock2shop\vo\ProductOption;
use stock2shop\base\ValueObject;

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

    /** @var string $source_product_code */
    public $source_product_code;

    /** @var string $tags */
    public $tags;

    /** @var string $vendor */
    public $vendor;

    /** @var ProductOption[] $options */
    public $options;

    /** @var MetaItem[] $meta */
    public $meta;

    /**
     * Product constructor.
     * @param array $data
     */
    function __construct(array $data) {
        $this->active = self::boolFrom($data, "active");
        $this->title = self::stringFrom($data, "title");
        $this->body_html = self::stringFrom($data, "body_html");
        $this->collection = self::stringFrom($data, "collection");
        $this->product_type = self::stringFrom($data, "product_type");
        $this->source_product_code = self::stringFrom($data, "source_product_code");
        $this->tags = self::stringFrom($data, "tags");
        $this->vendor = self::stringFrom($data, "vendor");
        $this->options =
            ProductOption::createArray(self::arrayFrom($data, "options"));
        $this->meta = MetaItem::createArray(self::arrayFrom($data, "meta"));
    }

    /**
     * sort array properties of Product
     */
    public function sort() {
        $this->sortArray($this->options, "name");
        $this->sortArray($this->meta, "key");
    }

    /**
     * Computes a hash of the system product excluding the variants and images.
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
