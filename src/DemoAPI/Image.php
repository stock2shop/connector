<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

class Image
{
    public int $url;
    public string $id;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->id = $data['url'];
    }

}
