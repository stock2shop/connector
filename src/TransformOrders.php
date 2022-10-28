<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Mustache_Engine;
use Stock2Shop\Connector\DemoOrder\Order;
use Stock2Shop\Share\DTO;

class TransformOrders
{
    private const INSTRUCTION_SYNC_ORDER = "sync_order";

    /**
     * @param DTO\ChannelOrderWebhook[] $webHooks
     * @return Order[]
     */
    public static function getDemoOrders(array $webHooks): array
    {
        $orders = [];
        foreach ($webHooks as $wh) {
            // storage_code is a path to a local file for now.
            // in practice the order will need to be read from S3
            $data = file_get_contents($wh->storage_code);
            if ($data == false) {
                return [];
            }

            $orders[] = new Order(json_decode($data, true));
        }

        return $orders;
    }

    /**
     * @param Order[] $demoOrders
     * @return DTO\ChannelOrder[]
     */
    public static function getChannelOrders(array $demoOrders): array
    {
        $channelOrders = [];
        foreach ($demoOrders as $do) {
            $co = new DTO\ChannelOrder([
                'channel_order_code' => $do->line_items[0]->sku,
                'notes'              => $do->customer_note_notify,
                'total_discount'     => $do->discount_amount,
                'billing_address'    => [
                    'address1'      => $do->billing_address->street,
                    'address2'      => null,
                    'city'          => $do->billing_address->city,
                    'company'       => null,
                    'country'       => null,
                    'country_code'  => $do->billing_address->country_id,
                    'first_name'    => $do->billing_address->firstname,
                    'last_name'     => $do->billing_address->lastname,
                    'phone'         => $do->billing_address->telephone,
                    'province'      => $do->billing_address->region,
                    'province_code' => $do->billing_address->region_id,
                    'type'          => null,
                    'zip'           => $do->billing_address->postcode,
                ],
                'customer'           => [
                    'accepts_marketing' => null,
                    'email'             => $do->customer[0]->email ?? null,
                    'first_name'        => $do->customer[0]->firstname ?? null,
                    'last_name'         => $do->customer[0]->lastname ?? null,
                ],
                'instruction'        => self::INSTRUCTION_SYNC_ORDER,
                'line_items'         => [],
                'meta'               => [],
                'shipping_address'   => [
                    'address1'      => $do->shipping_address->street,
                    'address2'      => null,
                    'city'          => $do->shipping_address->city,
                    'company'       => null,
                    'country'       => null,
                    'country_code'  => $do->shipping_address->country_id,
                    'first_name'    => $do->shipping_address->firstname,
                    'last_name'     => $do->shipping_address->lastname,
                    'phone'         => $do->shipping_address->telephone,
                    'province'      => $do->shipping_address->region,
                    'province_code' => $do->shipping_address->region_id,
                    'type'          => null,
                    'zip'           => $do->shipping_address->postcode,
                ],
                'shipping_lines'     => [
                    [
                        'price'     => $do->shipping_amount,
                        'title'     => $do->line_items[0]->name,
                        'tax_lines' => [
                            [
                                'price' => $do->base_shipping_tax_amount,
                                'title' => $do->line_items[0]->name,
                                'rate'  => $do->base_shipping_tax_amount,
                            ]
                        ]
                    ]
                ]
            ]);

            foreach ($do->line_items as $doli) {
                $co->line_items[] = new DTO\ChannelOrderLineItem([
                    'channel_variant_code' => $doli->id,
                    'barcode'              => null,
                    'grams'                => $do->weight,
                    'price'                => $doli->price_with_discount_and_tax,
                    'qty'                  => $doli->qty,
                    'sku'                  => $doli->sku,
                    'title'                => $doli->name,
                    'total_discount'       => $doli->price - $doli->price_with_discount,
                    'tax_lines'            => [
                        [
                            'price' => $doli->price,
                            'title' => $doli->name,
                            'rate'  => $doli->tax_rate
                        ]
                    ]
                ]);
            }
            $channelOrders[] = $co;
        }

        return $channelOrders;
    }

    public static function getChannelOrdersTemplate(string $template, array $demoOrder): string
    {
        $m = new Mustache_Engine();

        $render = $m->render($template, $demoOrder);

        return $render;
    }
}
