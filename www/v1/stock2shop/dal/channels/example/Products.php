<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\vo;
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
     *
     * - product.id is the file name for the product.
     * - product.variant[].channel_variant_code is the file name for a variant.
     * - product.image[].channel_image_code is the file name for an image.
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @param vo\Flag[] $flagsMap
     * @return vo\ChannelProduct[] $channelProducts
     */
    public function sync(array $channelProducts, vo\Channel $channel, array $flagsMap): array
    {
        foreach ($channelProducts as $key => $product) {
            $channelProducts[$key]->channel_product_code = (string)$product->id;
            $channelProducts[$key]->success              = false;
            foreach ($product->variants as $vKey => $variant) {
                $channelProducts[$key]->variants[$vKey]->channel_variant_code = (string)$variant->id;
                $channelProducts[$key]->variants[$vKey]->success              = true;
            }
            foreach ($product->images as $ki => $img) {
                $channelProducts[$key]->images[$ki]->channel_image_code = (string)$img->id;
                $channelProducts[$key]->images[$ki]->success            = true;
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
        foreach ($channelProducts as $key => $product) {
            $channelProducts[$key]->channel_product_code = (string)$product->id;
            $channelProducts[$key]->success              = true;
            if ($channelProducts[$key]->delete) {
                $channelProducts[$key]->success = false;
            }
            foreach ($product->variants as $vKey => $variant) {
                $channelProducts[$key]->variants[$vKey]->channel_variant_code = (string)$variant->id;
                $channelProducts[$key]->variants[$vKey]->success              = true;
                if ($channelProducts[$key]->variants[$vKey]->delete) {
                    $channelProducts[$key]->variants[$vKey]->success = false;
                }
            }
            foreach ($product->images as $ki => $img) {
                $channelProducts[$key]->images[$ki]->channel_image_code = (string)$img->id;
                $channelProducts[$key]->images[$ki]->success            = true;
                if ($channelProducts[$key]->images[$ki]->delete) {
                    $channelProducts[$key]->images[$ki]->success = false;
                }
            }
        }
        return $channelProducts;
    }

}