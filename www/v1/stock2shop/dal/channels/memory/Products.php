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
        foreach ($channelProducts as $productKey => $product) {
            if ($product->delete) {
                ChannelState::deleteProductsByGroupIDs([$product->channel_product_code]);
                ChannelState::deleteImagesByGroupIDs([$product->channel_product_code]);
                $product->success = true;
                unset($channelProducts[$productKey]);
                continue;
            }

            // Do we have a memory product with a product_group_id?
            $existingMemoryProducts = ChannelState::getProductsByGroupIDs([$product->channel_product_code]);
            $currentGroupProductID = $existingMemoryProducts[0]->product_group_id ?? false;
            foreach ($product->variants as $variant) {

                // Does the memory product exist?
                $existingMemoryProduct = ChannelState::getProductsByIDs([$variant->channel_variant_code]);
                if ($variant->delete) {
                    if (count($existingMemoryProduct) === 1) {
                        $ids = ChannelState::deleteProductsByIDs([$variant->channel_variant_code]);
                        $variant->success = true;
                        // Delete all images linked to this variant.
                        $imageIds = ChannelState::deleteImagesByProductIDs([$variant->channel_variant_code]);
                        foreach ($product->images as $deletedProductImage) {
                            if (in_array($deletedProductImage->channel_image_code, $imageIds)) {
                                $deletedProductImage->success = true;
                            }
                        }
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
            $existingMemoryImages = ChannelState::getImagesByGroupIDs([$product->channel_product_code]);
            foreach ($product->images as $channelProductImageKey => $channelProductImageValue) {

                // Check if we need to delete the image.
                if ($channelProductImageValue->delete) {
                    $deletedImageCodes = ChannelState::deleteImageByUrl([$channelProductImageValue->src]);
                    if (!empty($deletedImageCodes)) {
                        unset($product->images[$channelProductImageKey]);
                    }
                } else {

                    // Get existing "MemoryProducts" off the channel.
                    $existingMemoryProducts = ChannelState::getProductsByGroupIDs([$product->channel_product_code]);
                    foreach ($existingMemoryProducts as $memoryProductKey => $memoryProduct) {

                        // Map the "ChannelImage" VO and "MemoryProduct" onto a "MemoryImage".
                        $imageMapper = new ImageMapper($product->images[$channelProductImageKey], $memoryProduct);
                        $memoryImage = $imageMapper->get();

                        // If it exists, then we'll just assign the ID and update.
                        $memoryImage = ChannelState::updateImages([$memoryImage])[0];
                        $product->images[$channelProductImageKey]->success = true;
                        $product->images[$channelProductImageKey]->channel_image_code = $memoryImage->id;
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
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    public function get(string $token, int $limit, vo\Channel $channel): vo\ChannelProductGet
    {
        /** @var vo\ChannelProductGet $channelProductGet */
        $channelProductGet = new vo\ChannelProductGet([]);

        // Get products from ChannelState by offset.
        if ($token === '') {
            $offset = 0;
        } else {
            $offset = (int)$token;
        }
        $allProductGroups = ChannelState::getProductGroups();

        // get products with appropriate index
        $productGroups = array_slice($allProductGroups, $offset, $limit, true);

        // Build channel_products from groups
        foreach ($productGroups as $product_group_id => $group) {
            $variants = [];
            $images   = [];
            foreach ($group as $product) {
                $variants[] = [
                    'channel_variant_code' => $product->id,
                    'sku'                  => $product->id,
                    'success'              => true
                ];
            }
            $groupImages = ChannelState::getImagesByGroupIDs([$product_group_id]);
            foreach ($groupImages as $image) {
                $images[] = [
                    'channel_image_code' => $image->id,
                    'success'            => true
                ];
            }
            $channelProductGet->channel_products[] = new vo\ChannelProduct([
                'channel_product_code' => $product_group_id,
                'success'              => true,
                'variants'             => $variants,
                'images'               => $images
            ]);
        }

        // Set token
        if(count($productGroups) === 0) {
            $channelProductGet->token = $token;
        } else {
            $channelProductGet->token = (string) ($offset + $limit);
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
        $productsToRemove = [];
        $imagesToRemove = [];
        $variantsToRemove = [];

        // ---------------------------------------

        foreach ($channelProducts as $product) {
            $productFiles = ChannelState::getProducts();
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