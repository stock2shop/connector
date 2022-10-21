<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

class Option
{
    public ?int $id;
    public string $sku;

    public function __construct(array $data)
    {
        $this->sku = self::stringFrom($data, 'sku');
        $this->id = self::intFrom($data, 'id');
    }

    public static function intFrom(array $data, string $key): ?int
    {
        if (array_key_exists($key, $data)) {
            return (int) $data[$key];
        }
        return null;
    }

    public static function stringFrom(array $data, string $key): ?string
    {
        if (array_key_exists($key, $data)) {
            return (string) $data[$key];
        }
        return null;
    }
}
