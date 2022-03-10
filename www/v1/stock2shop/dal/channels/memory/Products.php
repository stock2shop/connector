<?php

namespace stock2shop\dal\channels\memory;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\exceptions;
use stock2shop\vo;
use stock2shop\helpers;

/**
 * See comments in ProductsInterface
 *
 * @package stock2shop\dal\memory
 */
class Products implements ProductsInterface
{
    const META_MUSTACHE_TEMPLATE = 'mustache_template';

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
        // Get configuration used to customise the channel.
        // You could have all sorts of meta here, for example, username and password to
        // authenticate with some 3rd party shopping cart.
        $template = helpers\Meta::get($channel->meta, self::META_MUSTACHE_TEMPLATE);

        // This example channel updates products one at a time.
        // In many channels you work on this should be done in bulk where possible.

        // Build and save all MemoryProduct objects.
        foreach ($channelProducts as $cp) {

            foreach ($cp->variants as $cv) {
                $mapper         = new ProductMapper($cp, $cv, $template);
                $exampleProduct = $mapper->get();
                if ($cp->delete || $cv->delete) {
                    ChannelState::deleteProductsByIDs([$exampleProduct->id]);
                } elseif (!$exampleProduct->id) {
                    $exampleProduct->product_group_id = $cp->id;
                    $productId = ChannelState::create($exampleProduct);
                    $cv->channel_variant_code = $productId;
                } else {
                    ChannelState::update([$exampleProduct]);
                }
                $cp->channel_product_code = $exampleProduct->product_group_id;
                $cp->success              = true;
                $cv->channel_variant_code = $exampleProduct->id;
                $cv->success              = true;

                // set images
                foreach ($cp->images as $ci) {
                    $mapper       = new ImageMapper($ci, $exampleProduct);
                    $exampleImage = $mapper->get();
                    if ($ci->delete) {
                        ChannelState::deleteImages([$exampleImage->id]);
                    } else {
                        ChannelState::updateImages([$exampleImage]);
                    }
                    $ci->channel_image_code = $exampleImage->id;
                    $ci->success            = true;
                }
            }
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
    public function get(string $channel_product_code, int $limit, vo\Channel $channel): array
    {
        // Return array.
        $channelProducts = [];

        // Get products from the channel's state which are filtered
        // starting from the position of channel_product_code and
        // limited by the integer value.
        $products = ChannelState::getProductsList($channel_product_code, $limit);

        // Iterate over the products returned from the channel
        // and build a map. The key of the map will be the
        // product_group_id and the value will be the product IDs.
        $productMap = [];
        foreach($products as $memProduct) {
            if(!array_key_exists($memProduct->product_group_id, $productMap)) {
                $productMap[$memProduct->product_group_id] = [];
            }
            $productMap[$memProduct->product_group_id][] = ["channel_variant_code" => $memProduct->id, "success"=>true];
        }

        // Convert map into stock2shop VOs.
        foreach($productMap as $productId => $variantIds) {

            // Map the product onto a `vo\ChannelProduct()` object.
            $channelProducts[] = new vo\ChannelProduct([
                'channel_product_code' => $productId,
                'success' => true,
                'variants' => $variantIds
            ]);

        }

        return $channelProducts;

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
        $template = helpers\Meta::get($channel->meta, self::META_MUSTACHE_TEMPLATE);
        foreach ($channelProducts as $kcp => $cp) {
            foreach ($cp->variants as $kcv => $cv) {

                // Transform `vo\ChannelProduct` to `MemoryProduct` and fetch existing
                // `MemoryProduct` stored in `ChannelState`.
                $mapper                  = new ProductMapper($cp, $cv, $template);
                $exampleProduct          = $mapper->get();
                $existingExampleProducts = ChannelState::getProductsByIDs([$exampleProduct->id]);

                // remove variant if it is not found
                if (count($existingExampleProducts) === 0) {
                    unset($cp->variants[$kcv]);
                    continue;
                }
                $cp->channel_product_code = $exampleProduct->product_group_id;
                $cp->success              = true;
                $cv->channel_variant_code = $exampleProduct->id;
                $cv->success              = true;

                // Check images exist in state
                foreach ($cp->images as $kci => $ci) {

                    $mapper                = new ImageMapper($ci, $exampleProduct);
                    $exampleImage          = $mapper->get();
                    $existingExampleImages = ChannelState::getImagesByIDs([$exampleImage->id]);
                    if (count($existingExampleImages) === 0) {
                        unset($cp->images[$kci]);
                        continue;
                    }
                    $ci->channel_image_code = $exampleImage->id;
                    $ci->success            = true;
                }
            }

            // remove products which have no variants
            if (count($cp->variants) === 0) {
                unset($channelProducts[$kcp]);
            }
        }
        return $channelProducts;
    }

}