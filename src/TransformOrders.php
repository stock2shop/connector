<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Mustache_Engine;
use Stock2Shop\Connector\DemoAPI\Order;
use Stock2Shop\Share\DTO;

class TransformOrders
{
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
                'total_discount'     => $do->discount_amount,
                'billing_address'    => [
                    'address1'      => $do->billing_address->street,
                    'city'          => $do->billing_address->city,
                    'country_code'  => $do->billing_address->country_id,
                    'first_name'    => $do->billing_address->firstname,
                    'last_name'     => $do->billing_address->lastname,
                    'phone'         => $do->billing_address->telephone,
                    'province'      => $do->billing_address->region,
                    'province_code' => $do->billing_address->region_id,
                    'zip'           => $do->billing_address->postcode,
                ],
                'customer'           => [
                    'accepts_marketing' => false,
                    'email'             => $do->customer->email ?? null,
                    'first_name'        => $do->customer->firstname ?? null,
                    'last_name'         => $do->customer->lastname ?? null,
                ],
                'line_items'         => [],
                'meta'               => [],
                'shipping_address'   => [
                    'address1'      => $do->shipping_address->street,
                    'city'          => $do->shipping_address->city,
                    'country_code'  => $do->shipping_address->country_id,
                    'first_name'    => $do->shipping_address->firstname,
                    'last_name'     => $do->shipping_address->lastname,
                    'phone'         => $do->shipping_address->telephone,
                    'province'      => $do->shipping_address->region,
                    'province_code' => $do->shipping_address->region_id,
                    'zip'           => $do->shipping_address->postcode,
                ],
                'shipping_lines'     => [
                    [
                        'price'     => $do->base_total_paid - $do->base_shipping_amount,
                        'title'     => $do->line_items[0]->name,
                        'tax_lines' => [
                            [
                                'price' => $do->base_shipping_amount,
                                'title' => $do->line_items[0]->name,
                                'rate'  => $do->tax_amount,
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

    /**
     * @return DTO\ChannelOrder[]
     */
    public static function getChannelOrdersTemplate(string $template, array $demoOrder): array
    {
        $channelOrders = [];

        // create mustache engine to parse template
        $m = new Mustache_Engine();

        // apply template to each order and add resulting ChannelOrder to the return variable
        foreach ($demoOrder as $order) {
            // get order as an associative array
            $orderArr = json_decode(json_encode($order), true);
            $render = $m->render($template, $orderArr);

            // get render as an associative array
            $renderArr = json_decode($render, true);
            $channelOrders[] = new DTO\ChannelOrder($renderArr);
        }

        return $channelOrders;
    }

    public static function getChannelOrdersLineItems(string $template, array $lineItem): DTO\ChannelOrderLineItem
    {
        // create mustache engine to parse template
        $m = new Mustache_Engine();

        $render = $m->render($template, $lineItem);

        // get render as an associative array
        $renderArr = json_decode($render, true);
        return new DTO\ChannelOrderLineItem($renderArr);
    }
}
