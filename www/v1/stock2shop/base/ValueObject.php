<?php

namespace stock2shop\base;

abstract class ValueObject
{
    /**
     * Sorts a multi-dimensional array by key name.
     *
     * WARNING The $sortable array must be passed by reference
     * https://stackoverflow.com/a/10483117
     *
     * @param array $sortable
     * @param string $keyName
     */
    protected function sortArray(array &$sortable, string $keyName) {
        usort($sortable, function ($a, $b) use ($keyName) {
            return $a->$keyName <=> $b->$keyName;
        });
    }

    /**
     * @param array $data
     * @param string $key
     * @return bool Value of key if it exists or false
     */
    static function boolFrom(array $data, string $key): bool {
        if (array_key_exists($key, $data)) {
            if (gettype($data[$key]) === "string") {
                $s = strtolower($data[$key]);
                if ($s === "true") {
                    return true;
                }
                if ($s === "false") {
                    return false;
                }
            }
            return (bool)$data[$key];
        }
        return false;
    }

    /**
     * @param array $data
     * @param string $key
     * @return string Value of key if it exists or empty string
     */
    static function stringFrom(array $data, string $key): string {
        if (array_key_exists($key, $data)) {
            return (string)$data[$key];
        }
        return "";
    }

    /**
     * @param array $data
     * @param string $key
     * @return float Value of key if it exists or zero
     */
    static function floatFrom(array $data, string $key): float {
        if (array_key_exists($key, $data)) {
            return (float)$data[$key];
        }
        return 0;
    }

    /**
     * @param array $data
     * @param string $key
     * @return int Value of key if it exists or zero
     */
    static function intFrom(array $data, string $key): int {
        if (array_key_exists($key, $data)) {
            return (int)$data[$key];
        }
        return 0;
    }

    /**
     * @param array $data
     * @param string $key
     * @return array Value of key if it exists,
     *  and can be converted to array, or empty array
     */
    static function arrayFrom(array $data, string $key): array {
        if (array_key_exists($key, $data)) {
            switch (gettype($data[$key])) {
                case "object":
                    return (array)$data[$key];
                case "array":
                    return $data[$key];
            }
        }
        // For everything else return empty array
        return [];
    }

}
