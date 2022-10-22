<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Connector\DemoAPI\Product;
use Stock2Shop\Share\DTO;
use Stock2Shop\Share;

class SyncResults
{
    /**
     * @param DTO\ChannelProduct[] $channelProducts
     */
    public static function setFailed(array $channelProducts): void
    {
        foreach ($channelProducts as $cp) {
            $cp->success = false;
            foreach ($cp->images as $ci) {
                $ci->success = false;
            }
            foreach ($cp->variants as $cv) {
                $cv->success = false;
            }
        }
    }

    /**
     * @param DTO\ChannelProduct[] $channelProducts
     * @param Product[] $demoProducts
     * @return void
     */
    public static function setSuccess(array $channelProducts, array $demoProducts): void
    {
        // sku is unique in both Stock2Shop and on the demo channel
        $mapSKU   = [];
        $mapImage = [];
        foreach ($demoProducts as $demoProduct) {
            foreach ($demoProduct->options as $option) {
                $mapSKU[$option->sku] = [
                    'product' => $demoProduct,
                    'option'  => $option
                ];
            }
            // images src maps to url
            foreach ($demoProduct->images as $image) {
                $mapImage[$image->url] = $image;
            }
        }
        foreach ($channelProducts as &$channelProduct) {
            foreach ($channelProduct->variants as $variant) {
                if (array_key_exists($variant->sku, $mapSKU)) {
                    $variant->success                     = true;
                    $variant->channel_variant_code        = $mapSKU[$variant->sku]['option']->id;
                    $channelProduct->success              = true;
                    $channelProduct->channel_product_code = $mapSKU[$variant->sku]['product']->id;
                    foreach ($channelProduct->images as $image) {
                        if (array_key_exists($image->src, $mapImage)) {
                            $image->success            = true;
                            $image->channel_image_code = $mapImage[$image->src]->id;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param DTO\ChannelProduct[] $channelProducts
     */
    public static function setDeleteSuccess(array $channelProducts): void
    {
        foreach ($channelProducts as $cp) {
            $cp->success              = true;
            $cp->channel_product_code = null;
            foreach ($cp->images as $ci) {
                $ci->success            = true;
                $ci->channel_image_code = null;
            }
            foreach ($cp->variants as $cv) {
                $cv->success              = true;
                $cv->channel_variant_code = null;
            }
        }
    }

}
