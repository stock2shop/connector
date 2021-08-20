<?php

namespace stock2shop\vo;

use stock2shop\vo\Product;
use stock2shop\vo\SystemVariant;
use stock2shop\base\ValueObject;

class SystemFulfillment extends Fulfillment
{
    /** @var bool $active */
    public $active;

    /** @var bool $active */
    public $client_id;

    /** @var int $fulfillmentservice_id */
    public $fulfillmentservice_id;

    /** @var int $id */
    public $id;

    /** @var int $order_id */
    public $order_id;


    /**
     * Creates the data object to spec.
     *
     * @param array $data
     *
     * @return void
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->active                = self::boolFrom($data, "active");
        $this->id                    = static::intFrom($data, 'id');
        $this->client_id             = static::intFrom($data, 'client_id');
        $this->fulfillmentservice_id = static::intFrom($data, 'fulfillmentservice_id');
        $this->order_id              = static::intFrom($data, 'order_id');
    }

}
