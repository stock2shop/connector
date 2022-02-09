<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;
use stock2shop\exceptions\UnprocessableEntity;

/**
 * Channel
 *
 * This is the Channel Value Object definition.
 *
 * @package stock2shop\vo
 */
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
     * Default Constructor
     * @param array $data
     * @throws UnprocessableEntity
     */
    public function __construct(array $data)
    {
        $this->id = static::intFrom($data, 'id');
        $this->client_id = static::intFrom($data, 'client_id');
        $this->description = static::stringFrom($data, 'description');
        $this->active = static::boolFrom($data, 'active');
        $this->type = static::stringFrom($data, 'type');
        $this->price_tier = static::stringFrom($data, 'price_tier');
        $this->qty_availability = static::stringFrom($data, 'qty_availability');
        $this->sync_token = static::stringFrom($data, 'sync_token');
        $this->meta = Meta::createArray(self::arrayFrom($data, "meta"));
    }

    /**
     * Get Meta Item Value By Key
     *
     * This is method to help with accessing the objects stored in the meta
     * class property of a Channel object.
     *
     * @param string $keyName The key you want the value for,
     * @return mixed $metaItem->value The value of the Meta item which has the matching key name.
     */
    public function getMetaItemValueByKey(string $keyName)
    {
        foreach ($this->meta as $metaItem) {
            // If the key matches the requested keyName, return the object.
            if ($metaItem->key === $keyName) {
                return $metaItem->value;
            }
        }
        return "";
    }

}
