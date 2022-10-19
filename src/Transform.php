<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Share\DTO;

class Transform
{
    public static function DtoToDemoProduct(DTO\ChannelProducts $channelProducts): DemoProduct
    {
    }

    /**
     * @param DemoProduct[] $demoProducts
     */
    public static function DemoProductToDto(array $demoProducts): DTO\ChannelProducts
    {
    }
}
