<?php

namespace stock2shop\dal\channels\memory;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\exceptions;
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

        // Instantiate the maps.
        // We'll use the maps for products, variants and images to know which
        // have been synchronized to the channel when we mark the entities synced.
        $products_success_map = [];
        $variants_success_map = [];
        $images_success_map = [];



        // First we need to map the products onto the Memory channel
        // data classes. We will add these to an array of products
        // which may be sent to the channel.
        $memoryChannelProducts = [];
//        foreach($channelProducts as $cpk => $cpp) {
//            foreach($channelProducts[$cpk]->variants as $cpv) {
//                $mapper = new ProductMapper($cpp, $cpv, $template);
//                $memoryChannelProducts[$cpv->sku] = $mapper->get();
//            }
//        }





        // This example channel updates products one at a time.
        // In many channels your work on this should be done in bulk where possible.
        foreach ($channelProducts as $key => $product) {
            foreach ($product->variants as $variant) {

                $pMapper = new ProductMapper($product, $variant, $template);
                $memoryProduct = $pMapper->get();

                if ($product->delete || $variant->delete) {
                    ChannelState::deleteProductsByIDs([$memoryProduct->id]);
                } elseif (!$memoryProduct->id) {
                    $memoryProduct->id = ChannelState::create($memoryProduct);
                } else {
                    ChannelState::update([$memoryProduct]);
                }
                $variant->channel_variant_code = $memoryProduct->id;
//                $memoryProductMap[$variant->channel_variant_code] = [ "mp" => $memoryProduct, => $product->];
            }
        }


        // Build map of all the products on the channel which
        // require an image to be linked to them. Some of the images
        // might already have been linked to a product  on the channel,
        // so for these we will have to link the same image. The images
        // are shared between the products which map to the same
        // product->variant group on the channel.
//        $productVariantMap = [];
//        foreach($channelProducts as $key => $product) {
//            foreach($channelProducts[$key]->variants as $variant) {
//                $productVariantMap[$variant->sku][] = $product->images;
//            }
//        }
        // Iterate over the products on the channel and use the
        // productVariantMap to determine the images to create for each product.
        // When we create images on the channel, we will also receive an ID back
        // from the channel's state.
//        foreach ($channelProducts as $key => $product) {
//            foreach($channelProducts[$key]->images as $iKey => $iValue) {
//                $mpMap = $memoryProductMap[$product->id];
//                foreach($mpMap as $mp) {
//                    $imageMapper = new ImageMapper($iValue, $mp);
//                    $imageMapping = $imageMapper->get();
//                    if(!isset($imageMapping->id)) {
//
//                    }
//                    $channelImageId = ChannelState::createImage();
//                }
//                $channelProducts[$key]->images[$iKey]->channel_image_code = $channelImageId;
//            }
//        }



        // Iterate over products and mark synced.
        foreach($channelProducts as $key => $product) {
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
     * See comments in ProductsInterface::get
     *
     * @param string $channel_product_code
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
        foreach ($products as $memProduct) {
            if (!array_key_exists($memProduct->product_group_id, $productMap)) {
                $productMap[$memProduct->product_group_id] = [];
            }
            $productMap[$memProduct->product_group_id][] = ["channel_variant_code" => $memProduct->id, "success" => true];
        }

        // Convert map into stock2shop VOs.
        foreach ($productMap as $productId => $variantIds) {
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