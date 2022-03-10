<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\exceptions;
use stock2shop\vo;
use stock2shop\helpers;

/**
 * See comments in ProductsInterface
 *
 * @package stock2shop\dal\example
 */
class Products implements ProductsInterface
{
    // Your connector implementation must be as configurable as possible.
    // Add any meta configuration you use in either `sync()`, `get()` or
    // `getByCode()` as class constants here. Naming conventions for
    // constants are the uppercase meta key name prefixed by META_ with
    // underscores substituting spaces. The value must always be lowercase.

//    const META_MUSTACHE_TEMPLATE = 'mustache_template';

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

//        $template = helpers\Meta::get($channel->meta, self::META_MUSTACHE_TEMPLATE);

        // This example channel updates products one at a time.
        // In many channels you work on this should be done in bulk where possible.

        // Iterate over products:
        foreach ($channelProducts as $cp) {

            // Iterate over product variants:
            foreach ($cp->variants as $cv) {

                // Map Stock2Shop products and variants onto the
                // channel's structure:

                // for example..
                // $mapper = new ProductMapper($cp, $cv);
                // $exampleProduct = $mapper->map($template);

                if ($cp->delete || $cv->delete) {

                    // If Stock2Shop has marked the product or variant's
                    // "delete" property, then logic to remove the product/
                    // variant is added here:

                    // for example..
                    // $client = new ApiClient();
                    // $client->delete('/product', $exampleProduct);

                } else {

                    // If the product is not marked for delete, then we
                    // might want to update it on the channel.

                    // for example..
                    // $client = new ApiClient();
                    // $client->put('/product', $exampleProduct);

                }

                // After the relevant action has been performed on the product,
                // update the Stock2Shop Value Object which it belongs to by
                // setting the "success" property for both the variant and the
                // product as well as the "channel_product_code" and
                // "channel_variant_code" properties respectively:

//                $cp->success = true;       // product
//                $cv->success = true;       // variant

                // This ensures that the Stock2Shops system is able to know
                // from looking at the response returned by this method whether
                // the synchronisation was successful or not.

                // The "channel_*_code" properties are always the outbound ID.
                // Please set this to the value used by your system to uniquely
                // identify products:

//                $cp->channel_product_code = $exampleProduct->group_id;
//                $cv->channel_variant_code = $exampleProduct->id;


                // Next, map the images for your products by looping over
                // the images for each product provided by our system:

                foreach ($cp->images as $ci) {

                    // As with products/variants, you may want to abstract the
                    // logic for doing the transformation from our format to
                    // the one that this channel supports into its own mapper
                    // class, which you might use like this...

//                    $mapper = new ImageMapper($ci, $exampleProduct);
//                    $exampleImage = $mapper->get();

                    if ($ci->delete) {

                        // Execute the action which deletes the product's
                        // image from the channel:

//                        $apiClient->delete("/products/2/images/44");

                    } else {

                        // Update or create the image on the channel:

//                        $apiClient->post("/products/2/images");

                    }

                    // Finally, remember to set the "channel_image_code" property
                    // to the unique identifier value used on the channel:

//                    $ci->channel_image_code = $exampleImage->id;

                    // And mark the synchronisation as successful for the image.

//                    $ci->success            = true;

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

        // Instantiate your api client or data helper class.
//        $apiClient = new example\ApiClient();

        foreach ($channelProducts as $kcp => $cp) {
            foreach ($cp->variants as $kcv => $cv) {

                // Transform the products from Stock2Shop format
                // into channel format before doing the request:

//                $mapper = new ProductMapper($cp, $cv, $template);
//                $exampleProduct = $mapper->get();
//                $existingExampleProducts = $apiClient->get('/products/?id=' . $exampleProduct->id);

                // Set the values from the product received by the channel
                // to the Stock2Shop product and variant VO structures.
                // Stock2Shop uses this to confirm whether the product is
                // in fact on the channel already.

                $cp->channel_product_code = $exampleProduct->group_id;
                $cp->success              = true;
                $cv->channel_variant_code = $exampleProduct->id;
                $cv->success              = true;

                // The same logic must be applied for the product images
                // which are on the channel. Also set the "channel_image_code"
                // property with the unique identifier used by the channel
                // to reference the product.

                // The success property must be marked 'true' so that we know
                // that the 'getByCode()' was successful.

                foreach ($cp->images as $kci => $ci) {

                    // Transform S2S Image to ExampleImage and fetch ExampleImages
                    $mapper                = new ImageMapper($ci, $exampleProduct);
                    $exampleImage          = $mapper->get();

                    // Get the image from the channel:
                    $existingExampleImages = $apiClient->get('/products/' . $cp->channel_product_code . '/images);

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