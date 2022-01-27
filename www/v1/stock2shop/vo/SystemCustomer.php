<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class SystemCustomer extends ValueObject
{
    /** @var bool|null $accepts_marketing */
    public $accepts_marketing;

    /** @var bool|null $active */
    public $active;

    /** @var Address[] $addresses */
    public $addresses;

    /** @var string|null $channel_customer_code */
    public $channel_customer_code;

    /** @var int|null $channel_id */
    public $channel_id;

    /** @var int|null $client_id */
    public $client_id;

    /** @var string|null $email */
    public $email;

    /** @var string|null $first_name */
    public $first_name;

    /** @var string|null $last_name */
    public $last_name;

    /** @var Meta[] $meta */
    public $meta;

    /** @var User[] $user */
    public $user;

    /**
     * SystemCustomer constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     * @throws \stock2shop\exceptions\Validation
     */
    public function __construct(array $data)
    {
        $this->accepts_marketing     = static::boolFrom($data, 'accepts_marketing');
        $this->active                = static::boolFrom($data, 'active');
        $this->addresses             = Address::createArray(static::arrayFrom($data, 'addresses'));
        $this->channel_customer_code = static::stringFrom($data, 'channel_customer_code');
        $this->channel_id            = static::intFrom($data, 'channel_id');
        $this->client_id             = static::intFrom($data, 'client_id');
        $this->email                 = static::stringFrom($data, 'email');
        $this->first_name            = static::stringFrom($data, 'first_name');
        $this->last_name             = static::stringFrom($data, 'last_name');
        $this->meta                  = Meta::createArray(static::arrayFrom($data, 'meta'));
        $this->user                  = new User(static::arrayFrom($data, 'user'));
    }
}
