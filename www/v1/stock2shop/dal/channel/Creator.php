<?php

namespace stock2shop\dal\channel;

use stock2shop\vo\Channel;

abstract class Creator
{
    /** @var Channel|null $channel The channel that the Creator is creating connector's for. */
    public $channel;

    /**
     * @return void
     */
    public function __construct(Channel $channel=null)
    {
        $this->channel = $channel ?? null;
    }

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

