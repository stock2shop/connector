<?php

namespace stock2shop\dal\channels\memory;

use stock2shop\vo;

/**
 * @package stock2shop\dal\memory
 */
class ImageMapper
{

    /**
     * @var MemoryImage
     */
    private $image;

    /**
     * @param vo\ChannelImage $ci
     * @param MemoryProduct $ep
     */
    public function __construct(vo\ChannelImage $ci, MemoryProduct $ep)
    {
        $ei             = new MemoryImage();
        $ei->id         = $ci->src;
        $ei->product_id = $ep->product_group_id;
        $this->image    = $ei;
    }

    /**
     * @return MemoryImage
     */
    public function get(): MemoryImage
    {
        return $this->image;
    }

}