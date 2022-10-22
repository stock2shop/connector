<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

class Image extends Base
{
    public string $url;
    public ?string $id;

    public function __construct(array $data)
    {
        $this->id  = self::stringFrom($data, 'id');
        $this->url = self::stringFrom($data, 'url');
    }

    /**
     * @return Image[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $a[] = new Image((array)$item);
        }
        return $a;
    }
}
