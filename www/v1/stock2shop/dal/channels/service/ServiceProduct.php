<?php

namespace stock2shop\dal\channels\service;

/**
 * Service Product
 *
 * Data class describing a product on this channel.
 * @package stock2shop\dal\channels\service
 */
class ServiceProduct
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
    public $group;

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