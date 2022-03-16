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
    public function __construct(vo\ChannelImage $ci, MemoryProduct $mp)
    {
        // Image mapping onto channel MemoryImage.
        $mapping = [
            "id" => $ci->channel_image_code,
            "product_id" => $mp->id,
            "url" => $ci->src
        ];

        // Create new object.
        $mi = new MemoryImage($mapping);
        $this->image = $mi;
    }

    /**
     * @return MemoryImage
     */
    public function get(): MemoryImage
    {
        return $this->image;
    }
}