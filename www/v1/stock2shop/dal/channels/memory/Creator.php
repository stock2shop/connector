<?php

namespace stock2shop\dal\channels\memory;

use stock2shop\dal\channel;
use stock2shop\exceptions;
use stock2shop\lib;

class Creator extends channel\Creator
{

    /**
     * See comments in stock2shop\dal\channel\Creator::createProducts
     *
     * @return channel\Products
     */
    public function createProducts(): channel\Products
    {
        $products = new Products();
        if ($this->channel) {
            $products->logWriter = lib\LogWriterFactory::create('debug', $this->channel);
        }
        return $products;
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
