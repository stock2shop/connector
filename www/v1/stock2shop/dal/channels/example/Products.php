<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\vo;

/**
 * Products
 *
 * This class is where the Data Access Layer is mapping onto
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
     * Creates a file for each product and for each product variant.
     * This method illustrates the possible cleanup operations required
     * for e-commerce channels.
     *
     * product.id is the file name for the product.
     * product.variant[].channel_variant_code is the file name for the variant.
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @param vo\Flag[] $flagsMap
     * @return ChannelProduct[] $channelProducts
     */
    public function sync(array $channelProducts, vo\Channel $channel, array $flagsMap): array
    {

        $logLineCounter = 0;

        // A 'separator' is used when creating product variant and product image file names.
        // The separator is an example of Stock2Shop Channel 'meta'.
        // Meta is a configured on Channel level and describes the channel and the required functionality.
        $variantSeparator = "";
        $imageSeparator = "";

        // Loop through the channel meta and assign values to local variables
        // for the two separators we need.
        foreach ($channel->meta as $metaItem) {
            if ($metaItem->key === self::CHANNEL_SEPARATOR_VARIANT) {
                $variantSeparator = $metaItem->value;
            }
            if ($metaItem->key === self::CHANNEL_SEPARATOR_IMAGE) {
                $imageSeparator = $metaItem->value;
            }
        }

        // ------------------------------------------------

        // Iterate through the channel products.
        foreach ($channelProducts as &$product) {

            $productId = $product->id;
            $sourceProductCode = $product->source_product_code;
            $channelProductCode = $product->channel_product_code;
            $clientId = $product->client_id;

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

                // Create or update product by writing the product data to disk/file.
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

                        file_put_contents($filePath, json_encode($variant));

                        $filesToKeep[] = $variant->channel_variant_code;
                    }
                }

                // ------------------------------------------------

                // Iterate through the product images.
                foreach ($product->images as $image) {

                    // This is the path to the source system storage for this file.
                    $filePath = self::DATA_PATH . '/products/' . $image->channel_image_code;

                    if ($product->delete) {
                        unlink($filePath);
                    } else {
                        file_put_contents($filePath, json_encode($image));
                        $filesToKeep[] = $image->channel_image_code;
                    }

                }

                // ------------------------------------------------

                // Remove old variants and images
                foreach ($currentFiles as $fileName => $obj) {
                    if (!in_array($fileName, $filesToKeep)) {
                        // Check if the file is an image or a variant of the product.
//                        if(strpos($fileName, $imageSeparator) !== false || strpos($fileName, $variantSeparator) !== false) {
                        // Unlink the JSON file from the source products directory.
                        unlink(self::DATA_PATH . '/products/' . $fileName);
//                        }
                    }
                }

            }

            // Mark products and variants as successfully synced
            // TODO: Shouldn't this be in the Value Object itself? Something like $product->setSynced();
            $date = new \DateTime();
            $product->synced = $date->format('Y-m-d H:i:s');

            // Mark product as successfully synced.
            $product->success = true;
            foreach ($product->variants as $variant) {
                // Set product variants as successfully synced.
                $variant->success = true;
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
        /** @var Meta[] $map */
        $map = [];

        /** @var Meta $metaRow */
        foreach($channel->meta as $metaRow) {
            $map[$metaRow->key] = $metaRow->value;
        }

        /** @var  $currentFiles */
        $currentFiles = data\Helper::getJSONFiles("products");

        $cnt = 0;
        $channelProducts = [];
        foreach ($currentFiles as $fileName => $fileData) {

            if (strcmp($token, $fileName) < 0) {

                // Use regex to check the filename and determine what
                // kind of object it is.

                // 2. Variant
                // -----------------------------------------------------

                // The regex pattern may be broken up into:

                // product prefix:   [^[0-9]{5}]   include all numbers up to 5 characters at the start of the string.
                // separator:        [~]           include the separator.
                // variant suffix:   [\w%]+        include all numbers and letters of any length.

                if(preg_match('/^[[0-9]{5}[~][\w%]+].json/', $fileName)) {

                    // Create new Variant using the VO and add to the product.
                    $channelProducts[$fileName]->variants[] = new vo\ChannelVariant($fileData);

                }

                // 3. Image
                // -----------------------------------------------------
                if(preg_match('/^[[0-9]{5}[=][\w]+].json/', $fileName)) {
                    // TODO: We need to check whether the image is linked to a product or linked to a variant.
                    // Create new ChannelImage using the VO and add to the product.
                    $channelProducts[$fileName]->images[] = new vo\ChannelImage($fileData);
                }

                // 1. Product
                // -----------------------------------------------------
                // The regex pattern may be broken up into:
                // product prefix:   [^[0-9]{5}]   include all numbers up to 5 characters at the start of the string.
                if(preg_match('/^[0-9]{5}.json/', $fileName)) {
                    // Check that we have not reached the limit.
                    if ($cnt > $limit) {
                        break;
                    }
                    // Create new ChannelProduct using the VO and add to the array.
                    $channelProducts[$fileName] = new vo\ChannelProduct($fileData);
                    $cnt++;
                }

            }
        }

        return $channelProducts;

    }


