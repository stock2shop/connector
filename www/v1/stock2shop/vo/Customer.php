<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class Customer extends ValueObject
{
    /** @var string|null $email */
    public $email;

    /** @var string|null $first_name */
    public $first_name;

    /** @var string|null $last_name */
    public $last_name;

    /**
     * Customer constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     * @throws \stock2shop\exceptions\Validation
     */
    public function __construct(array $data)
    {
        $this->email                 = static::stringFrom($data, 'email');
        $this->first_name            = static::stringFrom($data, 'first_name');
        $this->last_name             = static::stringFrom($data, 'last_name');
    }
}
