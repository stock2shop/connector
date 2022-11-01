<?php

declare(strict_types=1);

namespace Stock2Shop\Tests\Connector\feature;

use GuzzleHttp\Client;
use Mustache_Engine;
use Stock2Shop\Connector\ChannelCreator;
use Stock2Shop\Connector\ChannelOrders;
use Stock2Shop\Connector\Config\Environment;
use Stock2Shop\Connector\Config\LoaderArray;
use Stock2Shop\Connector\DemoAPI\Payment;
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
        // create two webhooks for test run
        $wh1 = new DTO\ChannelOrderWebhook([
            'storage_code' => __DIR__ . '/../data/order1.json',
            'payload'      => file_get_contents(__DIR__ . '/../data/order1.json')
        ]);
        $wh2 = new DTO\ChannelOrderWebhook([
            'storage_code' => __DIR__ . '/../data/order2.json',
            'payload'      => file_get_contents(__DIR__ . '/../data/order2.json')
        ]);
        $hooks = [$wh1, $wh2];

        $co      = new ChannelOrders();
        $channel = new DTO\Channel($this->getTestDataChannel());
        $orders  = $co->transform($hooks, $channel);

        $this->assertCount(2, $orders);
        foreach ($orders as $index => $order) {
            $this->assertIsArray($order->meta);
            $this->assertIsArray($order->line_items);
            $this->assertIsArray($order->shipping_lines);
            $this->assertNotEmpty($order->channel_order_code);
            $this->assertNotEmpty($order->billing_address->address1);
            $this->assertFalse($order->customer->accepts_marketing);
            $this->assertNotEmpty($order->shipping_address->address1);
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

            // check that instruction has been set correctly
            $arr = json_decode($hooks[$index]->payload, true);
            if ($arr['state'] == DTO\ChannelOrder::ORDER_STATE_PROCESSING) {
                $this->assertEquals(DTO\ChannelOrder::INSTRUCTION_ADD_ORDER, $order->instruction);
            } else {
                $this->assertEmpty($order->instruction);
            }
        }
    }

    public function testCustomTransform()
    {
        // create two webhooks for test run
        $wh1 = new DTO\ChannelOrderWebhook([
            'storage_code' => __DIR__ . '/../data/order1.json',
            'payload'      => file_get_contents(__DIR__ . '/../data/order1.json')
        ]);
        $wh2 = new DTO\ChannelOrderWebhook([
            'storage_code' => __DIR__ . '/../data/order2.json',
            'payload'      => file_get_contents(__DIR__ . '/../data/order2.json')
        ]);
        $hooks = [$wh1, $wh2];

        $co               = new ChannelOrders();
        $channel          = new DTO\Channel($this->getTestDataChannel());
        $baseTemplate     = file_get_contents(__DIR__ . '/../data/channelOrderTemplate.json');
        $lineItemTemplate = file_get_contents(__DIR__ . '/../data/channelOrderLineItemTemplate.json');
        $channel->meta    = DTO\Meta::createArray(
            [
                [
                    'key'   => Meta::CHANNEL_ORDER_TEMPLATE,
                    'value' => $baseTemplate
                ],
                [
                    'key'   => Meta::CHANNEL_ORDER_LINE_ITEM_TEMPLATE,
                    'value' => $lineItemTemplate
                ]
            ]
        );
        $orders           = $co->transform([$wh1, $wh2], $channel);

        $this->assertCount(2, $orders);
        foreach ($orders as $index => $order) {
            $this->assertIsArray($order->meta);
            $this->assertIsArray($order->line_items);
            $this->assertIsArray($order->shipping_lines);
            $this->assertNotEmpty($order->billing_address->address1);
            $this->assertFalse($order->customer->accepts_marketing);
            $this->assertNotEmpty($order->shipping_address->address1);
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
                $this->assertNotEmpty($li->price);
                foreach ($li->tax_lines as $tl) {
                    $this->assertNotEmpty($tl->price);
                }
            }

            // check that instruction has been set correctly
            $arr = json_decode($hooks[$index]->payload, true);
            if ($arr['state'] == DTO\ChannelOrder::ORDER_STATE_PROCESSING) {
                $this->assertEquals(DTO\ChannelOrder::INSTRUCTION_ADD_ORDER, $order->instruction);
            } else {
                $this->assertEmpty($order->instruction);
            }
        }
    }
}
