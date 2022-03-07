<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\vo;
use stock2shop\lib;
use stock2shop\helpers;
use stock2shop\exceptions;

/**
 * Products
 *
 * This class is where the Data Access Layer is mapped onto
 * the Stock2Shop Value Objects from the source system you are
 * integrating with.
 *
 * @package stock2shop\dal\example
 */
class Products implements ProductsInterface
{

    /**
     * Sync
     *
     * This method synchronises products, variants and images on the channel.
     * This is a simple example for marking entities after they have been
     * synchronised to meet Stock2Shop's requirements.
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @param vo\Flag[] $flagsMap
     * @return vo\ChannelProduct[] $channelProducts
     */
    public function sync(array $channelProducts, vo\Channel $channel, array $flagsMap): array
    {
        foreach ($channelProducts as $key => &$product) {

            // Mark Product As Synced.

            // - 'channel_product_code' to the product ID.
            // - 'success' to true.
            // - 'synced' to the current timestamp.

            $channelProducts[$key]->channel_product_code = (string)$product->id;
            $channelProducts[$key]->success = true;

            // Mark Variants As Synced.

            // - 'channel_variant_code' to the variant ID.
            // - 'success' to true.

            foreach ($product->variants as $vKey => $variant) {
                $channelProducts[$key]->variants[$vKey]->channel_variant_code = (string)$variant->id;
                $channelProducts[$key]->variants[$vKey]->success = true;
            }

            // Mark Images As Synced.

            // - 'channel_image_code' to the image ID.
            // - 'success' to true.

            foreach ($product->images as $ki => $img) {
                $channelProducts[$key]->images[$ki]->channel_image_code = (string)$img->id;
                $channelProducts[$key]->images[$ki]->success = true;
            }
        }
        return $channelProducts;
    }

    /**
     * Get
     *
     * This method implements the get() method from the dal\channel\Products interface class.
     * Use this method to structure the integration you are coding according to Stock2Shop's
     * requirements.
     *
     * You will use the vo\ChannelProductGet class here to associate a token value with each
     * product. We use the tokens in our system to determine the last product returned from
     * the channel (like a 'cursor').
     *
     * @param string $token
     * @param int $limit
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[] $channelProducts
     */
    public function get(string $token, int $limit, vo\Channel $channel): array
    {
        throw new exceptions\NotImplemented();
    }

    /**
     * Get By Code
     *
     * This method returns vo\ChannelProduct items by code.
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[]
     */
    public function getByCode(array $channelProducts, vo\Channel $channel): array
    {
        // Products.
        foreach ($channelProducts as $key => $product) {
            $channelProducts[$key]->channel_product_code = (string)$product->id;
            $channelProducts[$key]->success = true;
            if ($channelProducts[$key]->delete === true) {
                $channelProducts[$key]->success = false;
            }

            // Variants.
            foreach ($product->variants as $vKey => $variant) {
                $channelProducts[$key]->variants[$vKey]->channel_variant_code = (string)$variant->id;
                $channelProducts[$key]->variants[$vKey]->success = true;
                if ($channelProducts[$key]->variants[$vKey]->delete === true) {
                    $channelProducts[$key]->variants[$vKey]->success = false;
                }
            }

            // Images.
            foreach ($product->images as $ki => $img) {
                $channelProducts[$key]->images[$ki]->channel_image_code = (string)$img->id;
                $channelProducts[$key]->images[$ki]->success = true;
                if ($channelProducts[$key]->images[$ki]->delete === true) {
                    $channelProducts[$key]->images[$ki]->success = false;
                }
            }
        }
        return $channelProducts;
    }

}