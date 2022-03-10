<?php

namespace stock2shop\dal\channels\boilerplate;

use stock2shop\dal\channel;
use stock2shop\exceptions;

/**
 * Creator
 */
class Creator extends channel\Creator
{

    /**
     * See comments in Creator::createProducts
     *
     * @return channel\Products
     */
    public function createProducts(): channel\Products
    {
        return new Products();
    }

    /**
     * See comments in Creator::createOrders
     *
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
     * See comments in Creator::createFulfillments
     *
     * @return channel\Orders
     * @throws exceptions\NotImplemented
     */
    public function createFulfillments(): channel\Fulfillments
    {
        // See comment above.
        throw new exceptions\NotImplemented();
    }

}
