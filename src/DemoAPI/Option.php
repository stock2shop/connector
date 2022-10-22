<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

class Option extends Base
{
    public ?string $id;
    public string $sku;

    public function __construct(array $data)
    {
        $this->sku = self::stringFrom($data, 'sku');
        $this->id  = self::stringFrom($data, 'id');
    }

    /**
     * @return Option[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $a[] = new Option((array)$item);
        }
        return $a;
    }
}
