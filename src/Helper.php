<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Share;

class Helper
{
    public const DATA_DIR = __DIR__ . '/data';

    public static function setDataDir()
    {
        if (!is_dir(self::DATA_DIR)) {
            mkdir(self::DATA_DIR);
        }
    }

    /**
     * This function will create a stdClass object with fields specified by the channel.
     * Field values are set using the ChannelProduct passed as a param
     */
    public static function createPayloadProduct(Share\DTO\ChannelProduct $product): \stdClass
    {
        // product
        $channelProduct = new \stdClass();
        $channelProduct->name = $product->title;
        $channelProduct->id = $product->channel_product_code ?? null;
        $channelProduct->variants = [];
        $channelProduct->images = [];

        // set variants
        foreach ($product->variants as $v) {
            $variant = new \stdClass();
            $variant->sku = $v->sku;
            $variant->id = $v->channel_variant_code ?? null;
            $channelProduct->variants[] = $variant;
        }

        // set images
        foreach ($product->images as $i) {
            $image = new \stdClass();
            $image->source = $i->src;
            $image->id = $i->channel_image_code ?? null;
            $channelProduct->images[] = $image;
        }

        return $channelProduct;
    }

    /**
     * This function will set the appropriate fields on the Share\DTO\ChannelProducts based off of the
     * channels response
     */
    public static function setChannelProductFields(Share\DTO\ChannelProducts &$products, array $data)
    {
        // we know that a Share\DTO\ChannelProducts Title field is the equivalent of the data's name field
        foreach ($data as $p) {
            /** @var $product Share\DTO\ChannelProducts  **/
            foreach ($products->channel_products as $value=>$product) {
                if ($product->title == $p->name) {
                    $product->channel_product_code = $p->id;
                    $product->success = true;

                    // set variants channel_variant_code
                    foreach ($p->variants as $v) {
                        foreach ($product->variants as $value=>$variant) {
                            if ($v->sku == $variant->sku) {
                                $variant->channel_variant_code = $v->id;
                                $variant->success = true;
                            }
                        }
                    }

                    // set images channel_image_code
                    foreach ($p->images as $i) {
                        foreach ($product->images as $value=>$image) {
                            if ($i->source == $image->src) {
                                $image->channel_image_code = $i->id;
                                $image->success = true;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Convenience function for marking multiple products as deleted
     */
    public static function markAsDeleted(Share\DTO\ChannelProducts &$products, array $data)
    {
        foreach ($data as $cpc) {
            foreach ($products->channel_products as $p) {
                /** @var $product Share\DTO\ChannelProducts  **/
                if ($p->channel_product_code == $cpc) {
                    self::setDeleted($p);
                }
            }
        }
    }

    /**
     * This function will set the fields, that are no longer valid,
     * post deletion from the channel to null
     */
    public static function setDeleted(Share\DTO\ChannelProduct &$product)
    {
        $product->channel_product_code = null;
        foreach ($product->variants as $v) {
            $v->channel_variant_code = null;
            $v->success = true;
        }
        foreach ($product->images as $i) {
            $i->channel_image_code = null;
            $i->success = true;
        }
        $product->success = true;
    }

    /**
     * This function will remove all products from the channel
     */
    public static function clearChannelProducts()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:1234/clean');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_exec($ch);
    }
}
