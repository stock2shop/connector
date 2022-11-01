<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

class Payment extends Base
{
    public ?string $entity_id;
    public ?string $parent_id;
    public ?int $shipping_captured;
    public ?string $method;
    public AdditionalPaymentInfo $additional_information;

    public function __construct(array $data)
    {
        $this->entity_id              = self::stringFrom($data, 'entity_id');
        $this->parent_id              = self::stringFrom($data, 'parent_id');
        $this->shipping_captured      = self::intFrom($data, 'shipping_captured');
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
