<?php

namespace stock2shop\dal\channels\memory;

use stock2shop\vo;

/**
 * Data class describing the memory channels product
 *
 * @package stock2shop\dal\memory
 */
class MemoryProduct
{

    /**
     * @var string
     */
    public $id;

    /**
     * @var int
     */
    public $product_group_id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var float
     */
    public $price;

    /**
     * @var int
     */
    public $quantity;

    /**
     * Default Constructor
     *
     * Populates this object with associative array data.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data=[])
    {
        $this->id               = ($data["id"]) ?? null;
        $this->name             = ($data["name"]) ?? null;
        $this->price            = ($data["price"]) ?? null;
        $this->quantity         = ($data["quantity"]) ?? null;
        $this->product_group_id = (string)($data["product_group_id"]) ?? null;
    }

}