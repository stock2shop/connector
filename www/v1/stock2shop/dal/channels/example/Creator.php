<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel;
use stock2shop\exceptions;

/**
 * Creator
 */
class Creator extends channel\Creator
{

    /**
     * Create Products
     * @return channel\Products
     */
    public function createProducts(): channel\Products
    {
        // This is a concrete class implementation of the
        // abstract Creator factory class defined in dal\channel.
        // Do not add any additional methods to this class.
        return new Products();
    }

    /**
     * Create Orders
     * @return channel\Orders
     * @throws exceptions\NotImplemented
     */
    public function createOrders(): channel\Orders
    {
        // If your connector implementation does not support synchronising
        // order or fulfillment data, then you will throw an exception here.
        throw new exceptions\NotImplemented();
    }

    /**
     * Create Fulfillments
     * @return channel\Orders
     * @throws exceptions\NotImplemented
     */
    public function createFulfillments(): channel\Fulfillments
    {
        // See comment above.
        throw new exceptions\NotImplemented();
    }

}
