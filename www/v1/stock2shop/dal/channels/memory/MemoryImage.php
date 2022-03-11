<?php

namespace stock2shop\dal\channels\memory;

/**
 * Data class describing the memory channels images which are linked to products
 *
 * @package stock2shop\dal\memory
 */
class MemoryImage
{

    /**
     * @var string The ID of the product to which this image belongs.
     */
    public $product_id;

    /**
     * @var string The ID which the channel has given this image.
     */
    public $id;

    /**
     * @var string The storage path for the channel image.
     */
    public $url;

    /**
     * Default Constructor
     *
     * Populates this object with associative array data.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->id = ($data['id']) ?? null;
        $this->product_id = ($data['product_id']) ?? null;
        $this->url = ($data['url']) ?? null;
    }

}