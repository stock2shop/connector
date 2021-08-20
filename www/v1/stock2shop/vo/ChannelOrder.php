<?php

namespace stock2shop\vo;

/**
 * TODO anything additional fields here?
 *
 * Class ChannelOrder
 * @package stock2shop\vo
 */
class ChannelOrder extends Order
{


    /**
     * Product constructor.
     * @param array $data
     */
    function __construct(array $data)
    {
        parent::__construct($data);

    }

    /**
     * Creates an array of this class.
     *
     * @param array $data
     *
     * @return Order[]
     */
    static function createArray(array $data): array
    {
        $returnable = [];
        foreach ($data as $item) {
            $returnable[] = new ChannelOrder((array)$item);
        }
        return $returnable;
    }

}
