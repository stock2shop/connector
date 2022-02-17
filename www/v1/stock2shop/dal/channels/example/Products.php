<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\vo;
use stock2shop\helpers;

/**
 * Products
 *
 * This class is where the Data Access Layer is mapped onto
 * the Stock2Shop Value Objects from the source system you are
 * integrating with.
 */
class Products implements ProductsInterface
{

    /** @const string DATA_PATH */
    const DATA_PATH = __DIR__ . "/data";

    /** @const string CHANNEL_SEPARATOR_VARIANT */
    const CHANNEL_SEPARATOR_VARIANT = "variant_separator";

    /** @const string CHANNEL_SEPARATOR_IMAGE */
    const CHANNEL_SEPARATOR_IMAGE = "image_separator";

    /**
     * Sync
     *
     * Creates a file for each product and for each product, variant
     * and product image. This method illustrates the possible cleanup
     * operations required for e-commerce channels.
     *
     * - product.id is the file name for the product.
     * - product.variant[].channel_variant_code is the file name for a variant.
     * - product.image[].channel_image_code is the file name for an image.
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @param vo\Flag[] $flagsMap
     * @return ChannelProduct[] $channelProducts
     */
    public function sync(array $channelProducts, vo\Channel $channel, array $flagsMap): array
    {

        /** @var string $imageSeparator Channel separator meta for images. */
        $imageSeparator = helpers\Meta::get($channel->meta, self::CHANNEL_SEPARATOR_IMAGE);

        /** @var string $variantSeparator Channel separator meta for variants. */
        $variantSeparator = helpers\Meta::get($channel->meta, self::CHANNEL_SEPARATOR_VARIANT);

        // ------------------------------------------------

        // Iterate through the channel products.
        foreach ($channelProducts as &$product) {

            $prefix = urlencode($product->id);
            $productFileName = $prefix . '.json';

            // ------------------------------------------------

            // Create channel_product_code for each product from the file name.
            // In your integration, this would be the ID or code that the target
            // system uses to uniquely identify the product.
            // i.e. in WooCommerce this would be the post ID of the product.
            $product->channel_product_code = $productFileName;
            foreach ($product->variants as $variant) {

                // Create channel_variant_code for each product variant.
                // In this example, the channel_variant_code is a combination
                // of the $prefix + channel separator (configured as channel meta)
                // + the url encoded variant SKU code.
                $encodedVariantSku = urlencode($variant->sku);
                $variant->channel_variant_code = $prefix . $variantSeparator . $encodedVariantSku . '.json';

            }

            // ------------------------------------------------

            // Do the same as the loop above to set the channel_image_code for each channel image.
            foreach ($product->images as $image) {
                $encodedChannelImageId = urlencode($image->id);
                $image->channel_image_code = $prefix . $imageSeparator . $encodedChannelImageId . '.json';
            }

            // ------------------------------------------------

            // Fetch the current files from the source (in this case, flat-file).
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, "products");

            // Check if the product has been flagged for delete.
            if ($product->delete === true) {

                foreach ($currentFiles as $currentFileName => $obj) {
                    unlink(self::DATA_PATH . '/products/' . $currentFileName);
                }

            } else {

                /**
                 * SEND PRODUCT TO CHANNEL.
                 * This is where the product is sent to the physical channel. If the
                 * channel is an eCommerce website such as Magento or WooCommerce, then
                 * you will do this over HTTP (using the corresponding API client library
                 * or a client like Guzzle).
                 */
                file_put_contents(self::DATA_PATH . '/products/' . $product->channel_product_code, json_encode($product));

                // ------------------------------------------------

                $filesToKeep = [];
                $filesToKeep[] = $product->channel_product_code;

                // Iterate through the product variants.
                foreach ($product->variants as $variant) {

                    // This is the path to the source system storage for this file.
                    $filePath = self::DATA_PATH . '/products/' . $variant->channel_variant_code;

                    if ($product->delete) {

                        unlink($filePath);

                    } else {

                        /**
                         * SEND VARIANT TO CHANNEL.
                         * This is where the variant is sent to the channel.
                         */
                        file_put_contents($filePath, json_encode($variant));

                        // ------------------------------------------------

                        $filesToKeep[] = $variant->channel_variant_code;

                    }
                }

                // Iterate through the product images.
                foreach ($product->images as $image) {
                    // This is the path to the source system storage for this file.
                    $filePath = self::DATA_PATH . '/products/' . $image->channel_image_code;
                    if ($product->delete) {

                        /**
                         * DELETE IMAGE ON CHANNEL.
                         */
                        unlink($filePath);

                    } else {

                        /**
                         * SEND IMAGE TO CHANNEL.
                         * This is where the image is sent to the channel.
                         */
                        file_put_contents($filePath, json_encode($image));

                        // ------------------------------------------------

                        $filesToKeep[] = $image->channel_image_code;
                    }
                }

                // Remove old variants and images
                foreach ($currentFiles as $fileName => $obj) {
                    if (!in_array($fileName, $filesToKeep)) {
                        unlink(self::DATA_PATH . '/products/' . $fileName);
                    }
                }

            }

            // Mark products, images and variants as successfully synced.
            // TODO: Shouldn't this be in the Value Object itself? Something like $product->setSynced();
            $date = new \DateTime();
            $product->synced = $date->format('Y-m-d H:i:s');

            // Mark product as successfully synced.
            $product->success = true;
            foreach ($product->variants as $variant) {
                // Set product variants as successfully synced.
                $variant->success = true;
            }
            foreach ($product->images as $image) {
                // Set product images as successfully synced.
                $image->success = true;
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
     * The workflow you define in this method must include setting the token property of each
     * ChannelProductGet class object to the channel_product_code.
     *
     * @param string $token
     * @param int $limit
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[] $channelProducts
     */
    public function get(string $token, int $limit, vo\Channel $channel): array
    {
        /** @var string $imageSeparator Channel separator meta for images. */
        $imageSeparator = helpers\Meta::get($channel->meta, self::CHANNEL_SEPARATOR_IMAGE);

        /** @var string $variantSeparator Channel separator meta for variants. */
        $variantSeparator = helpers\Meta::get($channel->meta, self::CHANNEL_SEPARATOR_VARIANT);

        /** @var  $currentFiles */
        $currentFiles = data\Helper::getJSONFiles("products");

        $cnt = 0;
        $channelProducts = [];

        foreach ($currentFiles as $fileName => $fileData) {

            if (strcmp($token, $fileName) < 0) {

                // Variant check.
                if(strcmp($variantSeparator, $fileName) < 0) {
                    $channelProducts[$fileName]->variants[] = new vo\ChannelVariant($fileData);
                }

                // Image check.
                if(strcmp($imageSeparator, $fileName) < 0) {
                    $channelProducts[$fileName]->images[] = new vo\ChannelImage($fileData);
                }

                // product prefix:   [^[0-9]{5}]   include all numbers up to 5 characters at the start of the string.
                if(preg_match('/^[0-9]{5}.json/', $fileName)) {

                    // Check that we have not reached the limit.
                    if ($cnt > $limit) {
                        break;
                    }

                    // Create new vo\ChannelProduct using the VO and add to the array.
                    $channelProducts[$fileName] = new vo\ChannelProduct($fileData);
                    $cnt++;

                }

            }
        }

        return $channelProducts;

    }

    /**
     * Get By Code
     *
     * This method returns ChannelProduct items by code.
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[]
     */
    public function getByCode(array $channelProducts, vo\Channel $channel): array
    {
        /** @var vo\ChannelProduct[] $channelProductsSync */
        $channelProductsSync = [];

        foreach ($channelProducts as $product) {

            $prefix = urlencode($product->id);

            // Get all product data from the channel.
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, 'products');

            foreach ($currentFiles as $fileName => $obj) {

                if ($fileName === $prefix . '.json') {

                    // Create product VO.
                    $newSyncProduct = new vo\ChannelProduct([
                        'channel_product_code' => $fileName
                    ]);

                    $channelProductsSync[$prefix] = $newSyncProduct;

                } else {

                    // If the obj has a channel_image_code, then we can
                    // assume it is a product image.
                    if(array_key_exists("channel_image_code", $obj)) {
                        $channelProductsSync[$prefix]->images[] = new vo\ChannelImage([
                            "channel_image_code" => $obj["channel_image_code"]
                        ]);
                    }

                    // For channel product variants, the sku and channel_variant_code are
                    // properties which must be set and are evaluated in the ChannelTest class.
                    if (array_key_exists('channel_variant_code', $obj)) {
                            $channelProductsSync[$prefix]->variants[] = new vo\ChannelVariant([
                            "sku" => $obj["sku"],
                            "channel_variant_code" => $obj["channel_variant_code"]
                        ]);
                    }

                }

            }
        }

        return $channelProductsSync;

    }

}