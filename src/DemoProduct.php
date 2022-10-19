<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Share;

class DemoProduct
{
    public int $id;
    public string $name;
    /** @var DemoOption[]  */
    public array $options;
    /** @var DemoImage[]  */
    public array $images;

    public function __construct(array $data)
    {
    }

}
