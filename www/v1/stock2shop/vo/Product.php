<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class Product extends ValueObject
{
    /** @var bool|null $active */
    public $active;

    /** @var string|null $title */
    public $title;

    /** @var string|null $body */
    public $body_html;

    /** @var string|null $collection */
    public $collection;

    /** @var string|null $productType */
    public $product_type;

    /** @var string|null $tags */
    public $tags;

    /** @var string|null $vendor */
    public $vendor;

    /** @var ProductOption[] $options */
    public $options;

    /** @var Meta[] $meta */
    public $meta;

    /**
     * Product constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    function __construct(array $data)
    {
        $this->active       = self::boolFrom($data, "active");
        $this->title        = self::stringFrom($data, "title");
        $this->body_html    = self::stringFrom($data, "body_html");
        $this->collection   = self::stringFrom($data, "collection");
        $this->product_type = self::stringFrom($data, "product_type");
        $this->tags         = self::stringFrom($data, "tags");
        $this->vendor       = self::stringFrom($data, "vendor");
        $this->options      = ProductOption::createArray(self::arrayFrom($data, "options"));
        $this->meta         = Meta::createArray(self::arrayFrom($data, "meta"));
    }

    /**
     * sort array properties of Product
     */
    public function sort()
    {
        $this->sortArray($this->options, "name");
        $this->sortArray($this->meta, "key");
    }

    /**
     * @return string
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    public function computeHash(): string
    {
        $p = new Product((array)$this);
        $p->sort();
        $json = json_encode($p);

        return md5($json);
    }
}
