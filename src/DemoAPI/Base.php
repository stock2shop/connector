<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

abstract class Base
{

    public static function stringFrom(array $data, string $key): ?string
    {
        if (array_key_exists($key, $data)) {
            return (string)$data[$key];
        }
        return null;
    }

    public static function arrayFrom(array $data, string $key): array
    {
        if (array_key_exists($key, $data)) {
            return (array)$data[$key];
        }
        return [];
    }

}