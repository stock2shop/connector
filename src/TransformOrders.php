<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Connector\DemoAPI\Order;
use Stock2Shop\Share\DTO;

class TransformOrders
{
    /**
     * @param Order[] $demoOrders
     * @return DTO\ChannelOrder[]
     */
    public static function getChannelOrders(array $demoOrders, string|false $template): array
    {
        $channelOrders = [];
        foreach ($demoOrders as $do) {
            $co = null;

            if (!$template) {
                // if no template is provided we can just assign the values
                $co = self::getChannelOrder($do);
            } else {
                // if a template is provided we should use mustache to render the ChannelOrder
                $co = self::getChannelOrderTemplate($template, $do);
            }

            // set line items
            foreach ($do->line_items as $doli) {
                $co->line_items[] = new DTO\ChannelOrderLineItem([
                    'channel_variant_code' => $doli->id,
                    'barcode'              => null,
                    'grams'                => $do->weight,
                    'price'                => $doli->price - $doli->total_discount,
                    'qty'                  => $doli->qty,
                    'sku'                  => $doli->sku,
                    'title'                => $doli->name,
                    'total_discount'       => $doli->total_discount,
                    'tax_lines'            => [
                        [
                            'price' => $doli->price_with_tax + $doli->total_discount,
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

    private static function getChannelOrder(DemoAPI\Order $order): DTO\ChannelOrder
    {
        return new DTO\ChannelOrder([
            'channel_order_code' => $order->line_items[0]->sku,
            'total_discount'     => $order->discount_amount,
            'billing_address'    => [
                'address1'      => $order->billing_address->street,
                'city'          => $order->billing_address->city,
                'country_code'  => $order->billing_address->country_id,
                'first_name'    => $order->billing_address->firstname,
                'last_name'     => $order->billing_address->lastname,
                'phone'         => $order->billing_address->telephone,
                'province'      => $order->billing_address->region,
                'province_code' => $order->billing_address->region_id,
                'zip'           => $order->billing_address->postcode,
            ],
            'customer'           => [
                'accepts_marketing' => false,
                'email'             => $order->customer->email ?? null,
                'first_name'        => $order->customer->firstname ?? null,
                'last_name'         => $order->customer->lastname ?? null,
            ],
            'line_items'         => [],
            'meta'               => [],
            'shipping_address'   => [
                'address1'      => $order->shipping_address->street,
                'city'          => $order->shipping_address->city,
                'country_code'  => $order->shipping_address->country_id,
                'first_name'    => $order->shipping_address->firstname,
                'last_name'     => $order->shipping_address->lastname,
                'phone'         => $order->shipping_address->telephone,
                'province'      => $order->shipping_address->region,
                'province_code' => $order->shipping_address->region_id,
                'zip'           => $order->shipping_address->postcode,
            ],
            'shipping_lines'     => [
                [
                    'price'     => $order->base_total_paid - (int)$order->base_shipping_amount,
                    'title'     => $order->shipping_description,
                    'tax_lines' => [
                        [
                            'price' => $order->base_shipping_amount,
                            'title' => $order->line_items[0]->name,
                            'rate'  => $order->tax_amount,
                        ]
                    ]
                ]
            ]
        ]);
    }

    public static function getChannelOrderTemplate(string $template, DemoAPI\Order $demoOrder): DTO\ChannelOrder
    {
        $mustache = new \Mustache_Engine();
        // get order as an associative array
        $orderArr = json_decode(json_encode($demoOrder), true);
        $render   = $mustache->render($template, $orderArr);

        // get render as an associative array
        $renderArr       = json_decode($render, true);
        return new DTO\ChannelOrder($renderArr);
    }
}
