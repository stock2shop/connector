<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel;
use stock2shop\vo\ChannelOrder;
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
     * product.id is the file name for the product
     * product.variant[].channel_variant_code is the file name for the variant
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
            $prefix          = urlencode($product->id);
            $productFileName = $prefix . '.json';

            // create channel codes for product and variants
            $product->channel_product_code = $productFileName;
            foreach ($product->variants as $variant) {
                $variant->channel_variant_code = $prefix . $separator . urlencode($variant->sku) . '.json';
            }

            // fetch current files for this prefix
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, "products");

            // Remove product / variants if delete true
            if ($product->delete) {
                foreach ($currentFiles as $currentFileName => $obj) {
                    unlink(self::DATA_PATH . '/products/' . $currentFileName);
                }
            } else {

                // create / update product
                file_put_contents(self::DATA_PATH . '/products/' . $product->channel_product_code, json_encode($product));

                // Create / update variants
                $variantsToKeep = [];
                foreach ($product->variants as $variant) {
                    $filePath = self::DATA_PATH . '/products/' . $variant->channel_variant_code;
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
                        unlink(self::DATA_PATH . '/products/' . $fileName);
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
            $prefix       = urlencode($product->id);
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, "products");
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
        $response                   = new SyncChannelProducts([]);
        $response->channel_products = $channelProducts;
        return $response;
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
        $currentFiles    = data\Helper::getJSONFiles("products");

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

    public function getOrders(string $token, int $limit, array $meta): array
    {
        $currentFiles  = data\Helper::getJSONFiles("orders");
        $channelOrders = [];
        $cnt           = 1;
        foreach ($currentFiles as $fileName => $obj) {
            if (strcmp($token, $fileName) < 0) {
                if ($cnt > $limit) {
                    break;
                }
                $orderJSON       = json_encode($obj);
                $order           = json_decode($orderJSON, true);
                $channelOrders[] = $this->transformOrder($order, $meta);
                $cnt++;
            }
        }
        return $channelOrders;
    }

    /**
     * @param ChannelOrder[] $orders
     * @param array $meta
     * @return array
     */
    public function getOrdersByCode(array $orders, array $meta): array
    {
        $currentFiles  = data\Helper::getJSONFiles("orders");
        $channelOrders = [];
        foreach ($orders as $order) {
            $fileName = $order->channel_order_code . ".json";
            if (array_key_exists($fileName, $currentFiles)) {
                $orderJSON       = json_encode($currentFiles[$fileName]);
                $order           = json_decode($orderJSON, true);
                $channelOrders[] = $this->transformOrder($order, $meta);
            }
        }
        return $channelOrders;
    }

    /**
     * @param mixed $webhookOrder
     * @param array $meta
     * @return ChannelOrder
     */
    public function transformOrder($webhookOrder, array $meta): ChannelOrder
    {
        $order                       = new ChannelOrder([]);
        $order->notes                = $webhookOrder['instructions'];
        $order->channel_order_code   = $webhookOrder['order_number'];
        $order->customer->first_name = $webhookOrder['customer']['name'];
        $order->customer->email      = $webhookOrder['customer']['email'];
        foreach ($webhookOrder['items'] as $item) {
            $order->line_items[] = new OrderLineItem([
                'sku'                  => $item['sku'],
                'qty'                  => $item['qty'],
                'price'                => $item['price'],
                'channel_variant_code' => $item['sku']
            ]);
        }
        return $order;
    }

}
