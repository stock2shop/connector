<?php

namespace stock2shop\vo;

class SystemCustomer extends Customer
{
    /** @var bool|null $accepts_marketing */
    public $accepts_marketing;

    /** @var bool|null $active */
    public $active;

    /** @var string|null $channel_customer_code */
    public $channel_customer_code;

    /** @var Address[] $addresses */
    public $addresses;

    /** @var int|null $channel_id */
    public $channel_id;

    /** @var int|null $client_id */
    public $client_id;

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
        parent::__construct($data);

        $this->accepts_marketing     = static::boolFrom($data, 'accepts_marketing');
        $this->active                = static::boolFrom($data, 'active');
        $this->channel_customer_code = static::stringFrom($data, 'channel_customer_code');
        $this->addresses             = Address::createArray(static::arrayFrom($data, 'addresses')); // Not in source order
        $this->channel_id            = static::intFrom($data, 'channel_id');
        $this->client_id             = static::intFrom($data, 'client_id');
        $this->meta                  = Meta::createArray(static::arrayFrom($data, 'meta')); // Not in source order
        $this->user                  = new User(static::arrayFrom($data, 'user')); // Not in source order
    }
}
