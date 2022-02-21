<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class Fulfillment extends ValueObject
{
    /** @var int|null $client_id */
    public $client_id;

    /** @var int|null $order_id */
    public $order_id;

    /** @var int|null $fulfillmentservice_id */
    public $fulfillmentservice_id;

    /** @var string|null $fulfillmentservice_order_code */
    public $fulfillmentservice_order_code;

    /** @var string|null $created */
    public $created;

    /** @var string|null $modified */
    public $modified;

    /** @var string|null $channel_synced */
    public $channel_synced;

    /** @var string|null $state */
    public $state;

    /** @var string|null $status */
    public $status;

    /** @var string|null $tracking_number */
    public $tracking_number;

    /** @var string|null $tracking_company */
    public $tracking_company;

    /** @var string|null $tracking_url */
    public $tracking_url;

    /** @var string|null $notes */
    public $notes;

    /** @var boolean|null $active */
    public $active;

    /**
     * Fulfillment constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    function __construct(array $data)
    {
        $this->client_id = self::intFrom($data, 'client_id');
        $this->order_id = self::intFrom($data, 'order_id');
        $this->fulfillmentservice_id = self::intFrom($data, 'fulfillmentservice_id');
        $this->fulfillmentservice_order_code = self::stringFrom($data, 'fulfillmentservice_order_code');
        $this->created = self::stringFrom($data, 'created');
        $this->modified = self::stringFrom($data, 'modified');
        $this->channel_synced = self::stringFrom($data, 'channel_synced');
        $this->state = self::stringFrom($data, 'state');
        $this->status = self::stringFrom($data, 'status');
        $this->tracking_number = self::stringFrom($data, 'tracking_number');
        $this->tracking_company = self::stringFrom($data, 'tracking_company');
        $this->tracking_url = self::stringFrom($data, 'tracking_url');
        $this->notes = self::stringFrom($data, 'notes');
        $this->active = self::boolFrom($data, 'active');
    }

    /**
     * @param array $data
     * @return Fulfillment[]
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $pmd = new Fulfillment((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }
}
