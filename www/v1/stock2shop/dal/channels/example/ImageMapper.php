<?php

namespace stock2shop\dal\channels\example;

use stock2shop\vo;

/**
 * @package stock2shop\dal\example
 */
class ImageMapper
{

    /**
     * @var ExampleImage
     */
    private $image;

    /**
     * @param vo\ChannelImage $ci
     * @param ExampleProduct $ep
     */
    public function __construct(vo\ChannelImage $ci, ExampleProduct $ep)
    {
        $ei             = new ExampleImage();
        $ei->id         = $ci->src;
        $ei->product_id = $ep->product_group_id;
        $this->image    = $ei;
    }

    /**
     * @return ExampleImage
     */
    public function get(): ExampleImage
    {
        return $this->image;
    }

}