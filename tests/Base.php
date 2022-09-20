<?php

declare(strict_types=1);

namespace Stock2Shop\Tests\Connector;

use PHPUnit\Framework\TestCase;

class Base extends TestCase
{
    protected function getTestDataChannel(): array
    {
        return $this->loadJSON('channel.json');
    }

    protected function getTestDataChannelFlagMap(): array
    {
        return $this->loadJSON('channelFlagMap.json');
    }

    protected function getTestDataChannelProducts(): array
    {
        return $this->loadJSON('channelProducts.json');
    }

    protected function getTestDataOrderTransform(): array
    {
        return $this->loadJSON('orderTransform.json');
    }

    protected function getTestDataChannelFulfillments(): array
    {
        return $this->loadJSON('channelFulfillments.json');
    }

    private function loadJSON(string $filename): array
    {
        $path = __DIR__ . '/data/' . $filename;
        return json_decode(file_get_contents($path), true);
    }
}
