<?php

namespace stock2shop\dal\channel;

abstract class Creator
{

    /**
     * @return Products
     */
    abstract public function createProducts(): Products;

    /**
     * @return Orders
     */
    abstract public function createOrders(): Orders;

    /**
     * @return Fulfillments
     */
    abstract public function createFulfillments(): Fulfillments;

}

