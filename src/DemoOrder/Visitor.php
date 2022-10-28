<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoOrder;

use Stock2Shop\Share;

class Visitor extends Base
{
    public ?string $http_user_agent;
    public ?string $remote_addr;

    public function __construct(array $data)
    {
        $this->http_user_agent = self::stringFrom($data, 'http_user_agent');
        $this->remote_addr     = self::stringFrom($data, 'remote_addr');
    }

    /**
     * @return Visitor[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $a[] = new Visitor((array)$item);
        }
        return $a;
    }
}
