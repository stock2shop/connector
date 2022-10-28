<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoOrder;

use Stock2Shop\Share;

class Customer extends Base
{
    public ?string $entity_id;
    public ?string $website_id;
    public ?string $email;
    public ?string $group_id;
    public ?string $store_id;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $is_active;
    public ?string $disable_auto_group_change;
    public ?string $created_in;
    public ?string $firstname;
    public ?string $lastname;
    public ?string $rp_token;
    public ?string $rp_token_created_at;
    public ?string $default_billing;
    public ?string $default_shipping;
    public ?string $taxvat;
    public ?string $failures_num;
    public ?string $first_failure;

    public function __construct(array $data)
    {
        $this->entity_id                 = self::stringFrom($data, 'entity_id');
        $this->website_id                = self::stringFrom($data, 'website_id');
        $this->email                     = self::stringFrom($data, 'email');
        $this->group_id                  = self::stringFrom($data, 'group_id');
        $this->store_id                  = self::stringFrom($data, 'store_id');
        $this->created_at                = self::stringFrom($data, 'created_at');
        $this->updated_at                = self::stringFrom($data, 'updated_at');
        $this->is_active                 = self::stringFrom($data, 'is_active');
        $this->disable_auto_group_change = self::stringFrom($data, 'disable_auto_group_change');
        $this->created_in                = self::stringFrom($data, 'created_in');
        $this->firstname                 = self::stringFrom($data, 'firstname');
        $this->lastname                  = self::stringFrom($data, 'lastname');
        $this->rp_token                  = self::stringFrom($data, 'rp_token');
        $this->rp_token_created_at       = self::stringFrom($data, 'rp_token_created_at');
        $this->default_billing           = self::stringFrom($data, 'default_billing');
        $this->default_shipping          = self::stringFrom($data, 'default_shipping');
        $this->taxvat                    = self::stringFrom($data, 'taxvat');
        $this->failures_num              = self::stringFrom($data, 'failures_num');
        $this->first_failure             = self::stringFrom($data, 'first_failure');
    }

    /**
     * @return Customer[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $a[] = new Customer((array)$item);
        }
        return $a;
    }
}
