<?php

declare(strict_types=1);

namespace Stock2Shop\Tests\Connector\feature;

use GuzzleHttp\Client;
use Mustache_Engine;
use Stock2Shop\Connector\ChannelCreator;
use Stock2Shop\Connector\ChannelOrders;
use Stock2Shop\Connector\Config\Environment;
use Stock2Shop\Connector\Config\LoaderArray;
use Stock2Shop\Connector\Meta;
use Stock2Shop\Share\Channel\ChannelProductsInterface;
use Stock2Shop\Share\DTO;
use Stock2Shop\Tests\Connector\Base;

final class ChannelOrderTest extends Base
{
    /**
     * Feature test
     * Syncs product data to channel.
     * Uses get and getBy to ensure products exist on channel
     * Then runs same again with update and delete
     *
     * @return void
     */
    public function testDefaultTransform()
    {
        Environment::set(
            new LoaderArray([
                'LOG_CHANNEL'      => 'Share',
                'LOG_FS_DIR'       => sprintf('%s/../output/', __DIR__),
                'LOG_FS_FILE_NAME' => 'system.log'
            ])
        );

        $wh1 = new DTO\ChannelOrderWebhook([
            'storage_code' => __DIR__ . '/../data/order1.json'
        ]);
        $wh2 = new DTO\ChannelOrderWebhook([
            'storage_code' => __DIR__ . '/../data/order2.json'
        ]);

        $co      = new ChannelOrders();
        $channel = new DTO\Channel($this->getTestDataChannel());
        $orders  = $co->transform([$wh1, $wh2], $channel);

        $this->assertCount(2, $orders);
        foreach ($orders as $order) {
            $this->assertIsArray($order->meta);
            $this->assertIsArray($order->line_items);
            $this->assertIsArray($order->shipping_lines);
        }


    }

    public function testCustomTransform()
    {
        $wh1             = new DTO\ChannelOrderWebhook([
            'storage_code' => __DIR__ . '/../data/order1.json'
        ]);
        $wh2             = new DTO\ChannelOrderWebhook([
            'storage_code' => __DIR__ . '/../data/order2.json'
        ]);
        $co              = new ChannelOrders();
        $channel         = new DTO\Channel($this->getTestDataChannel());
        $channel->meta[] = new DTO\Meta([
            'key'   => 'order_transform_channel_order_code',
            'value' => '{{ protect_code }}'
        ]);
        $orders          = $co->transform([$wh1, $wh2], $channel);

        $this->assertCount(2, $orders);
        foreach ($orders as $order) {
            $this->assertIsArray($order->meta);
            $this->assertIsArray($order->line_items);
            $this->assertIsArray($order->shipping_lines);
        }
    }
}
