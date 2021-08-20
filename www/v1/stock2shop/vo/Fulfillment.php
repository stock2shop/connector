<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class Fulfillment extends ValueObject
{
    /** @var string $state */
    public $state;

    /** @var string $status */
    public $status;

    /** @var string $tracking_number */
    public $tracking_number;

    /** @var string $tracking_company */
    public $tracking_company;

    /** @var string $tracking_url */
    public $tracking_url;

    /** @var string $notes */
    public $notes;

    /**
     * Product constructor.
     * @param array $data
     */
    function __construct(array $data)
    {
        $this->state            = self::stringFrom($data, "state");
        $this->status           = self::stringFrom($data, "status");
        $this->tracking_number  = self::stringFrom($data, "tracking_number");
        $this->tracking_company = self::stringFrom($data, "tracking_company");
        $this->tracking_url     = self::stringFrom($data, "tracking_url");
        $this->notes            = self::stringFrom($data, "notes");
    }

    /**
     * Creates an array of this class.
     *
     * @param array $data
     *
     * @return Order[]
     */
    static function createArray(array $data): array
    {
        $returnable = [];
        foreach ($data as $item) {
            $returnable[] = new Fulfillment((array)$item);
        }
        return $returnable;
    }

}
