<?php

namespace stock2shop\vo;

use stock2shop\vo\User;
use stock2shop\vo\Address;
use stock2shop\base\ValueObject;
use stock2shop\vo\CustomerMetaItem;

// TODO This is incomplete
class SystemCustomer extends ValueObject
{
    /** @var bool $accepts_marketing */
    public $accepts_marketing;

    /** @var bool $active */
    public $active;

    // TODO Create the Addresses class
//    /** @var Addresses $addresses */
//    public $addresses;

    /** @var string $channel_customer_code */
    public $channel_customer_code;

    /** @var int $channel_id */
    public $channel_id;

    /** @var int $client_id */
    public $client_id;

    /** @var string $email */
    public $email;

    /** @var string $first_name */
    public $first_name;

    /** @var string $last_name */
    public $last_name;

    /** @var CustomerMetaItem[] $meta */
    public $meta;

    /** @var User[] $user */
    public $user;

    /**
     * SystemCustomer constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->accepts_marketing = static::boolFrom($data, 'accepts_marketing');
        $this->active = static::boolFrom($data, 'active');
        $this->addresses = Address::createArray(static::arrayFrom($data, 'addresses'));
        $this->channel_customer_code = static::stringFrom($data, 'channel_customer_code');
        $this->channel_id = static::intFrom($data, 'channel_id');
        $this->client_id = static::intFrom($data, 'client_id');
        $this->email = static::stringFrom($data, 'email');
        $this->first_name = static::stringFrom($data, 'first_name');
        $this->last_name = static::stringFrom($data, 'last_name');
        $this->meta = CustomerMetaItem::createArray(static::arrayFrom($data, 'meta'));
        $this->user = new User(static::arrayFrom($data, 'user'));

        return $this;
    }
}
