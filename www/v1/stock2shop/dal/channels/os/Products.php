<?php

namespace stock2shop\dal\channels\os;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\dal\channels\os\data\Helper;
use stock2shop\helpers;
use stock2shop\vo;

/**
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
     * See comments in ProductsInterface::sync
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @param vo\Flag[] $flagsMap
     * @return vo\ChannelProduct[] $channelProducts
     */
    public function sync(array $channelProducts, vo\Channel $channel, array $flagsMap): array
    {

        /** @var string $imageSeparator Channel separator for channel codes and storage. */
        $storageSeparator = helpers\Meta::get($channel->meta, self::META_STORAGE_SEPARATOR);

        // ------------------------------------------------

        // Iterate through the channel products.
        foreach ($channelProducts as $product) {
            $prefix = $product->id;

            // ------------------------------------------------

            // Create channel_product_code for each product from the file name.
            // In your integration, this would be the ID or code that the target
            // system uses to uniquely identify the product.
            // i.e. in WooCommerce this would be the post ID of the product.
            $product->channel_product_code = $prefix . '.json';
            foreach ($product->variants as $variant) {
                $variant->channel_variant_code = $prefix . $storageSeparator . $variant->id . '.json';
            }

            // ------------------------------------------------

            // Do the same as the loop above to set the channel_image_code for each channel image.
            foreach ($product->images as $image) {
                $image->channel_image_code = $prefix . $storageSeparator . $storageSeparator . $image->id . '.json';
            }

            // ------------------------------------------------

            // Fetch the current files from the source (in this case, flat-file).
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, 'products');

            // Check if the product has been flagged for delete.
            if ($product->delete === true) {
                foreach ($currentFiles as $filename => $obj) {
                    $fullpath = data\Helper::getDataPath() . '/products/' . $filename;
                    if (file_exists($fullpath)) {
                        unlink($fullpath);
                    }
                }
            } else {
                $filename = data\Helper::getDataPath() . '/products/' . $product->channel_product_code;
                file_put_contents($filename, json_encode($product));
                foreach ($product->variants as $variant) {
                    $filename = data\Helper::getDataPath() . '/products/' . $variant->channel_variant_code;
                    if ($variant->delete) {
                        if (file_exists($filename)) {
                            unlink($filename);
                        }
                    } else {
                        file_put_contents($filename, json_encode($variant));
                    }
                }

                // Iterate through the product images.
                foreach ($product->images as $image) {
                    $filename = self::DATA_PATH . '/products/' . $image->channel_image_code;
                    if ($image->delete) {
                        if (file_exists($filename)) {
                            unlink($filename);
                        }
                    } else {
                        file_put_contents($filename, json_encode($image));
                    }
                }
            }

            // Mark product as successfully synced.
            $product->success = true;
            foreach ($product->variants as $variant) {
                $variant->success = true;
            }
            foreach ($product->images as $image) {
                $image->success = true;
            }
        }
        return $channelProducts;
    }

    /**
     *
     * See comments in ProductsInterface::get
     *
     * @param string $token only return results greater than this
     * @param int $limit max records to return
     * @param vo\Channel $channel
     * @return vo\ChannelProductGet $channelProductGet
     */
    public function get(string $token, int $limit, vo\Channel $channel): vo\ChannelProductGet
    {
        /** @var string $imageSeparator Channel separator for channel codes and storage. */
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

        return new vo\ChannelProductGet([

            // We are using end() here to set the pointer in the
            // "channelProducts" array to the end of the array.
            // This gives us the last channel product returned.
            // However, the product might not exist (i.e. we've
            // already returned the last product on the prev. page).
            // If this is the case, then we return the same token
            // so that the worker knows that the "token" value was
            // the end of the items on the channel.
            'token' => end($channelProducts)->channel_product_code ?? $token,

            // "channelProducts" may be an empty array or
            // an array of ($limit) products.
            'channelProducts' => $channelProducts
        ]);
    }

    /**
     * See comments in ProductsInterface::getByCode
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[]
     */
    public function getByCode(array $channelProducts, vo\Channel $channel): array
    {
        $productsToRemove = [];
        $imagesToRemove = [];
        $variantsToRemove = [];
        foreach ($channelProducts as $product) {
            $productFiles = data\Helper::getJSONFilesByPrefix($product->id, 'products');
            $hasProduct = false;
            foreach ($productFiles as $filename => $data) {
                if ($filename === $product->channel_product_code) {
                    $hasProduct = true;
                    break;
                }
            }
            if (!$hasProduct) {
                array_push($productsToRemove, $product->channel_product_code);
            }
            foreach ($product->variants as $variant) {
                $hasVariant = false;
                foreach ($productFiles as $filename => $data) {
                    if ($filename === $variant->channel_variant_code) {
                        $hasVariant = true;
                        break;
                    }
                }
                if (!$hasVariant) {
                    array_push($variantsToRemove, $variant->channel_variant_code);
                }
            }
            foreach ($product->images as $image) {
                $hasImage = false;
                foreach ($productFiles as $filename => $data) {
                    if ($filename === $image->channel_image_code) {
                        $hasImage = true;
                        break;
                    }
                }
                if (!$hasImage) {
                    array_push($imagesToRemove, $image->channel_image_code);
                }
            }
        }
        // remove products, images and variants not on channel.
        foreach ($channelProducts as $key => $product) {
            foreach ($product->variants as $vk => $variant) {
                if (in_array($variant->channel_variant_code, $variantsToRemove)) {
                    unset($product->variants[$vk]);
                }
            }
            foreach ($product->images as $ik => $image) {
                if (in_array($image->channel_image_code, $imagesToRemove)) {
                    unset($product->images[$ik]);
                }
            }
            if (in_array($product->channel_product_code, $productsToRemove)) {
                unset($channelProducts[$key]);
            }
        }

        return $channelProducts;
    }

}