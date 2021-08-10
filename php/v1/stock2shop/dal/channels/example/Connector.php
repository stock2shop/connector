<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel;
use stock2shop\vo\ChannelProduct;
use stock2shop\vo\ChannelVariant;
use stock2shop\vo\MetaItem;
use stock2shop\vo\SyncChannelProducts;

class Connector implements channel\Connector
{

    const SEPARATOR = '~';

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
        $map = MetaItem::getMap($params->meta);
        $path = $map['file.path'];

        foreach ($params->channel_products as $product) {
            $prefix = urlencode($product->source_product_code);
            $productFileName =  $prefix . '.json';

            // create channel codes for product and variants
            $product->channel_product_code = $productFileName;
            foreach ($product->variants as $variant) {
                $variant->channel_variant_code = $prefix . self::SEPARATOR . urlencode($variant->sku) . '.json';
            }

            // fetch current files for this prefix
            $currentFiles = $this->getJSONFilesByPrefix($path, $prefix);

            // Remove product / variants if delete true
            if ($product->delete) {
                foreach ($currentFiles as $currentFileName => $obj) {
                    unlink($path . '/' . $currentFileName);
                }
            } else {

                // create / update product
                file_put_contents($path . '/' . $product->channel_product_code, json_encode($product));

                // Create / update variants
                $variantsToKeep = [];
                foreach ($product->variants as $variant) {
                    $filePath = $path . '/' . $variant->channel_variant_code;
                    if ($product->delete) {
                        unlink($filePath);
                    } else {
                        file_put_contents($filePath, json_encode($variant));
                        array_push($variantsToKeep, $variant->channel_variant_code);
                    }
                }

                // Remove old variants
                foreach ($currentFiles as $fileName => $obj) {
                    if(!in_array($fileName, $variantsToKeep) && strpos($fileName, self::SEPARATOR) !== false) {
                        unlink($path . '/' . $fileName);
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
        $map = MetaItem::getMap($params->meta);
        $path = $map['file.path'];
        $channelProducts = [];
        $channelVariants = [];
        foreach ($params->channel_products as $product) {
            $prefix = urlencode($product->source_product_code);
            $currentFiles = $this->getJSONFilesByPrefix($path, $prefix);
            foreach ($currentFiles as $fileName => $obj) {
                if($fileName === $prefix . '.json') {
                    // This is a Product
                    $channelProduct = new ChannelProduct([
                        "channel_product_code" => $obj->channel_product_code
                    ]);
                } else {

                    // This is a Variant
                    $channelVariants[] = new ChannelVariant(
                        [
                            "sku" => $obj->sku,
                            "channel_variant_code" => $obj->channel_variant_code
                        ]
                    );
                }
            }
            $channelProduct->variants = $channelVariants;
            $channelProducts[] = $channelProduct;
        }
        $params->channel_products = $channelProducts;
        return $params;
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

    /**
     * @param string $path
     * @param string $prefix
     * @return array
     */
    private function getJSONFilesByPrefix(string $path, string $prefix): array
    {
        $files = $this->getJSONFiles($path);
        $items = [];
        foreach ($files as $fileName => $obj) {
            if (strpos($fileName, $prefix) === 0) {
                $items[$fileName] = $obj;
            }
        }
        return $items;
    }

    /**
     * @param string $path
     * @return array
     */
    private function getJSONFiles(string $path): array
    {
        $files     = [];
        $fileNames = array_diff(scandir($path), array('..', '.'));
        foreach ($fileNames as $file) {
            if (substr($file, -5) === '.json') {
                $contents     = file_get_contents($file);
                $files[$file] = json_decode($contents);
            }
        }
        return $files;
    }

}
