<?php

namespace stock2shop\dal\channels\example;

use stock2shop\vo;
use stock2shop\exceptions;
use stock2shop\dal\channel;

/**
 * Products
 *
 * This class is where the Data Access Layer is mapping onto
 * the Stock2Shop Value Objects from the source system you are
 * integrating with.
 */
class Products implements channel\Products
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
     * @return vo\ChannelProduct[] $channelProducts
     */
    public function sync(array $channelProducts, vo\Channel $channel, array $flagsMap): array
    {

        /**
         * Configure channel meta.
         */
        $map = Meta::createArray($channel->meta);
        $separator = $map['separator'];

        foreach($channelProducts as $product) {

            /**
             * Generate the product prefix - which is the encoded product_id.
             */
            $prefix          = urlencode($product->id);
            $productFileName = $prefix . '.json';

            /**
             * Create Stock2Shop internal channel_product_code for each product.
             */
            $product->channel_product_code = $productFileName;
            foreach ($product->variants as $variant) {
                $variant->channel_variant_code = $prefix . $separator . urlencode($variant->sku) . '.json';
            }

            /**
             * Fetch the JSON files with the matching 'products' prefix.
             */
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, "products");

            /**
             * If the $delete property is set, then remove the product.
             */
            if ($product->delete) {
                foreach ($currentFiles as $currentFileName => $obj) {
                    unlink(self::DATA_PATH . '/products/' . $currentFileName);
                }
            } else {

                /**
                 * Create or update product.
                 * Here we are writing back to the channel to update the products.
                 * In this example, the product data is being written to file instead.
                 */
                file_put_contents(self::DATA_PATH . '/products/' . $product->channel_product_code, json_encode($product));

                /**
                 * Create or update product variants.
                 * This is where the product variants are being create or updated.
                 */
                $variantsToKeep = [];
                foreach ($product->variants as $variant) {

                    $filePath = self::DATA_PATH . '/products/' . $variant->channel_variant_code;

                    if ($product->delete) {
                        unlink($filePath);
                    } else {
                        file_put_contents($filePath, json_encode($variant));
                        $variantsToKeep[] = $variant->channel_variant_code;
                    }
                }

                /**
                 * Remove old products and/or product variants.
                 * $currentFiles may contain fileNames for either.
                 */
                foreach ($currentFiles as $fileName => $obj) {
                    if (!in_array($fileName, $variantsToKeep) && strpos($fileName, $separator) !== false) {
                        unlink(self::DATA_PATH . '/products/' . $fileName);
                    }
                }

            }

            /**
             * Mark the product and product variants as successfully synchronized.
             * This is done on a channel by updating the $synced property of the
             * $product object with the current timestamp.
             */
            $date = new \DateTime();
            $product->success = true;
            $product->synced  = $date->format('Y-m-d H:i:s');
            foreach ($product->variants as $variant) {
                $variant->success = true;
            }

        }

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
     * @return ChannelProduct[] $channelProducts
     */
    public function get(string $token, int $limit, vo\Channel $channel): array
    {

        /** @var Meta $map */
        $map = $channel->meta;
        $separator = $map['separator'];

        /** @var string[] $currentFiles */
        $currentFiles = data\Helper::getJSONFiles("products");

        /**
         * In this example, we are using JSON files for each product as
         * the source of the product data.
         */
        $cnt = 1;
        $channelProducts = [];

        foreach ($currentFiles as $fileName => $obj) {
            if (strcmp($token, $fileName) < 0) {
                if (strpos($fileName, $separator) === false) {

                    if ($cnt > $limit) {
                        break;
                    }

                    /**
                     * Add product channel_product_code to array.
                     */
                    $channelProducts[] = new ChannelProduct([
                        "channel_product_code" => $obj->channel_product_code
                    ]);

                    $cnt++;

                } else {

                    /**
                     * Create new product variant from source.
                     */
                    $channelProducts[count($channelProducts) - 1]->variants[] = new ChannelVariant(
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
     * @param array $channelProducts
     * @param vo\Channel $channel
     * @return array
     * @throws exceptions\UnprocessableEntity
     */
    public function getByCode(array $channelProducts, vo\Channel $channel): array
    {
        $matchingProducts = [];

        /**
         * 1. Iterate through the channel_products and add the matches
         * to the channelProducts array.
         */
        foreach ($channelProducts as $product) {

            /**
             * 2. Prepare encoded prefix.
             */
            $prefix = urlencode($product->id);
            $currentFiles = data\Helper::getJSONFilesByPrefix($prefix, "products");

            foreach ($currentFiles as $fileName => $obj) {
                if ($fileName === $prefix . '.json') {

                    $matchingProducts[] = new ChannelProduct([
                        "channel_product_code" => $obj->channel_product_code
                    ]);

                } else {

                    $matchingProducts[count($matchingProducts) - 1]->variants[] = new ChannelVariant([
                        "sku" => $obj->sku,
                        "channel_variant_code" => $obj->channel_variant_code
                    ]);

                }
            }
        }

        return $matchingProducts;

    }

}