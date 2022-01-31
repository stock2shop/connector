<?php

namespace stock2shop\dal\channel;

/**
 * Creator
 */
abstract class Creator
{

    /**
     * @return Products
     */
    abstract public function createProducts(): array;

    /**
     * @return Orders
     */
    abstract public function createOrders(): array;

    /**
     * @return Fulfillments
     */
    abstract public function createFulfillments(): array;

}

