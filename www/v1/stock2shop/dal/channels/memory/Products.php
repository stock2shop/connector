<?php

namespace stock2shop\dal\channels\memory;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\helpers;
use stock2shop\vo;

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
        // In many channels your work on this should be done in bulk where possible.
        foreach ($channelProducts as $product) {
            if ($product->delete) {
                ChannelState::deleteProductsByGroupIDs([$product->channel_product_code]);
                ChannelState::deleteImagesByGroupIDs([$product->channel_product_code]);
                foreach($product->variants as $delVariant) {
                    $delVariant->success;
                }
                foreach($product->images as $delImage) {
                    $delImage->success;
                }
                $product->success = true;
                continue;
            }

            // Do we have a memory product with a product_group_id?
            $existingMemoryProducts = ChannelState::getProductsByGroupID([$product->channel_product_code]);
            $currentGroupProductID = $existingMemoryProducts[0]->product_group_id ?? false;
            foreach ($product->variants as $variant) {

                // Does the memory product exist?
                $existingMemoryProduct = ChannelState::getProductsByIDs([$variant->channel_variant_code]);
                if ($variant->delete) {
                    if (count($existingMemoryProduct) === 1) {
                        ChannelState::deleteProductsByIDs([$variant->channel_variant_code]);
                        $variant->success = true;
                        // Delete all images linked to this variant.
                        ChannelState::deleteImagesByProductIDs([$variant->channel_variant_code]);
                    }
                } else {
                    $pMapper = new ProductMapper($product, $variant, $template);
                    $memoryProduct = $pMapper->get();
                    if ($currentGroupProductID) {
                        $memoryProduct->product_group_id = $currentGroupProductID;
                    }
                    if (count($existingMemoryProduct) === 1) {
                        $memoryProduct->id = $existingMemoryProduct[0]->id;
                    }
                    $memoryProduct = ChannelState::update([$memoryProduct])[0];
                    $currentGroupProductID = $memoryProduct->product_group_id;
                    $variant->success = true;
                    $variant->channel_variant_code = $memoryProduct->id;
                    $product->success = true;
                    $product->channel_product_code = $currentGroupProductID;
                }
            }

            // -----------------------------------------

            // Synchronize product images.
            foreach ($product->images as $image) {

                // Check whether the image exists on the channel.
                $existingMemoryImages = ChannelState::getImagesByIDs([$image->channel_image_code]);
                if ($image->delete) {
                    if (count($existingMemoryImages) === 1) {
                        // Delete the image.
                        ChannelState::deleteImages([$image->channel_image_code]);
                        $image->success = true;
                    }
                } else {

                    // Get existing "MemoryProducts" off the channel.
                    $existingMemoryProducts = ChannelState::getProductsByGroupID([$product->channel_product_code]);
                    foreach ($existingMemoryProducts as $memoryProductKey => $memoryProduct) {

                        // Map the "ChannelImage" VO and "MemoryProduct" onto a "MemoryImage".
                        $imageMapper = new ImageMapper($image, array_values($memoryProduct)[0]);
                        $memoryImage = $imageMapper->get();

                        // If it exists, then we'll just assign the ID and update.
                        $memoryImage = ChannelState::updateImages([$memoryImage]);
                        $image->success = true;
                        $image->channel_image_code = $memoryImage->id;
                    }
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
     */
    public function get(string $token, int $limit, vo\Channel $channel): vo\ChannelProductGet
    {
        // Get products from the channel's state which are filtered
        // starting from the position of channel_product_code and
        // limited by the integer value.
        $products = ChannelState::getProductsList($token, $limit);

        // ----------------------------------------

        // Iterate over the products returned from the channel
        // and build a map. The key of the map will be the
        // product_group_id and the value will be the product IDs.
        $productMap = [];
        foreach ($products as $memProduct) {
            if (!array_key_exists($memProduct->product_group_id, $productMap)) {
                $productMap[$memProduct->product_group_id] = [];
            }
            $productMap[$memProduct->product_group_id][] = ["channel_variant_code" => $memProduct->id, "success" => true];
        }

        // ----------------------------------------

        // Convert map into stock2shop VOs.
        foreach ($productMap as $productId => $variantIds) {

            // Map the product onto a `vo\ChannelProduct()` object.
            $variants = vo\ChannelVariant::createArray($variantIds);

            // ----------------------------------------

            // Get images by "product_group_id" (channel_product_code).
            $images = ChannelState::getImagesByGroupIDs([$productId]);
            $channelImages = [];
            foreach ($images as $memoryImage) {
                $channelImages[] = new vo\ChannelImage([
                    'channel_image_code' => $memoryImage->id,
                    'success' => true,
                    'src' => $memoryImage->url
                ]);
            }

            // ----------------------------------------

            // Create ChannelProduct VO.
            $channelProducts[] = new vo\ChannelProduct([
                'channel_product_code' => $productId,
                'success' => true,
                'variants' => $variants,
                'images' => $channelImages
            ]);

        }

        // ----------------------------------------

        // Get the "channel_product_code" of the last
        // product in the result set returned from the
        // channel.
        $lastProduct = end($channelProducts);

        // ----------------------------------------

        // Return the "token" and "products" in a
        // ChannelProductGet object.
        return new vo\ChannelProductGet([
            'token' => $lastProduct->channel_product_code,
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

        // ---------------------------------------

        foreach ($channelProducts as $product) {
            $productFiles = ChannelState::getAllProducts();
            $hasProduct = false;
            $hasVariant = false;
            foreach ($productFiles as $filename => $data) {
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
                if (!$hasVariant) {
                    array_push($productsToRemove, $product->channel_product_code);
                }
            }
        }

        // -----------------------------------------

        // Iterate over the channel products in the return array
        // and set their "success" properties to true.

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