<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel;
use stock2shop\vo;
use stock2shop\vo\SyncChannelProducts;

class Connector implements channel\Connector
{

    /**
     * Write products to file and mark them as complete
     *
     * @param SyncChannelProducts $params
     * @return SyncChannelProducts
     * @throws \Exception
     */
    function syncProducts(vo\SyncChannelProducts $params): SyncChannelProducts
    {
        $map = vo\MetaItem::getMap($params->meta);
        $path = $map['file.path'];
        foreach ($params->channel_products as $product) {
            $name                          = $path . '/' . $product->source_product_code . '.json';
            $product->channel_product_code = 'c-' . $product->source_product_code;
            foreach ($product->variants as $variant) {
                $variant->channel_variant_code = 'c-' . $variant->source_variant_code;
                $variant->success              = true;
            }
            if ($product->delete) {
                unlink($name);
            } else {
                file_put_contents($name, json_encode($product));
            }
            $date             = new \DateTime();
            $product->success = true;
            $product->synced  = $date->format('c');
        }
        return $params;
    }

    /**
     * @param array|vo\ChannelProduct $channelProducts
     * @return array|vo\ChannelProduct[]
     */
    public function getProductsByCode(array $channelProducts): array
    {
        $map = [];
        foreach ($channelProducts as $product) {
            $map[$product->channel_product_code] = $product;
        }
        $path     = channel\Meta::get('file.path');
        $files    = scandir($path);
        $products = [];
        foreach ($files as $file) {
            if (substr($file, -5) === '.json') {
                $contents = file_get_contents($file);
                $product  = json_decode($contents);
                if (isset($map[$product->channel_product_code])) {
                    $channelProduct = new vo\ChannelProduct([
                        'channel_product_code' => $product->channel_product_code
                    ]);
                    foreach ($product->variants as $variant) {
                        $channelProduct->variants[] = new vo\ChannelVariant([
                            'channel_variant_code' => $variant->channel_variant_code
                        ]);
                    }
                    $products[] = $channelProduct;
                }
            }
        }
        return $products;
    }

    /**
     * @param int $page
     * @param int $limit
     * @return array|vo\ChannelProduct[]
     */
    public function getProducts(int $page, int $limit): array
    {
        $path     = channel\Meta::get('file.path');
        $files    = scandir($path);
        $products = [];
        foreach ($files as $file) {
            if (substr($file, -5) === '.json') {
                $contents       = file_get_contents($file);
                $product        = json_decode($contents);
                $channelProduct = new vo\ChannelProduct([
                    'channel_product_code' => $product->channel_product_code
                ]);
                foreach ($product->variants as $variant) {
                    $channelProduct->variants[] = new vo\ChannelVariant([
                        'channel_variant_code' => $variant->channel_variant_code
                    ]);
                }
                $products[] = $channelProduct;
            }
        }
        return $products;
    }

    public function getOrders(int $page, int $limit): array
    {
        // TODO: Implement getOrders() method.
    }

    public function getOrdersByCode(): array
    {
        // TODO: Implement getOrdersByCode() method.
    }

    public function transformOrder(\stdClass $channelOrder)
    {
        // TODO: Implement transformOrder() method.
    }

}
