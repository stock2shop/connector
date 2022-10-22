<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Connector\DemoAPI\Image;
use Stock2Shop\Connector\DemoAPI\Option;
use Stock2Shop\Connector\DemoAPI\Product;
use Stock2Shop\Share\DTO;

class Transform
{
    /**
     * @param DTO\ChannelProduct[] $channelProducts
     * @return Product[]
     */
    public static function getDemoProducts(array $channelProducts): array
    {
        $products = [];
        foreach ($channelProducts as $cp) {
            $options = [];
            $images  = [];
            foreach ($cp->variants as $v) {
                $options[] = new Option([
                    'id'  => $v->channel_variant_code,
                    'sku' => $v->sku
                ]);
            }
            foreach ($cp->images as $i) {
                $images[] = new Image([
                    'url' => $i->src,
                    'id'  => $i->channel_image_code
                ]);
            }
            $products[] = new DemoAPI\Product([
                'id'      => $cp->channel_product_code,
                'name'    => $cp->title,
                'options' => $options,
                'images'  => $images
            ]);
        }
        return $products;
    }

    /**
     * @param DTO\ChannelProduct[] $channelProducts
     * @return string[]
     */
    public static function getDemoProductIDS(array $channelProducts): array
    {
        $ids = [];
        foreach ($channelProducts as $cp) {
            if (
                !is_null($cp->channel_product_code) &&
                $cp->channel_product_code !== ''
            ) {
                $ids[] = $cp->channel_product_code;
            }
        }
        return $ids;
    }

    /**
     * @param Product[] $demoProducts
     */
    public static function getChannelProducts(array $demoProducts): DTO\ChannelProducts
    {
        $cps = new DTO\ChannelProducts([]);
        foreach ($demoProducts as $p) {
            $cp                       = new DTO\ChannelProduct([]);
            $cp->title                = $p->name;
            $cp->channel_product_code = $p->id;
            $cp->success              = true;
            foreach ($p->options as $o) {
                $cp->variants[] = new DTO\ChannelVariant([
                    'channel_variant_code' => $o->id,
                    'sku'                  => $o->sku,
                    'success'              => true
                ]);
            }
            foreach ($p->images as $i) {
                $cp->images[] = new DTO\ChannelImage([
                    'channel_image_code' => $i->id,
                    'src'                => $i->url,
                    'success'            => true
                ]);
            }
            $cps->channel_products[] = $cp;
        }
        return $cps;
    }
}
