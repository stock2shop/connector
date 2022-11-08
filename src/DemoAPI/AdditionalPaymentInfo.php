<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

/** @psalm-type PaymentInfo = array{
 *     method_title: ?string,
 *      payUReference: ?string
 * }
 */
class AdditionalPaymentInfo extends Base
{
    public ?string $method_title;
    public ?string $payUReference;

    /** @param PaymentInfo $data */
    public function __construct(array $data)
    {
        $this->method_title  = self::stringFrom($data, 'method_title');
        $this->payUReference = self::stringFrom($data, 'payUReference');
    }

    /**
     * @param PaymentInfo[] $data
     * @return AdditionalPaymentInfo[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $a[] = new AdditionalPaymentInfo((array)$item);
        }
        return $a;
    }
}
