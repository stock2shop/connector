<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

class Product
{
    public ?int $id;
    public string $name;
    /** @var Option[]  */
    public array $options;
    /** @var Image[]  */
    public array $images;

    public function __construct(array $data)
    {
        $this->id = self::intFrom($data, 'id');
        $this->name = self::stringFrom($data, 'name');
        $this->options = $data['options'];
        $this->images = $data['images'];
    }

    /**
     * @return Product[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $a[] = new Product((array)$item);
        }
        return $a;
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
