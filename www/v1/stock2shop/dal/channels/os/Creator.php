<?php

namespace stock2shop\dal\channels\os;

use stock2shop\dal\channel;
use stock2shop\exceptions;

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
     */
    public function createOrders(): channel\Orders
    {
        return new Orders();
    }

    /**
     * Create Fulfillments
     * @return channel\Fulfillments
     * @throws exceptions\NotImplemented
     */
    public function createFulfillments(): channel\Fulfillments
    {
        throw new exceptions\NotImplemented();
    }

}
