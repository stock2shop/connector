<?php

namespace stock2shop\dal\channels\os;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\dal\channels\os\data\Helper;
use stock2shop\helpers;
use stock2shop\vo;

/**
 *
 * Read comments in stock2shop\dal\channel\Products()
 *
 * @package stock2shop\dal\os
 */
class Products implements ProductsInterface
{
    /** @const string DATA_PATH */
    const DATA_PATH = __DIR__ . '/data';

    /** @const string */
    const META_STORAGE_SEPARATOR = 'storage_separator';

    /**
     * Read comments stock2shop\dal\channel\Products::sync
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @param vo\Flag[] $flagsMap
     * @return vo\ChannelProduct[] $channelProducts
     */
    public function sync(array $channelProducts, vo\Channel $channel, array $flagsMap): array
    {

        /** @var string $storageSeparator Separator for channel codes and storage. */
        $storageSeparator = helpers\Meta::get($channel->meta, self::META_STORAGE_SEPARATOR);
        foreach ($channelProducts as $product) {

            // prefix used for file storage. All variants and images for a product are stored with
            // the same prefix
            $prefix = $product->id;

            // Set channel_product_code for each product.
            // In your integration, this would be the ID or code that the channel
            // uses to uniquely identify the product.
            $product->channel_product_code = $prefix . '.json';

            // Set channel_variant_code for each variant
            foreach ($product->variants as $variant) {
                $variant->channel_variant_code = $prefix . $storageSeparator . $variant->id . '.json';
            }

            // Set channel_image_code for each channel image.
            foreach ($product->images as $image) {
                $image->channel_image_code = $prefix . $storageSeparator . $storageSeparator . $image->id . '.json';
            }

            // Fetch existing products already saved to disk.
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, 'products');

            // Check if the product has been flagged for delete.
            // All products, variants and images are saved to disk with the same prefix.
            // remove them all
            if ($product->delete === true) {
                foreach ($currentFiles as $filename => $obj) {
                    $path = data\Helper::getDataPath() . '/products/' . $filename;
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            } else {
                $path = data\Helper::getDataPath() . '/products/' . $product->channel_product_code;
                file_put_contents($path, json_encode($product));
                foreach ($product->variants as $variant) {
                    $path = data\Helper::getDataPath() . '/products/' . $variant->channel_variant_code;
                    if ($variant->delete) {
                        if (file_exists($path)) {
                            unlink($path);
                        }
                    } else {
                        file_put_contents($path, json_encode($variant));
                    }
                    $variant->success = true;
                }
                foreach ($product->images as $image) {
                    $path = self::DATA_PATH . '/products/' . $image->channel_image_code;
                    if ($image->delete) {
                        if (file_exists($path)) {
                            unlink($path);
                        }
                    } else {
                        file_put_contents($path, json_encode($image));
                    }
                    $image->success = true;
                }
            }
            $product->success = true;
        }
        return $channelProducts;
    }

    /**
     *
     * Read comments in stock2shop\dal\channel\Products::get
     *
     * @param string $token
     * @param int $limit
     * @param vo\Channel $channel
     * @return vo\ChannelProductGet
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    public function get(string $token, int $limit, vo\Channel $channel): vo\ChannelProductGet
    {
        /** @var string $storageSeparator*/
        $storageSeparator = helpers\Meta::get($channel->meta, self::META_STORAGE_SEPARATOR);
        $channelProducts = [];
        $channelProductsData = [];
        $cnt = 0;

        // results are sorted by channel_product_code asc already
        $products = Helper::getJSONFiles('products');

        // build products, variants and images hierarchy
        foreach ($products as $filename => $data) {
            $parts = explode($storageSeparator, $filename);
            $prefix = str_replace('.json', '', $parts[0]);
            if ($prefix > $token) {
                if (count($parts) === 1) {
                    $channelProductsData[$prefix] = [
                        'channel_product_code' => $filename,
                        'success' => true,
                        'variants' => [],
                        'images' => []
                    ];
                } elseif (count($parts) === 2) {
                    $channelProductsData[$prefix]['variants'][] = [
                        'channel_variant_code' => $filename,
                        'sku' => $data['sku'],
                        'success' => true
                    ];
                } elseif (count($parts) === 3) {
                    $channelProductsData[$prefix]['images'][] = [
                        'channel_image_code' => $filename,
                        'success' => true
                    ];
                }
            }
        }
        foreach ($channelProductsData as $product) {
            if ($cnt < $limit) {
                $channelProducts[] = new vo\ChannelProduct($product);
            }
            $cnt++;
        }

        // If there are no products left, return the existing token
        return new vo\ChannelProductGet([
            'token' => end($channelProducts)->channel_product_code ?? $token,
            'channelProducts' => $channelProducts
        ]);
    }

    /**
     * Read comments in stock2shop\dal\channel\Products::getByCode
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[]
     */
    public function getByCode(array $channelProducts, vo\Channel $channel): array
    {
        foreach ($channelProducts as $product) {
            $productFiles = data\Helper::getJSONFilesByPrefix($product->id, 'products');
            foreach ($productFiles as $filename => $data) {
                if ($filename === $product->channel_product_code) {
                    $product->success = true;
                    break;
                }
            }
            foreach ($product->variants as $variant) {
                foreach ($productFiles as $filename => $data) {
                    if ($filename === $variant->channel_variant_code) {
                        $variant->success = true;
                        break;
                    }
                }
            }
            foreach ($product->images as $image) {
                foreach ($productFiles as $filename => $data) {
                    if ($filename === $image->channel_image_code) {
                        $image->success = true;
                        break;
                    }
                }
            }
        }
        return $channelProducts;
    }

}