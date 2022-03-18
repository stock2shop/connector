<?php

namespace stock2shop\dal\channels\memory;

use stock2shop\vo;

/**
 * Image Mapper
 *
 * This is the mapper class for "MemoryImage" objects.
 *
 * @package stock2shop\dal\memory
 */
class ImageMapper
{
    /**
     * @var MemoryImage
     */
    private $_image;

    /**
     * Default Constructor
     *
     * Maps a Stock2Shop image onto the "memory" channel schema.
     *
     * @param vo\ChannelImage $ci
     * @param MemoryProduct $mp
     */
    public function __construct(vo\ChannelImage $ci, MemoryProduct $mp)
    {
        $mapping = [
            "id" => $ci->channel_image_code,
            "product_group_id" => $mp->product_group_id,
            "url" => $ci->src
        ];
        $mi = new MemoryImage($mapping);
        $this->_image = $mi;
    }

    /**
     * Get
     *
     * Accessor method for $_image property.
     *
     * @return MemoryImage
     */
    public function get(): MemoryImage
    {
        return $this->image;
    }
}