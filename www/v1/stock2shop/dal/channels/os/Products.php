<?php

namespace stock2shop\dal\channels\os;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\dal\channels\os\data\Helper;
use stock2shop\exceptions;
use stock2shop\helpers;
use stock2shop\vo;

/**
 *
 * @package stock2shop\dal\os
 */
class Products implements ProductsInterface
{
    /** @const string DATA_PATH */
    const DATA_PATH = __DIR__ . "/data";

    /** @const string */
    const META_STORAGE_SEPARATOR = "storage_separator";

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
            foreach ($product->images as &$image) {
                $image->channel_image_code = $prefix . $storageSeparator . $image->id . '.json';
            }

            // ------------------------------------------------

            // Fetch the current files from the source (in this case, flat-file).
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, 'products');

            // Check if the product has been flagged for delete.
            if ($product->delete === true) {
                foreach ($currentFiles as $filename => $obj) {
                    $fullpath = data\Helper::getDataPath() . '/products/' . $filename;
                    if(file_exists($fullpath)) {
                        unlink($fullpath);
                    }
                }
            } else {
                $filename = data\Helper::getDataPath() . '/products/' . $product->channel_product_code;
                file_put_contents($filename, json_encode($product));
                foreach ($product->variants as $variant) {
                    $filename = data\Helper::getDataPath() . '/products/' . $variant->channel_variant_code;
                    if ($variant->delete) {
                        if(file_exists($filename)) {
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
                        if(file_exists($filename)) {
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
            foreach ($product->images as &$image) {
                $image->success = true;
            }
        }
        return $channelProducts;
    }

    /**
     *
     * See comments in ProductsInterface::get
     *
     * @param string $token
     * @param int $limit
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[] $channelProducts
     */
    public function get(string $token, int $limit, vo\Channel $channel): array
    {
//        /** @var string $imageSeparator Channel separator for channel codes and storage. */
//        $storageSeparator = helpers\Meta::get($channel->meta, self::META_STORAGE_SEPARATOR);
//        $currentFiles = Helper::getJSONFiles($productsEndpointMeta);
//
//        // ------------------------------------------------
//
//        /** @var  $currentFiles */
//
//        $cnt = 0;
//        $channelProducts = [];
//        foreach ($currentFiles as $fileName => $fileData) {
//
//            // ------------------------------------------------
//
//            // Compare Products With Token
//
//            // The token is used to determine which products to add.
//            // We use strcmp() below to do the comparison.
//
//            if (strcmp($token, $fileName) < 0) {
//
//                // Variant check.
//                if(strcmp($variantSeparator, $fileName) < 0) {
//                    $channelProducts[$fileName]->variants[] = new vo\ChannelVariant($fileData);
//                }
//
//                // Image check.
//                if(strcmp($imageSeparator, $fileName) < 0) {
//                    $channelProducts[$fileName]->images[] = new vo\ChannelImage($fileData);
//                }
//
//                // product prefix:   [^[0-9]{5}]   include all numbers up to 5 characters at the start of the string.
//                if(preg_match('/^[0-9]{5}.json/', $fileName)) {
//
//                    // Check that we have not reached the limit.
//                    if ($cnt > $limit) {
//                        break;
//                    }
//
//                    // Create new vo\ChannelProduct using the VO and add to the array.
//                    $channelProducts[$fileName] = new vo\ChannelProduct($fileData);
//                    $cnt++;
//                }
//            }
//        }
        return [];
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
            if(!$hasProduct) {
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
                if(!$hasVariant) {
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
                if(!$hasImage) {
                    array_push($imagesToRemove, $image->channel_image_code);
                }
            }
        }
        // remove products, images and variants not on channel.
        foreach ($channelProducts as $key => $product) {
            foreach ($product->variants as $vk => $variant) {
                if(in_array($variant->channel_variant_code, $variantsToRemove)) {
                    unset($product->variants[$vk]);
                }
            }
            foreach ($product->images as $ik => $image) {
                if(in_array($image->channel_image_code, $imagesToRemove)) {
                    unset($product->images[$ik]);
                }
            }
            if(in_array($product->channel_product_code, $productsToRemove)) {
                unset($channelProducts[$key]);
            }
        }

        return $channelProducts;
    }

}