<?php

namespace stock2shop\dal\channels\boilerplate;

use stock2shop\dal\channel;
use stock2shop\exceptions;

class Creator extends channel\Creator
{

    /**
     * See comments in stock2shop\dal\channel\Creator::createProducts
     *
     * @return channel\Products
     */
    public function createProducts(): channel\Products
    {
        return new Products();
    }

    /**
     * See comments in stock2shop\dal\channel\Creator::createOrders
     *
     * @return channel\Orders
     * @throws exceptions\NotImplemented
     */
    public function createOrders(): channel\Orders
    {
        throw new exceptions\NotImplemented();
    }

    /**
     * See comments in stock2shop\dal\channel\Creator::createFulfillments
     *
     * @return channel\Orders
     * @throws exceptions\NotImplemented
     */
    public function createFulfillments(): channel\Fulfillments
    {
        throw new exceptions\NotImplemented();
    }
}
