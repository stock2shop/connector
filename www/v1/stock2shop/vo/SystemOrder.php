<?php

namespace stock2shop\vo;

class SystemOrder extends Order
{
    /** @var int|null $id */
    public $id;

    /** @var Meta $meta */
    public $meta;

    /** @var Customer $customer */
    public $customer;

    /**
     * SystemOrder constructor
     *
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     * @throws \stock2shop\exceptions\Validation
     */
    function __construct(array $data)
    {
        parent::__construct($data);

        $this->id = self::intFrom($data, 'id');
        $this->meta = Meta::createArray(self::arrayFrom($data, 'meta'));
        $this->customer = new Customer(self::arrayFrom($data, 'customer'));
    }
}
