<?php

namespace stock2shop\dal\channels\memory;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\helpers;
use stock2shop\vo;

/**
 * See comments in stock2shop\dal\channel\Product
 * See readme.md on how to load custom configuration for your channel
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
        foreach ($channelProducts as $cp) {
            foreach ($cp->variants as $cv) {
                $mapper = new ProductMapper($cp, $cv, $template);
                $exampleProduct = $mapper->get();
                if ($cp->delete) {
                    ChannelState::deleteProductsByGroupIDs([$exampleProduct->product_group_id]);
                } elseif ($cv->delete) {
                    ChannelState::deleteProducts([$exampleProduct->id]);
                } else {
                    ChannelState::updateProducts([$exampleProduct]);
                }
                $cp->channel_product_code = $exampleProduct->product_group_id;
                $cp->success = true;
                $cv->channel_variant_code = $exampleProduct->id;
                $cv->success = true;
                foreach ($cp->images as $ci) {
                    $mapper = new ImageMapper($ci, $exampleProduct);
                    $exampleImage = $mapper->get();
                    if ($ci->delete) {
                        ChannelState::deleteImages([$exampleImage->id]);
                    } else {
                        ChannelState::updateImages([$exampleImage]);
                    }
                    $ci->channel_image_code = $exampleImage->id;
                    $ci->success = true;
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
     * @return vo\ChannelProductGet $channelProducts
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    public function get(string $token, int $limit, vo\Channel $channel): vo\ChannelProductGet
    {
        // Get products from ChannelState by "offset pagination/paging".
        if ($token === '') {
            // Convert from empty string.
            // This is to add support for cursor-based
            // pagination in other connectors as well.
            $offset = 0;
        } else {
            $offset = (int)$token;
        }

        // Get all products from the channel's state.
        $allProductGroups = ChannelState::getProductGroups();

        // Get products with appropriate index.
        $productGroups = array_slice($allProductGroups, $offset, $limit, true);

        // Instantiate "vo\ChannelProductGet" object,
        // which we use to return both the last "offset"
        // and the "products" (if there are any).
        $channelProductGet = new vo\ChannelProductGet([]);

        // Build channel_products from groups.
        foreach ($productGroups as $product_group_id => $group) {
            $variants = [];
            $images = [];
            foreach ($group as $product) {
                $variants[] = [
                    'channel_variant_code' => $product->id,
                    'sku' => $product->id,
                    'success' => true
                ];
            }
            $groupImages = ChannelState::getImagesByGroupIDs([$product_group_id]);
            foreach ($groupImages as $image) {
                $images[] = [
                    'channel_image_code' => $image->id,
                    'success' => true
                ];
            }
            $channelProductGet->channel_products[] = new vo\ChannelProduct([
                'channel_product_code' => $product_group_id,
                'success' => true,
                'variants' => $variants,
                'images' => $images
            ]);
        }

        // If there are no products returned for this
        // page "$offset" then we set the token to the
        // current "$token".
        if (count($productGroups) === 0) {
            $channelProductGet->token = $token;
        } else {

            // If there are products returned from the channel,
            // then the 'token' is calculated by adding the current
            // page "$offset" to the "$limit".
            $channelProductGet->token = (string)($offset + $limit);
        }
        return $channelProductGet;
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
        $groups = ChannelState::getProductGroups();
        $images = ChannelState::getImages();
        foreach ($channelProducts as $cp) {
            if (isset($groups[$cp->channel_product_code])) {
                $cp->success = true;
                $memoryIDs = array_column($groups[$cp->channel_product_code], 'id');
                foreach ($cp->variants as $cv) {
                    if (in_array($cv->channel_variant_code, $memoryIDs)) {
                        $cp->success = true;
                    }
                }
                foreach ($cp->images as $img) {
                    if (isset($images[$img->channel_image_code])) {
                        $img->success = true;
                    }
                }
            }
        }
        return $channelProducts;
    }

}