<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

/** @psalm-type AddressData = array{address_type: string,
 *     city: ?string,
 *     country_id: ?string,
 *     email: ?string,
 *     entity_id: ?string,
 *     firstname: ?string,
 *     lastname: ?string,
 *     parent_id: ?string,
 *     postcode: ?string,
 *     quote_address_id: ?string,
 *     region: ?string,
 *     region_id: ?string,
 *     street: ?string,
 *     telephone: ?string
 * }
 */
class Address extends Base
{
    public ?string $address_type;
    public ?string $city;
    public ?string $country_id;
    public ?string $email;
    public ?string $entity_id;
    public ?string $firstname;
    public ?string $lastname;
    public ?string $parent_id;
    public ?string $postcode;
    public ?string $quote_address_id;
    public ?string $region;
    public ?string $region_id;
    public ?string $street;
    public ?string $telephone;

    /** @param AddressData $data */
    public function __construct(array $data)
    {
        $this->address_type     = self::stringFrom($data, 'address_type');
        $this->city             = self::stringFrom($data, 'city');
        $this->country_id       = self::stringFrom($data, 'country_id');
        $this->email            = self::stringFrom($data, 'email');
        $this->entity_id        = self::stringFrom($data, 'entity_id');
        $this->firstname        = self::stringFrom($data, 'firstname');
        $this->lastname         = self::stringFrom($data, 'lastname');
        $this->parent_id        = self::stringFrom($data, 'parent_id');
        $this->postcode         = self::stringFrom($data, 'postcode');
        $this->quote_address_id = self::stringFrom($data, 'quote_address_id');
        $this->region           = self::stringFrom($data, 'region');
        $this->region_id        = self::stringFrom($data, 'region_id');
        $this->street           = self::stringFrom($data, 'street');
        $this->telephone        = self::stringFrom($data, 'telephone');
    }

    /**
     * @param AddressData[] $data
     * @return Address[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $a[] = new Address((array)$item);
        }
        return $a;
    }
}
