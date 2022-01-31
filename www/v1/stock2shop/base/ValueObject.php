<?php

namespace stock2shop\base;

use stock2shop\exceptions;

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
     * @return bool|null Value of key if it exists
     */
    static function boolFrom(array $data, string $key) {
        if (array_key_exists($key, $data)) {
            if (is_null($data[$key])) {
                return null;
            }
            $type = gettype($data[$key]);
            if ($type === "string") {
                $s = strtolower($data[$key]);
                if ($s === "false") {
                    return false;
                }
                if ($s === "0") {
                    return false;
                }
                if ($s === "") {
                    return false;
                }
                return true;
            }
            if ($type === "integer") {
                if ($data[$key] === 0) {
                    return false;
                }
                return true;
            }
            if ($type === "double") {
                if ($data[$key] === 0.0) {
                    return false;
                }
                return true;
            }
            if ($type === "boolean") {
                return $data[$key];
            }
        }
        // Missing properties parse as null
        return null;
    }

    /**
     * String From
     * @param array $data
     * @param string $key
     * @return string|null Value of key if it exists
     */
    static function stringFrom(array $data, string $key) {
        if (array_key_exists($key, $data)) {
            if (is_null($data[$key])) {
                return null;
            }
            if (gettype($data[$key]) === "boolean") {
                if ($data[$key] === false) {
                    return "false";
                }
                return "true";
            }
            return (string)$data[$key];
        }
        // Missing properties parse as null
        return null;
    }

    /**
     * Float From
     * "There is no difference in PHP.
     * float, double or real are the same datatype.
     * At the C level, everything is stored as a double"
     * https://stackoverflow.com/a/3280927/639133
     * @param array $data
     * @param string $key
     * @return float|null Value of key if it exists
     */
    static function floatFrom(array $data, string $key) {
        if (array_key_exists($key, $data)) {
            if (is_null($data[$key])) {
                return null;
            }
            $type = gettype($data[$key]);
            if ($type === "string") {
                if (!is_numeric($data[$key])) {
                    throw new exceptions\UnprocessableEntity(
                        "value is not numeric");
                }
            }
            if ($type === "boolean") {
                throw new exceptions\UnprocessableEntity("value is a bool");
            }
            return (float)$data[$key];
        }
        // Missing properties parse as null
        return null;
    }

    /**
     * @param array $data
     * @param string $key
     * @return int|null Value of key if it exists
     */
    static function intFrom(array $data, string $key) {
        if (array_key_exists($key, $data)) {
            if (is_null($data[$key])) {
                return null;
            }
            $type = gettype($data[$key]);
            if ($type === "string") {
                if (!is_numeric($data[$key])) {
                    throw new exceptions\UnprocessableEntity(
                        "value is not numeric");
                }
            }
            if ($type === "boolean") {
                throw new exceptions\UnprocessableEntity("value is a bool");
            }
            return (int)$data[$key];
        }
        // Missing properties parse as null
        return null;
    }

    /**
     * Array From
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
