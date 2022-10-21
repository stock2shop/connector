<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

class Option
{
    public ?string $id;
    public string $sku;

    public function __construct(array $data)
    {
        $this->sku = self::stringFrom($data, 'sku');
        $this->id  = self::stringFrom($data, 'id');
    }

    public static function stringFrom(array $data, string $key): ?string
    {
        if (array_key_exists($key, $data)) {
            return (string)$data[$key];
        }
        return null;
    }
}
