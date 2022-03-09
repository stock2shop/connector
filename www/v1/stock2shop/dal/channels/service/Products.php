<?php

namespace stock2shop\dal\channels\service;

use stock2shop\vo;

class Products implements channel\Products
{

    /**
     * @inheritDoc
     */
    public function sync(array $channelProducts, vo\Channel $channel, array $flagsMap): array
    {
        // Instantiate the service repository.

        // We will use the repository to write any product data to
        // the in-memory storage for products, variants and images
        // during the synchronization process.

        foreach ($channelProducts as $key => $product) {
            $channelProducts[$key]->channel_product_code = (string)$product->id;
            $channelProducts[$key]->success = true;
            foreach ($product->variants as $vKey => $variant) {
                $channelProducts[$key]->variants[$vKey]->channel_variant_code = (string)$variant->id;
                $channelProducts[$key]->variants[$vKey]->success = true;
            }
            foreach ($product->images as $ki => $img) {
                $channelProducts[$key]->images[$ki]->channel_image_code = (string)$img->id;
                $channelProducts[$key]->images[$ki]->success = true;
            }
        }
        return $channelProducts;
    }

    /**
     * @inheritDoc
     */
    public function getByCode(array $channelProducts, vo\Channel $channel): array
    {
        // TODO: Implement getByCode() method.
    }

    /**
     * @inheritDoc
     */
    public function get(string $token, int $limit, vo\Channel $channel): array
    {
        // TODO: Implement get() method.
    }
}