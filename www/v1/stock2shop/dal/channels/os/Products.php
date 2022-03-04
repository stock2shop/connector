<?php

namespace stock2shop\dal\channels\os;

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
    /** @const string DATA_PATH */
    const DATA_PATH = __DIR__ . "/data";

    /** @const string CHANNEL_SEPARATOR_VARIANT */
    const CHANNEL_SEPARATOR_VARIANT = "variant_separator";

    /** @const string CHANNEL_SEPARATOR_IMAGE */
    const CHANNEL_SEPARATOR_IMAGE = "image_separator";

    /** @var string CHANNEL_ENDPOINT_PRODUCTS */
    const CHANNEL_ENDPOINT_PRODUCTS = "products_endpoint";

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
     * @return ChannelProduct[] $channelProducts
     */
    public function sync(array $channelProducts, vo\Channel $channel, array $flagsMap): array
    {

        /** @var string $imageSeparator Channel separator meta for images. */
        $imageSeparator = helpers\Meta::get($channel->meta, self::CHANNEL_SEPARATOR_IMAGE);

        /** @var string $variantSeparator Channel separator meta for variants. */
        $variantSeparator = helpers\Meta::get($channel->meta, self::CHANNEL_SEPARATOR_VARIANT);

        /** @var string $productsEndpoint Channel products endpoint. */
        $productsEndpoint = helpers\Meta::get($channel->meta, self::CHANNEL_ENDPOINT_PRODUCTS);

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
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, $productsEndpoint);

            // Check if the product has been flagged for delete.
            if ($product->delete === true) {
                foreach ($currentFiles as $currentFileName => $obj) {
                    $this->deleteProduct(self::DATA_PATH . '/' . $productsEndpoint . '/' . $currentFileName);
                }
            } else {
                $this->saveProduct(self::DATA_PATH . '/' . $productsEndpoint . '/' . $product->channel_product_code, $product);

                // Iterate through the product variants.
                foreach ($product->variants as $variant) {

                    // This is the path to the source system storage for this file.
                    $filePath = self::DATA_PATH . '/' . $productsEndpoint . '/' . $variant->channel_variant_code;
                    if ($product->delete) {
                        $this->deleteVariant($filePath);
                    } else {
                        $this->saveVariant($filePath, $variant);
                    }
                }

                // Iterate through the product images.
                foreach ($product->images as $image) {
                    $filePath = self::DATA_PATH . '/' . $productsEndpoint . '/' . $image->channel_image_code;
                    if ($product->delete) {
                        $this->deleteImage($filePath);
                    } else {
                        $this->saveImage($filePath, $image);
                    }
                }
            }

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
        // Channel Meta.

        // The following two variables '$imageSeparator' and '$variantSeparator' are an example
        // of how Channel Meta may be used to assist with processing the data for product images
        // and product variants received from a channel.

        // The aforementioned separator values are used to define the storage naming conventions
        // of `vo\ChannelImage` and `vo\ChannelVariant` objects on the channel. In this example,
        // the flat-file connector's target is the local file system and the separators are used in
        // the sync() method above and in the get() method (the current method) to indicate how
        // the entities must be saved to disk.

        /** @var string $imageSeparator Channel separator meta for images. */
        $imageSeparator = helpers\Meta::get($channel->meta, self::CHANNEL_SEPARATOR_IMAGE);

        /** @var string $variantSeparator Channel separator meta for variants. */
        $variantSeparator = helpers\Meta::get($channel->meta, self::CHANNEL_SEPARATOR_VARIANT);

        // ------------------------------------------------

        // Get Products From Channel.

        // In your integration, you should use the channel meta to make the process of mapping
        // products, variants and images onto Stock2Shop and back to the channel-supported format
        // logical, easy-to-understand and suitable for the target system you are working on.

        // Another example of using configurable channel meta to define the endpoint path for
        // getting products from the channel:

        $productsEndpointMeta = helpers\Meta::get($channel->meta, self::CHANNEL_ENDPOINT_PRODUCTS);
        $currentFiles = data\Helper::getJSONFiles($productsEndpointMeta);

        // ------------------------------------------------

        /** @var  $currentFiles */

        $cnt = 0;
        $channelProducts = [];
        foreach ($currentFiles as $fileName => $fileData) {

            // ------------------------------------------------

            // Compare Products With Token

            // The token is used to determine which products to add.
            // We use strcm() below to do the comparison.

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
     * This method defines the process flow for checking whether a list of products
     * is found on a channel. The products are checked by their 'channel_product_code'
     * property - as this is the ID associated with the channel for the product.
     *
     * - Product channel_product_code
     * - Product success
     * - Variant channel_variant_code
     * - Variant success
     * - Variant sku
     * - Image channel_image_code
     * - Image success
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[]
     * @throws exceptions\UnprocessableEntity
     */
    public function getByCode(array $channelProducts, vo\Channel $channel): array
    {
        return $channelProducts;
        /** @var vo\ChannelProduct[] $channelProductsFound */
        $channelProductsFound = [];

        foreach ($channelProducts as $product) {

            // Get the product files from disk.
            $productFiles = data\Helper::getJSONFilesByPrefix(urlencode($product->id), 'products');
            foreach ($productFiles as $fileChannelCode => $productFile) {
                $product->success = true;
                $product->source_product_code = $fileChannelCode;
                $channelProductsFound[$product->id . ".json"] = $product;

                $variantOrImage = $fileChannelCode;
                if (strpos($fileChannelCode, helpers\Meta::get($channel->meta, self::CHANNEL_SEPARATOR_VARIANT)) !== false) {
                    $channelProductsFound[$product->id . ".json"]->variants[] = new vo\ChannelVariant([
                        'channel_variant_code' => $productFile['channel_variant_code'],
                        'sku' => $productFile['sku'],
                        'success' => true
                    ]);
                }

                if (strpos($fileChannelCode, helpers\Meta::get($channel->meta, self::CHANNEL_SEPARATOR_IMAGE)) !== false) {
                    $channelProductsFound[$product->id . '.json']->images[] = new vo\ChannelImage([
                        'channel_variant_code' => $productFile['channel_image_code'],
                        'success' => true
                    ]);
                }
            }
        }
        return $channelProductsFound;

//        $channelProductsSync = [];
//        foreach ($channelProducts as $product) {
//
//            $prefix = urlencode($product->id);
//            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, 'products');
//
//            // -----------------------------------------------
//
//            foreach ($currentFiles as $fileName => $channelObject) {
//                if ($fileName === $prefix . '.json') {
//
//                    // Create product VO.
//                    $newSyncProduct = new vo\ChannelProduct([
//                        'channel_product_code' => $fileName
//                    ]);
//
//                    // Add product VO.
//                    $channelProductsSync[$prefix] = $newSyncProduct;
//
//                } else {
//
//                    // -----------------------------------------------
//
//                    // Channel Image
//
//                    // If the obj has a channel_image_code, then we can assume it is a
//                    // product image. As per Stock2Shop requirements:
//                    // - 'channel_image_code' must be set.
//
//                    if(array_key_exists("channel_image_code", $channelObject)) {
//                        $channelProductsSync[$prefix]->images[] = new vo\ChannelImage([
//                            "channel_image_code" => $channelObject["channel_image_code"]
//                        ]);
//                    }
//
//                    // -----------------------------------------------
//
//                    // Channel Variant
//
//                    // For channel product variants, the sku and channel_variant_code are
//                    // properties which must be set and are evaluated in the ChannelTest class.
//                    // - 'channel_variant_code' must be set.
//                    // - variant 'sku' must be set.
//
//                    if (array_key_exists("channel_variant_code", $channelObject)) {
//                        $channelProductsSync[$prefix]->variants[] = new vo\ChannelVariant([
//                            "sku" => $channelObject["sku"],
//                            "channel_variant_code" => $channelObject["channel_variant_code"]
//                        ]);
//                    }
//                }
//            }
//        }
//        return $channelProductsSync;

    }

    /**
     * Save Product
     *
     * This method adds a product to the channel.
     *
     * @param $productId
     * @param $product
     * @return bool $status
     */
    public function saveProduct($productId, $product): bool {

        // This method makes it possible for Stock2Shop to add products to the channel.
        // This is where you would send the product data to the system which this integration
        // is being developed for.

        // Here we are writing the product data to the local file system, but in a real-world
        // example you would replace the following with an external API call using cURL/Guzzle
        // or an API wrapper client (if available).

        // You will probably need to transform the data from our Stock2Shop format into
        // the format required by your system.

        // [transform product logic]:
        $transformedProductData = json_encode($product);

        // [save product logic]:
        file_put_contents($productId, $transformedProductData);
        return $productId;

    }

    /**
     * Delete Product
     *
     * Deletes a product from the channel.
     *
     * @param $productId
     * @return bool $status
     */
    public function deleteProduct($productId): bool {

        // This is where you would write the logic for deleting a product from a channel.
        // In this example, the productId is the name of the file in the `data/products`
        // directory.

        // [delete product logic]:
        unlink($productId);
        return true;
    }

    /**
     * Save Image
     *
     * Creates or updates the image on the channel.
     *
     * @param $imageId
     * @param $imageData
     * @return bool $status
     */
    public function saveImage($imageId, $imageData): bool {

        // This is where you would write the logic for saving a product image
        // to a channel. The image data is first transformed below and then
        // saved to the channel endpoint.

        // [transform product image logic]:
        $transformedProductImageData = json_encode($imageData);

        // [save product image logic]:
        file_put_contents($imageId, $transformedProductImageData);
        return true;
    }

    /**
     * Delete Image
     *
     * Removes an image from the channel.
     *
     * @param $imageId
     * @return bool $status
     */
    public function deleteImage($imageId): bool {

        // [delete product image logic]:
        unlink($imageId);
        return true;
    }

    /**
     * Save Variant
     *
     * Creates or updates the variant on the channel.
     *
     * @param $variantId
     * @param $variantData
     * @return bool $status
     */
    public function saveVariant($variantId, $variantData): bool {

        // Product variants are saved to separate endpoints (files in this example).
        // This is where you would code the logic for saving variants.

        // [transform variant logic]:
        $transformedVariantData = json_encode($variantData);

        // [save variant logic]:
        file_put_contents($variantId, $transformedVariantData);
        return true;
    }

    /**
     * Delete Variant
     *
     * Removes a variant from the channel.
     *
     * @param $variantId
     * @return bool $status
     */
    public function deleteVariant($variantId): bool {

        // This is where you would add the logic to delete a variant from the channel.

        // [delete product variant logic]:
        unlink($variantId);
        return true;
    }

}