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
    public $name;

    /**
     * @var string
     */
    public $brand;

    /**
     * @var float
     */
    public $price;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var string
     */
    public $parent_id;

    /**
     * unique path to each image
     * @var string[]
     */
    public $images;



}