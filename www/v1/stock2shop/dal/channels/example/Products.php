<?php

namespace stock2shop\dal\channels\example;

use stock2shop\vo;
use stock2shop\exceptions;
use stock2shop\dal\channel\Products as ProductsInterface;

/**
 * Products
 *
 * This class is where the Data Access Layer is mapping onto
 * the Stock2Shop Value Objects from the source system you are
 * integrating with.
 */
class Products implements ProductsInterface
{

    /**
     * @const string DATA_PATH
     */
    const DATA_PATH = __DIR__ . "/data";

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

        // Separator is used when creating variant file names.
        // The separator is an example of Stock2Shop Channel 'meta'.
        // Meta is a configured on Channel level and describes the
        // channel and the required functionality.

        /** @var string $separator */
        $separator = $channel->getMetaItemValueByKey("separator");

        // Iterate through the channel products.
        foreach ($channelProducts as &$product) {

            $prefix = urlencode($product->id);
            $productFileName = $prefix . '.json';

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
                $variant->channel_variant_code = $prefix . $separator . urlencode($variant->sku) . '.json';

            }

            // Fetch the current files from the source (in this case, flat-file).
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, "products");

            // Check if the product has been flagged for delete.
            if ($product->delete) {
                foreach ($currentFiles as $currentFileName => $obj) {
                    unlink(self::DATA_PATH . '/products/' . $currentFileName);
                }
            } else {

                // Create or update product by writing the product data to disk/file.
                file_put_contents(self::DATA_PATH . '/products/' . $product->channel_product_code, json_encode($product));

                $variantsToKeep = [];

                // Iterate through the product variants.
                foreach ($product->variants as $variant) {

                    // This is the path to the source system storage for this file.
                    $filePath = self::DATA_PATH . '/products/' . $variant->channel_variant_code;

                    if ($product->delete) {

                        // Delete the product from the source system.
                        // In this example, each product is saved to file.
                        // We are calling unlink() on the file path to delete the product.
                        unlink($filePath);

                    } else {

                        // Add the product.
                        // In this example, each product is saved to file.
                        // We are going to save the JSON structure to file.
                        file_put_contents($filePath, json_encode($variant));
                        $variantsToKeep[] = $variant->channel_variant_code;

                    }
                }

                // Remove old variants
                foreach ($currentFiles as $fileName => $obj) {
                    if (!in_array($fileName, $variantsToKeep) && strpos($fileName, $separator) !== false) {
                        // Unlink the JSON file from the source products directory.
                        unlink(self::DATA_PATH . '/products/' . $fileName);
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
        // Get separator.
        $separator = $channel->getMetaItemValueByKey('separator');
        $channelProducts = [];
        $currentFiles = data\Helper::getJSONFiles("products");

        // Create paged results.
        $cnt = 1;
        foreach ($currentFiles as $fileName => $obj) {

            // Compare the token and file name.
            if (strcmp($token, $fileName) < 0) {
                if (strpos($fileName, $separator) === false) {

                    // This a product
                    if ($cnt > $limit) {
                        break;
                    }

                    // Add vo\ChannelProductGet object to return array.
                    $channelProducts[] = new vo\ChannelProductGet([
                        "channel_product_code" => $obj->channel_product_code
                    ]);
                    $cnt++;

                } else {

                    // Add a variant to the ChannelProduct in the return array.
                    $channelProducts[count($channelProducts) - 1]->variants[] = new vo\ChannelVariant(
                        [
                            "sku" => $obj->sku,
                            "channel_variant_code" => $obj->channel_variant_code
                        ]
                    );

                    // Set the vo\ChannelProductGet's token value to the channel_variant_code.
                    $channelProducts[count($channelProducts) - 1]->token = $obj->channel_variant_code;

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
     * @return array
     * @throws exceptions\UnprocessableEntity
     */
    public function getByCode(array $channelProducts, vo\Channel $channel): array
    {

        $channelProducts = [];

        foreach ($channelProducts as $product) {

            $prefix = urlencode($product->id);
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, "products");

            foreach ($currentFiles as $fileName => $obj) {
                if ($fileName === $prefix . '.json') {

                    // This is a Product
                    $channelProducts[] = new vo\ChannelProduct([
                        "channel_product_code" => $obj->channel_product_code
                    ]);

                } else {

                    // This is a Variant
                    $channelProducts[count($channelProducts) - 1]->variants[] = new vo\ChannelVariant(
                        [
                            "sku" => $obj->sku,
                            "channel_variant_code" => $obj->channel_variant_code
                        ]
                    );

                }
            }
        }

        return $channelProducts;
    }

}