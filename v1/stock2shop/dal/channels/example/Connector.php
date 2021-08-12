<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel;
use stock2shop\vo\ChannelProduct;
use stock2shop\vo\ChannelProductGet;
use stock2shop\vo\ChannelVariant;
use stock2shop\vo\MetaItem;
use stock2shop\vo\Order;
use stock2shop\vo\OrderLineItem;
use stock2shop\vo\SyncChannelProducts;

class Connector implements channel\Connector
{

    const DATA_PATH = __DIR__ . '/data';

    /**
     * Creates a file for each product and for each variant.
     * This illustrates possible cleanup operations required for
     * e-commerce channels
     *
     * The channel_product_code is the file name for the product
     * The channel_variant_code is the file name for the variant
     *
     * @param SyncChannelProducts $params
     * @return SyncChannelProducts
     * @throws \Exception
     */
    public function syncProducts(SyncChannelProducts $params): SyncChannelProducts
    {
        // Example on how to load channel meta
        // Separator is used when creating variant file names
        $map       = MetaItem::getMap($params->meta);
        $separator = $map['separator'];
        foreach ($params->channel_products as $product) {
            $prefix          = urlencode($product->source_product_code);
            $productFileName = $prefix . '.json';

            // create channel codes for product and variants
            $product->channel_product_code = $productFileName;
            foreach ($product->variants as $variant) {
                $variant->channel_variant_code = $prefix . $separator . urlencode($variant->sku) . '.json';
            }

            // fetch current files for this prefix
            $currentFiles = $this->getJSONFilesByPrefix($prefix);

            // Remove product / variants if delete true
            if ($product->delete) {
                foreach ($currentFiles as $currentFileName => $obj) {
                    unlink(self::DATA_PATH . '/' . $currentFileName);
                }
            } else {

                // create / update product
                file_put_contents(self::DATA_PATH . '/' . $product->channel_product_code, json_encode($product));

                // Create / update variants
                $variantsToKeep = [];
                foreach ($product->variants as $variant) {
                    $filePath = self::DATA_PATH . '/' . $variant->channel_variant_code;
                    if ($product->delete) {
                        unlink($filePath);
                    } else {
                        file_put_contents($filePath, json_encode($variant));
                        array_push($variantsToKeep, $variant->channel_variant_code);
                    }
                }

                // Remove old variants
                foreach ($currentFiles as $fileName => $obj) {
                    if (!in_array($fileName, $variantsToKeep) && strpos($fileName, $separator) !== false) {
                        unlink(self::DATA_PATH . '/' . $fileName);
                    }
                }
            }

            // Mark products and variants as successfully synced
            $date             = new \DateTime();
            $product->success = true;
            $product->synced  = $date->format('Y-m-d H:i:s');
            foreach ($product->variants as $variant) {
                $variant->success = true;
            }
        }
        return $params;
    }

    /**
     * @param SyncChannelProducts $params
     * @return SyncChannelProducts
     */
    public function getProductsByCode(SyncChannelProducts $params): SyncChannelProducts
    {
        $channelProducts = [];
        foreach ($params->channel_products as $product) {
            $prefix       = urlencode($product->source_product_code);
            $currentFiles = $this->getJSONFilesByPrefix($prefix);
            foreach ($currentFiles as $fileName => $obj) {
                if ($fileName === $prefix . '.json') {

                    // This is a Product
                    $channelProducts[] = new ChannelProduct([
                        "channel_product_code" => $obj->channel_product_code
                    ]);
                } else {

                    // This is a Variant
                    $channelProducts[count($channelProducts) - 1]->variants[] = new ChannelVariant(
                        [
                            "sku"                  => $obj->sku,
                            "channel_variant_code" => $obj->channel_variant_code
                        ]
                    );
                }
            }
        }
        $params->channel_products = $channelProducts;
        return $params;
    }

    /**
     * @param string $token
     * @param int $limit
     * @param MetaItem[] $meta
     * @return ChannelProductGet[]
     */
    public function getProducts(string $token, int $limit, array $meta): array
    {
        $map             = MetaItem::getMap($meta);
        $separator       = $map['separator'];
        $channelProducts = [];
        $currentFiles    = $this->getJSONFiles();

        // Create paged results
        $cnt = 1;
        foreach ($currentFiles as $fileName => $obj) {
            if (strcmp($token, $fileName) < 0) {
                if (strpos($fileName, $separator) === false) {

                    // This a product
                    if ($cnt > $limit) {
                        break;
                    }
                    $channelProducts[] = new ChannelProductGet([
                        "channel_product_code" => $obj->channel_product_code
                    ]);
                    $cnt++;

                } else {

                    // This is a variant
                    $channelProducts[count($channelProducts) - 1]->variants[] = new ChannelVariant(
                        [
                            "sku"                  => $obj->sku,
                            "channel_variant_code" => $obj->channel_variant_code
                        ]
                    );
                    $channelProducts[count($channelProducts) - 1]->token      = $obj->channel_variant_code;
                }

            }
        }
        return $channelProducts;
    }

    public function getOrders(int $page, int $limit): array
    {
        // TODO: Implement getOrders() method.
    }

    public function getOrdersByCode(): array
    {
        // TODO: Implement getOrdersByCode() method.
    }

    public function transformOrder(\stdClass $webhookOrder, array $meta): Order
    {
        $order                       = new Order([]);
        $order->notes                = $webhookOrder->instructions;
        $order->channel_order_code   = $webhookOrder->order_number;
        $order->customer->first_name = $webhookOrder->customer['name'];
        $order->customer->email      = $webhookOrder->customer['email'];
        foreach ($webhookOrder->items as $item) {
            $order->line_items[] = new OrderLineItem([
                'sku'   => $item['sku'],
                'qty'   => $item['qty'],
                'price' => $item['price']
            ]);
        }
        return $order;
    }

    /**
     * @param string $prefix
     * @return array
     */
    private function getJSONFilesByPrefix(string $prefix): array
    {
        $files = $this->getJSONFiles();
        $items = [];
        foreach ($files as $fileName => $obj) {
            if (strpos($fileName, $prefix) === 0) {
                $items[$fileName] = $obj;
            }
        }
        return $items;
    }

    /**
     * @return array
     */
    private function getJSONFiles(): array
    {
        $files     = [];
        $fileNames = array_diff(scandir(self::DATA_PATH, SCANDIR_SORT_ASCENDING), array('..', '.'));
        sort($fileNames);
        foreach ($fileNames as $file) {
            if (substr($file, -5) === '.json') {
                $contents     = file_get_contents(self::DATA_PATH . '/' . $file);
                $files[$file] = json_decode($contents);
            }
        }
        return $files;
    }

}
