<?php
namespace stock2shop\dal\channel;

use stock2shop\vo;

/**
 * Creator
 *
 * This is the connector factory class definition.
 *
 * Copy this class to your connector directory and name it 'Creator.php'
 * to create the concrete connector factory class. Each class method
 * returns a specific connector class.
 */
abstract class Creator
{

    /**
     * Create Products
     *
     * In concrete class implementations, this method returns the
     * custom connector object for Products.
     *
     * @return Products
     */
    abstract public function createProducts(): Products;

    /**
     * Create Orders
     *
     * In concrete class implementations, this method returns the
     * custom connector object for Orders.
     *
     * @return Orders
     */
    abstract public function createOrders(): Orders;

    /**
     * Create Fulfillments
     *
     * In concrete class implementations, this method returns the
     * custom connector object for Fulfillments.
     *
     * @return Fulfillments
     */
    abstract public function createFulfillments(): Fulfillments;

}