//    public function get(string $token, int $limit, vo\Channel $channel): array
//    {
//
//        // Variant separator.
//        $variantSeparator = "";
//        foreach ($channel->meta as $metaItem) {
//            if ($metaItem->key === "variant_separator") {
//                $variantSeparator = $metaItem->value;
//            }
//        }
//
//        // Image separator
//        $imageSeparator = "";
//        foreach ($channel->meta as $metaItem) {
//            if ($metaItem->key === "image_separator") {
//                $imageSeparator = $metaItem->value;
//            }
//        }
//
//        $currentFiles = data\Helper::getJSONFiles("products");
//        $channelProducts = [];
//        $cnt = 1;
//
//        foreach ($currentFiles as $fileName => $file) {
//
//            if ($cnt > $limit) {
//                break;
//            }
//
//            // Compare the token and file name.
//            if (strcmp($token, $fileName) < 0) {
//
//                // Do the strpos calculations.
//                $isFileVariant = strpos($fileName, $variantSeparator);
//                $isFileImage = strpos($fileName, $imageSeparator);
//
//                // Does the file name have the separator string in it.
//                // If not, then we know that this product is not a product variant.
//                // Which means we can continue and add the product to the channelProducts array.
//                if ($isFileVariant) $channelProducts[$fileName]->variants[] = new vo\ChannelVariant($file);
//                if ($isFileImage) $channelProducts[$fileName]->images[] = new vo\ChannelImage($file);
//
//
//            } else {
//
//                $channelProduct = new vo\ChannelProduct($file);
//                $channelProduct->channel_product_code = $channelProduct->id;
//
//                if (empty($channelProducts)) {
//                    $channelProducts = [$fileName => $channelProduct];
//                }
//
//                $channelProducts[$fileName] = $channelProduct;
//
//            }
//
//
//            $cnt++;
//        }
//
//        return $channelProducts;
//
//    }

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
        $channelProductsSync = [];

        foreach ($channelProducts as $product) {

            $prefix = urlencode($product->id);
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, 'products');

            foreach ($currentFiles as $fileName => $obj) {

                if ($fileName === $prefix . '.json') {

                    $newSyncProduct = new vo\ChannelProduct([
                        'channel_product_code' => $fileName
                    ]);

                    $channelProductsSync[$prefix] = $newSyncProduct;

                } else {

                    $extractedProductId = $prefix . ".json";

                    if(array_key_exists("channel_image_code", $obj)) {
                        $channelProductsSync[$prefix]->images[] = new vo\ChannelImage([
                            "channel_image_code" => $obj["channel_image_code"]
                        ]);
                    }

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