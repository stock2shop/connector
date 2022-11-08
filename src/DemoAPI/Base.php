<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use InvalidArgumentException;
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

    public static function intFrom(array $data, string $key): ?int
    {
        if (array_key_exists($key, $data)) {
            return (int)$data[$key];
        }
        return null;
    }

    public static function floatFrom(array $data, string $key): ?float
    {
        if (array_key_exists($key, $data)) {
            return self::toFloat($data[$key]);
        }
        return null;
    }

    private static function toFloat($arg): ?float
    {
        if (is_null($arg)) {
            return null;
        }
        if (is_string($arg)) {
            if (!is_numeric($arg)) {
                if (trim($arg) === "") {
                    return null;
                }
                throw new InvalidArgumentException(
                    "value is not numeric"
                );
            }
        }
        if (is_bool($arg)) {
            throw new InvalidArgumentException("value is a bool");
        }
        return (float)$arg;
    }
}
