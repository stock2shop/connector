<?php

namespace stock2shop\vo;

class SourceProductProduct extends Product
{
    /** @var Variant $variants */
    public $variants;

    /** @var  ProductMetaDelete[] $meta_delete */
    public $meta_delete;

    /**
     * SourceProductProduct constructor.
     * @param array $data
     */
    function __construct(array $data) {
        parent::__construct($data);
        $this->variants = new Variant(self::arrayFrom($data, "variants"));
        $this->meta_delete = ProductMetaDelete::createArray(
            self::arrayFrom($data, "meta_delete"));
    }

    /**
     * sort array properties of SourceProductProduct
     */
    public function sort() {
        $this->sortArray($this->meta_delete, "key");
    }

}
