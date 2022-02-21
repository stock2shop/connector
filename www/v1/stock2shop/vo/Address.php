<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class Address extends ValueObject
{
    /** @var string|null $address1 */
    public $address1;

    /** @var string|null $address2 */
    public $address2;

    /** @var string|null $city */
    public $city;

    /** @var string|null $company */
    public $company;

    /** @var string|null $country */
    public $country;

    /** @var string|null $first_name */
    public $first_name;

    /** @var string|null $last_name */
    public $last_name;

    /** @var string|null $phone */
    public $phone;

    /** @var string|null $province */
    public $province;

    /** @var string|null $zip */
    public $zip;

    /** @var string|null $country_code */
    public $country_code;

    /** @var string|null $province_code */
    public $province_code;

    /**
     * Address constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    public function __construct(array $data)
    {
        $this->address1      = self::stringFrom($data, 'address1');
        $this->address2      = self::stringFrom($data, 'address2');
        $this->city          = self::stringFrom($data, 'city');
        $this->company       = self::stringFrom($data, 'company');
        $this->country       = self::stringFrom($data, 'country');
        $this->first_name    = self::stringFrom($data, 'first_name');
        $this->last_name     = self::stringFrom($data, 'last_name');
        $this->phone         = self::stringFrom($data, 'phone');
        $this->province      = self::stringFrom($data, 'province');
        $this->zip           = self::stringFrom($data, 'zip');
        $this->country_code  = self::stringFrom($data, 'country_code');
        $this->province_code = self::stringFrom($data, 'province_code');
    }

    /**
     * @param array $data
     * @return Address[]
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    static function createArray(array $data): array
    {
        $returnable = [];
        foreach ($data as $item) {
            $returnable[] = new Address((array)$item);
        }
        return $returnable;
    }
}
