<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

/**
 * Channel Product Get
 *
 * This is a Value Object class used to represent a collection of
 * `vo\ChannelProduct` objects and the corresponding cursor "token"
 * position of the channel.
 *
 * Please refer to the `get()` method's comments on the
 * `stock2shop\dal\channel\Products` interface for specific information
 * on how to use this class.
 *
 * @package stock2shop/vo
 */
class ChannelProductGet extends ValueObject
{

    /**
     * @var string|null
     * $token The token is the key of the last product in the collection.
     */
    public $token = null;

    /**
     * @var vo\ChannelProduct[]
     * $products This is an array of products which are returned from the channel.
     */
    public $products = [];

    /**
     * Default Constructor
     *
     * This method handles mass assignment of an associative array
     * to the class properties of this object.
     *
     * @param array $data
     * @return void
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    public function __construct(array $data = [])
    {
        $this->token = self::stringFrom($data, 'token');
        $this->products = ChannelProduct::createArray(self::arrayFrom($data, 'products'));
    }

}