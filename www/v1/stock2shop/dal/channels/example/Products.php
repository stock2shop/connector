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
        // Get the separator meta from the Channel object.
        $meta = $channel->meta;

        // TODO: Ask Chris about a better way to access channel meta data.
//        $separator = $map['separator'];

        $separator = null;
        // Loop through the meta data for the channel and assign the value of
        // the one with the 'separator' key to a local variable.
        foreach($meta as $metaItem) {
            if($metaItem->key === "separator")
            {
                $separator = $metaItem->value;
            }
        }

        // Loop through all the channelProducts and
        foreach($channelProducts as $product) {

            // Transform product id as prefix.
            $prefix          = urlencode($product->id);
            $productFileName = $prefix . '.json';

            // Assign the channel_product_code to the product from the filename.
            $product->channel_product_code = $productFileName;
            foreach ($product->variants as $variant) {
                // And also assign a channeL_variant_code to each variant using the prefix and the
                // separator configured in the Meta.
                $variant->channel_variant_code = $prefix . $separator . urlencode($variant->sku) . '.json';
            }

            // Get all source products which have been marked.
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, "products");

            // Delete products which have been marked.
            if ($product->delete) {
                foreach ($currentFiles as $currentFileName => $obj) {
                    // Unlink file from source.
                    unlink(self::DATA_PATH . '/products/' . $currentFileName);
                }
            } else {

                // Create or update product.
                // Here we are writing back to the channel to update the products.
                // In this example, the product data is being written to file instead.
                file_put_contents(self::DATA_PATH . '/products/' . $product->channel_product_code, json_encode($product));

                // Create or update product variants.
                // This is where the product variants are being create or updated.
                $variantsToKeep = [];
                foreach ($product->variants as $variant) {

                    // This is the path we are writing the channel product's data to.
                    $filePath = self::DATA_PATH . '/products/' . $variant->channel_variant_code;

                    // If the product has been marked to be deleted, then unlink the file from the source.
                    if ($product->delete) {
                        unlink($filePath);
                    } else {

                        // Write the product to the source (file).
                        file_put_contents($filePath, json_encode($variant));

                        // Gather the variant codes of variants which must be retained.
                        $variantsToKeep[] = $variant->channel_variant_code;
                    }
                }

                // Loop through the source data (files).
                foreach ($currentFiles as $fileName => $obj) {

                    // Check whether to delete the variant from the source (file).
                    if (!in_array($fileName, $variantsToKeep) && strpos($fileName, $separator) !== false) {
                        unlink(self::DATA_PATH . '/products/' . $fileName);
                    }
                }
            }

            // Mark product as successfully synchronised.
            $product->success = true;

            // The current date and time is added in the required format.
            $date = new \DateTime();
            $product->synced  = $date->format('Y-m-d H:i:s');

            // Loop through variants.
            foreach ($product->variants as $variant) {

                // Mark product variant as successfully synchronised.
                $variant->success = true;
            }

        }

        // Finally, return all the channel products.
        return $channelProducts;

    }

    /**
     * Get
     *
     * Returns the products from this channel.
     *
     * @param string $token
     * @param int $limit
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[] $channelProducts
     */
    public function get(string $token, int $limit, vo\Channel $channel): array
    {

        /** @var Meta $map */
        $map = $channel->meta;
        $separator = $map['separator'];

        /** @var string[] $currentFiles */
        $currentFiles = data\Helper::getJSONFiles("products");
        $cnt = 1;

        /** @var ChannelProductGet[] $channelProducts */
        $channelProducts = [];

        // Loop through the product source data - which in this example integration
        // is provided by the data\Helper's getJSONFiles() method.
        foreach ($currentFiles as $fileName => $obj) {
            if (strcmp($token, $fileName) < 0) {
                if (strpos($fileName, $separator) === false) {
                    if ($cnt > $limit) {
                        break;
                    }
                    $channelProducts[] = new vo\ChannelProductGet([
                        "channel_product_code" => $obj->channel_product_code
                    ]);
                    $cnt++;
                } else {
                    $channelProducts[count($channelProducts) - 1]->variants[] = new vo\ChannelVariant(
                        [
                            "sku"                  => $obj->sku,
                            "channel_variant_code" => $obj->channel_variant_code
                        ]
                    );
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
        $matchingProducts = [];

        foreach ($channelProducts as $product) {

            $prefix = urlencode($product->id);
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, "products");

            foreach ($currentFiles as $fileName => $obj) {
                if ($fileName === $prefix . '.json') {

                    $matchingProducts[] = new vo\ChannelProduct([
                        "channel_product_code" => $obj->channel_product_code
                    ]);

                } else {

                    $matchingProducts[count($matchingProducts) - 1]->variants[] = new vo\ChannelVariant([
                        "sku" => $obj->sku,
                        "channel_variant_code" => $obj->channel_variant_code
                    ]);

                }
            }
        }

        return $matchingProducts;
    }

}