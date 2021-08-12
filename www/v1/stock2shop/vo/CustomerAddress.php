<?php

namespace stock2shop\vo;

class CustomerAddress extends Address
{

    /** @var bool $default */
    public $default;

    /**
    * Creates the data object to spec.
    *
    * @param array $data
    *
    * @return void
    */
    public function __construct(array $data)
    {
        $this->default = self::boolFrom($data, 'default');
    }

    /**
     * Creates an array of this class.
     *
     * @param array $data
     *
     * @return Address[]
     */
    static function createArray(array $data): array {
        $returnable = [];
        foreach ($data as $item) {
            $returnable[] = new CustomerAddress((array) $item);
        }
        return $returnable;
    }
}
