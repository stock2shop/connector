<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\exceptions;
use stock2shop\lib;
use stock2shop\vo;

/**
 * See comments in ProductsInterface
 *
 * @package stock2shop\dal\example
 */
class Products implements ProductsInterface
{

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
        $cnt = 0;
        foreach ($channelProducts as $key => $product) {

            // Mark Product As Synced.
            // - 'channel_product_code' in this example is the product ID
            //    but this would be the unique identifier in your channel for the product.
            // - 'success' to true.
            $channelProducts[$cnt]->channel_product_code = (string)$product->id;
            $channelProducts[$cnt]->success = true;

            // Mark Variants As Synced.
            // - 'channel_variant_code' in this example is the variant ID.
            //   but this would be the unique identifier in your channel for the variant (e.g. sku).
            // - 'success' to true.
            foreach ($channelProducts[$cnt]->variants as $vKey => $variant) {
                $channelProducts[$cnt]->variants[$vKey]->channel_variant_code = (string)$variant->id;
                $channelProducts[$cnt]->variants[$vKey]->success = true;
            }

            // Mark Images As Synced.
            // - 'channel_image_code'.
            // - 'success' to true.
            foreach ($channelProducts[$cnt]->images as $ki => $img) {
                $channelProducts[$cnt]->images[$ki]->channel_image_code = (string)$img->id;
                $channelProducts[$cnt]->images[$ki]->success = true;
            }
            $cnt++;
        }
        return $channelProducts;
    }

    /**
     * See comments in ProductsInterface::get
     *
     * @param string $token
     * @param int $limit
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[] $channelProducts
     * @throws exceptions\NotImplemented
     */
    public function get(string $token, int $limit, vo\Channel $channel): array
    {
        throw new exceptions\NotImplemented();
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
        // Mimic fetching products from the channel.
        // in this example, we return true for all products
        // meaning all products are on the channel
        // In your channel, you would need to first verify the product
        // is actually on the channel before marking it as success = true.
        foreach ($channelProducts as $key => $product) {
            $channelProducts[$key]->channel_product_code = (string)$product->id;
            $channelProducts[$key]->success = true;
            if ($channelProducts[$key]->delete) {
                $channelProducts[$key]->success = false;
            }

            // Mark all variants as being on channel.
            foreach ($product->variants as $vKey => $variant) {
                $channelProducts[$key]->variants[$vKey]->channel_variant_code = (string)$variant->id;
                $channelProducts[$key]->variants[$vKey]->success = true;
                if ($channelProducts[$key]->variants[$vKey]->delete) {
                    $channelProducts[$key]->variants[$vKey]->success = false;
                }
            }

            // Mark all images as being on channel.
            foreach ($product->images as $ki => $img) {
                $channelProducts[$key]->images[$ki]->channel_image_code = (string)$img->id;
                $channelProducts[$key]->images[$ki]->success = true;
                if ($channelProducts[$key]->images[$ki]->delete) {
                    $channelProducts[$key]->images[$ki]->success = false;
                }
            }
        }
        return $channelProducts;
    }

}