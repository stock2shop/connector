<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class Address extends ValueObject
{
    /** @var int $customer_id */
    public $customer_id;

    /** @var string $address1 */
    public $address1;

    /** @var string $address2 */
    public $address2;

    /** @var string $city */
    public $city;

    /** @var string $company */
    public $company;

    /** @var string $country */
    public $country;

    /** @var string $first_name */
    public $first_name;

    /** @var string $last_name */
    public $last_name;

    /** @var string $phone */
    public $phone;

    /** @var string $province */
    public $province;

    /** @var string $zip */
    public $zip;

    /** @var string $country_code */
    public $country_code;

    /** @var string $province_code */
    public $province_code;

    /** @var int $client_id */
    public $client_id;

    /** @var string $address_code */
    public $address_code;

    /** @var string $type */
    public $type;

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
        $this->customer_id = self::intFrom($data, 'customer_id');
        $this->address1 = self::stringFrom($data, 'address1');
        $this->address2 = self::stringFrom($data, 'address2');
        $this->city = self::stringFrom($data, 'city');
        $this->company = self::stringFrom($data, 'company');
        $this->country = self::stringFrom($data, 'country');
        $this->first_name = self::stringFrom($data, 'first_name');
        $this->last_name = self::stringFrom($data, 'last_name');
        $this->phone = self::stringFrom($data, 'phone');
        $this->province = self::stringFrom($data, 'province');
        $this->zip = self::intFrom($data, 'zip');
        $this->country_code = self::stringFrom($data, 'country_code');
        $this->province_code = self::stringFrom($data, 'province_code');
        $this->client_id = self::intFrom($data, 'province_code');
        $this->address_code = self::stringFrom($data, 'address_code');
        $this->type = self::stringFrom($data, 'type');
        $this->default = self::boolFrom($data, 'default');

        return $this;
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
            $returnable[] = new Address((array) $item);
        }

        return $returnable;
    }
}
