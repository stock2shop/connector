<?php

namespace example;

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
        return new Products();
    }

    /**
     * Create Orders
     * @return channel\Orders
     * @throws exceptions\NotImplemented
     */
    public function createOrders(): channel\Orders
    {
        throw new exceptions\NotImplemented();
    }

    /**
     * Create Fulfillments
     * @return channel\Orders
     * @throws exceptions\NotImplemented
     */
    public function createFulfillments(): channel\Fulfillments
    {
        throw new exceptions\NotImplemented();
    }

}
