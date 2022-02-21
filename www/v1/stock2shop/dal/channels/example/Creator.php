<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel;
use stock2shop\exceptions;

class Creator extends channel\Creator
{

    const DATA_PATH = __DIR__ . '/data';

    /**
     * Create Products
     *
     * This method implements the products channel creator.
     * This is an example of how to feed product data into
     * an implementation of the Data Access Layer.
     *
     * @return channel\Products
     */
    public function createProducts(): channel\Products
    {
        return new Products();
    }

    /**
     * Create Orders
     *
     * This method returns the connector's Order class.
     * The class must define all the methods present in
     * the dal\channel\Orders interface and is concerned
     * with processing of channel webhook data coming into
     * Stock2Shop.
     *
     * @return channel\Orders
     */
    public function createOrders(): channel\Orders
    {
        return new Orders();
    }

    public function createFulfillments(): channel\Fulfillments
    {
        throw new exceptions\NotImplemented();
    }

}
