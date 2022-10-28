<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoOrder;

use Stock2Shop\Share;

class Payment extends Base
{
    public ?string $entity_id;
    public ?string $parent_id;
    public ?int $base_shipping_captured;
    public ?int $shipping_captured;
    public ?int $base_amount_paid;
    public ?string $base_shipping_amount;
    public ?string $shipping_amount;
    public ?int $amount_paid;
    public ?string $base_amount_ordered;
    public ?string $amount_ordered;
    public ?string $method;
    public AdditionalPaymentInfo $additional_information;

    public function __construct(array $data)
    {
        $this->entity_id              = self::stringFrom($data, 'entity_id');
        $this->parent_id              = self::stringFrom($data, 'parent_id');
        $this->base_shipping_captured = self::intFrom($data, 'base_shipping_captured');
        $this->shipping_captured      = self::intFrom($data, 'shipping_captured');
        $this->base_amount_paid       = self::intFrom($data, 'base_amount_paid');
        $this->base_shipping_amount   = self::stringFrom($data, 'base_shipping_amount');
        $this->shipping_amount        = self::stringFrom($data, 'shipping_amount');
        $this->amount_paid            = self::intFrom($data, 'amount_paid');
        $this->base_amount_ordered    = self::stringFrom($data, 'base_amount_ordered');
        $this->amount_ordered         = self::stringFrom($data, 'amount_ordered');
        $this->method                 = self::stringFrom($data, 'method');
        $this->additional_information = new AdditionalPaymentInfo(self::arrayFrom($data, 'additional_information'));
    }

    /**
     * @return Payment[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $a[] = new Payment((array)$item);
        }
        return $a;
    }
}
