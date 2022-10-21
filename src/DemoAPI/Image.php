<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use JetBrains\PhpStorm\Pure;
use Stock2Shop\Share;

class Image
{
    public string $url;
    public ?string $id;

    #[Pure] public function __construct(array $data)
    {
        $this->id  = self::stringFrom($data, 'id');
        $this->url = self::stringFrom($data, 'url');
    }

    public static function stringFrom(array $data, string $key): ?string
    {
        if (array_key_exists($key, $data)) {
            return (string)$data[$key];
        }
        return null;
    }

}
