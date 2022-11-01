<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

class Order extends Base
{
    public Address $billing_address;
    public Customer $customer;
    /** @var LineItem[] $line_items */
    public array $line_items;
    public Payment $payment;
    public Address $shipping_address;
    public ?string $entity_id;
    public ?string $state;
    public ?string $protect_code;
    public ?string $shipping_description;
    public ?string $base_shipping_amount;
    public ?float $base_total_paid;
    public ?float $discount_amount;
    public ?string $tax_amount;
    public ?string $weight;
    public ?string $increment_id;

    public function __construct(array $data)
    {
        $this->billing_address      = new Address(self::arrayFrom($data, 'billing_address'));
        $this->customer             = new Customer(self::arrayFrom($data, 'customer'));
        $this->line_items           = LineItem::createArray(self::arrayFrom($data, 'line_items'));
        $this->payment              = new Payment(self::arrayFrom($data, 'payment'));
        $this->shipping_address     = new Address(self::arrayFrom($data, 'shipping_address'));
        $this->shipping_address     = new Address(self::arrayFrom($data, 'shipping_address'));
        $this->entity_id            = self::stringFrom($data, 'entity_id');
        $this->state                = self::stringFrom($data, 'state');
        $this->protect_code         = self::stringFrom($data, 'protect_code');
        $this->shipping_description = self::stringFrom($data, 'shipping_description');
        $this->base_shipping_amount = self::stringFrom($data, 'base_shipping_amount');
        $this->base_total_paid      = self::floatFrom($data, 'base_total_paid');
        $this->discount_amount      = self::floatFrom($data, 'discount_amount');
        $this->tax_amount           = self::stringFrom($data, 'tax_amount');
        $this->weight               = self::stringFrom($data, 'weight');
        $this->increment_id         = self::stringFrom($data, 'increment_id');
    }

    /**
     * @return Order[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $a[] = new Order((array)$item);
        }
        return $a;
    }
}
