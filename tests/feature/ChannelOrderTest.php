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
use Stock2Shop\Connector\TransformOrders;
use Stock2Shop\Share\Channel\ChannelProductsInterface;
use Stock2Shop\Share\DTO;
use Stock2Shop\Share\DTO\ChannelOrderAddress;
use Stock2Shop\Tests\Connector\Base;

final class ChannelOrderTest extends Base
{
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
            $this->assertNotEmpty($order->channel_order_code);
            $this->assertNotEmpty($order->billing_address->address1);
            $this->assertFalse($order->customer->accepts_marketing);
            $this->assertNotEmpty($order->shipping_address->address1);
            $this->assertEquals(TransformOrders::INSTRUCTION_SYNC_ORDER, $order->instruction);
            foreach ($order->shipping_lines as $sl) {
                $this->assertNotEmpty($sl->title);
                $this->assertIsArray($sl->tax_lines);
                foreach ($sl->tax_lines as $tl) {
                    $this->assertNotEmpty($tl->price);
                }
            }
            foreach ($order->line_items as $li) {
                $this->assertNotEmpty($li->channel_variant_code);
                $this->assertIsArray($li->tax_lines);
                foreach ($li->tax_lines as $tl) {
                    $this->assertNotEmpty($tl->price);
                }
            }
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

        $demoProducts = TransformOrders::getDemoOrders([$wh1, $wh2]);

        $this->assertCount(2, $orders);
        foreach ($orders as $index => $order) {
            // check that the custom field assignment has worked
            $this->assertEquals($demoProducts[$index]->protect_code, $order->channel_order_code);
            $this->assertIsArray($order->meta);
            $this->assertIsArray($order->line_items);
            $this->assertIsArray($order->shipping_lines);
            $this->assertNotEmpty($order->billing_address->address1);
            $this->assertFalse($order->customer->accepts_marketing);
            $this->assertNotEmpty($order->shipping_address->address1);
            $this->assertEquals(TransformOrders::INSTRUCTION_SYNC_ORDER, $order->instruction);
            foreach ($order->shipping_lines as $sl) {
                $this->assertNotEmpty($sl->title);
                $this->assertIsArray($sl->tax_lines);
                foreach ($sl->tax_lines as $tl) {
                    $this->assertNotEmpty($tl->price);
                }
            }
            foreach ($order->line_items as $li) {
                $this->assertNotEmpty($li->channel_variant_code);
                $this->assertIsArray($li->tax_lines);
                foreach ($li->tax_lines as $tl) {
                    $this->assertNotEmpty($tl->price);
                }
            }
        }
    }
}
