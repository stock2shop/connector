<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use JetBrains\PhpStorm\Pure;
use Stock2Shop\Connector\DemoAPI\Image;
use Stock2Shop\Connector\DemoAPI\Option;
use Stock2Shop\Connector\DemoAPI\Product;
use Stock2Shop\Share\DTO;

class Transform
{
    #[Pure] public static function DtoToDemoProduct(DTO\ChannelProduct $product): Product
    {
        $options = [];
        $images  = [];
        foreach ($product->variants as $v) {
            $options[] = new Option([
                'id'  => $v->channel_variant_code,
                'sku' => $v->sku
            ]);
        }
        foreach ($product->images as $i) {
            $images[] = new Image([
                'url' => $i->src,
                'id'  => $i->channel_image_code
            ]);
        }
        return new DemoAPI\Product([
            'id'      => $product->channel_product_code,
            'name'    => $product->title,
            'options' => $options,
            'images'  => $images
        ]);
    }

    /**
     * @param Product[] $demoProducts
     */
    public static function DemoProductToDto(array $demoProducts): DTO\ChannelProducts
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

    /**
     * SetChannelCodesFromDemoProduct will set the following properties
     * ChannelProduct->channel_product_code
     * ChannelProduct->success
     * ChannelProduct->Variants[]->channel_variant_code
     * ChannelProduct->Variants[]->success
     * ChannelProduct->Images[]->channel_image_code
     * ChannelProduct->Images[]->success
     * ChannelProduct->Images[]->src
     * @param DTO\ChannelProducts $cps
     * @param Product[] $dps
     */
    public static function SetChannelCodesFromDemoProducts(DTO\ChannelProducts &$cps, array $dps)
    {
        foreach ($dps as $dp) {
            foreach ($cps->channel_products as $x => $cp) {
                if ($dp->name == $cp->title) {
                    $cp->channel_product_code = $dp->id;
                    $cp->success              = true;
                    foreach ($dp->options as $option) {
                        foreach ($cp->variants as $y => $variant) {
                            if ($option->sku == $variant->sku) {
                                $variant->channel_variant_code = $option->id;
                                $variant->success              = true;
                            }
                        }
                    }
                    foreach ($dp->images as $image) {
                        foreach ($cp->images as $z => $i) {
                            if ($image->url == $i->src) {
                                $i->channel_image_code = $image->id;
                                $i->success            = true;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string[] $codes the channel_product_codes to be unset
     */
    public static function UnsetChannelProperties(DTO\ChannelProducts $cps, array $codes)
    {
        foreach ($codes as $code) {
            foreach ($cps->channel_products as $x => $cp) {
                if ($code == $cp->channel_product_code) {
                    $cp->channel_product_code = null;
                    $cp->success              = true;
                    foreach ($cp->variants as $y => $v) {
                        $v->channel_variant_code = null;
                        $v->success              = true;
                    }
                    foreach ($cp->images as $z => $i) {
                        $i->channel_image_code = null;
                        $i->success            = true;
                    }
                }
            }
        }
    }
}
