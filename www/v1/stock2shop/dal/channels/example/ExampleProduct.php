<?php

namespace stock2shop\dal\channels\example;

use stock2shop\vo;

/**
 * Data class describing the example channels product
 *
 * @package stock2shop\dal\example
 */
class ExampleProduct
{

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
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
}