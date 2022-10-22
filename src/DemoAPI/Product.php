<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

class Product extends Base
{
    public ?string $id;
    public string $name;
    /** @var Option[] */
    public array $options;
    /** @var Image[] */
    public array $images;

    public function __construct(array $data)
    {
        $options = self::arrayFrom($data, 'options');
        $images  = self::arrayFrom($data, 'images');

        $this->id      = self::stringFrom($data, 'id');
        $this->name    = self::stringFrom($data, 'name');
        $this->options = Option::createArray($options);
        $this->images  = Image::createArray($images);
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
}
