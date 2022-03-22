<?php

namespace stock2shop\helpers;

/**
 *
 * Helpers for working with stock2shop meta
 * This includes channel, source, product, order, customer meta and so on...
 * The meta should have a key and value property
 *
 * Class Meta
 * @package stock2shop\helpers
 */
class Meta
{
    /**
     * Creates a key value array
     *
     * @param array $meta
     * @return array
     */
    public static function map(array $meta): array
    {
        $arr = [];
        foreach ($meta as $item) {
            $m              = (array)$item;
            $arr[$m['key']] = $m['value'];
        }
        return $arr;
    }

    /**
     * Returns list of meta with matching prefix
     *
     * @param array $meta
     * @param string $prefix
     * @return array
     */
    public static function prefixSearch(array $meta, string $prefix): array
    {
        $arr = [];
        foreach ($meta as $item) {
            $m = (array)$item;
            $key = strtolower($m['key']);
            if (strpos($key, strtolower($prefix)) === 0) {
                $arr[] = $m;
            }
        }
        return $arr;
    }

    /**
     * Gets the value for a specific key
     *
     * @param array $meta
     * @param string $key
     * @return bool|mixed
     */
    public static function get(array $meta, string $key)
    {
        foreach ($meta as $item) {
            $m = (array)$item;
            if ($m['key'] === $key) {
                return $m['value'];
            }
        }
        return false;
    }

    /**
     * Checks if a key is set to true
     *
     * @param array $meta
     * @param string $key
     * @return bool
     */
    public static function isTrue(array $meta, string $key): bool
    {
        $m = self::get($meta, $key);
        if (!$m) {
            return false;
        }
        if (gettype($m) === "string") {
            $s = strtolower($m);
            if ($s === "true") {
                return true;
            }
            if ($s === "false") {
                return false;
            }
        }
        return (bool)$m;
    }

}
