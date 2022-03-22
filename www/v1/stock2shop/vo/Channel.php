<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class Channel extends ValueObject
{

    /** @var int|null $id */
    public $id;

    /** @var string|null $description */
    public $description;

    /** @var int|null $client_id */
    public $client_id;

    /** @var bool|null $active */
    public $active;

    /** @var string|null $type */
    public $type;

    /** @var string|null $price_tier */
    public $price_tier;

    /** @var string|null $qty_availabilty */
    public $qty_availability;

    /** @var string|null $sync_token */
    public $sync_token;

    /** @var Meta[] $meta */
    public $meta;

    /**
     * Channel constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    public function __construct(array $data)
    {
        $this->id               = static::intFrom($data, 'id');
        $this->client_id        = static::intFrom($data, 'client_id');
        $this->description      = static::stringFrom($data, 'description');
        $this->active           = static::boolFrom($data, 'active');
        $this->type             = static::stringFrom($data, 'type');
        $this->price_tier       = static::stringFrom($data, 'price_tier');
        $this->qty_availability = static::stringFrom($data, 'qty_availability');
        $this->sync_token       = static::stringFrom($data, 'sync_token');
        $this->meta             = Meta::createArray(self::arrayFrom($data, "meta"));
    }
}
