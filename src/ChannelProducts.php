<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Share;

class ChannelProducts implements Share\Channel\ChannelProductsInterface
{
    public function sync(
        Share\DTO\ChannelProducts $channelProducts,
        Share\DTO\Channel         $channel
    ): Share\DTO\ChannelProducts {
        Helper::setDataDir();
        foreach ($channelProducts->channel_products as $product) {
            $path = Helper::getProductPath($product);

            // remove
            if ($product->delete) {
                if (file_exists($path)) {
                    unlink($path);
                }
                $product->channel_product_code = null;
                foreach ($product->variants as $v) {
                    $v->channel_variant_code = null;
                }
                foreach ($product->images as $i) {
                    $i->channel_image_code = null;
                }
            } else {
                $product->channel_product_code = (string)$product->id;
                foreach ($product->variants as $v) {
                    $v->channel_variant_code = (string)$v->id;
                }
                foreach ($product->images as $i) {
                    $i->channel_image_code = (string)$i->id;
                }
                file_put_contents($path, json_encode($product));
            }
            $product->success = true;
            foreach ($product->variants as $v) {
                $v->success = true;
            }
            foreach ($product->images as $i) {
                $i->success = true;
            }
        }
        return $channelProducts;
    }

    public function get(
        string            $channel_product_code,
        int               $limit,
        Share\DTO\Channel $channel
    ): Share\DTO\ChannelProducts {
        Helper::setDataDir();
        $products = Helper::getJSONFiles();
        $cnt      = 0;
        $results  = [
            'channel_products' => []
        ];
        foreach ($products as $filename => $data) {
            $parts = explode('.', $filename);
            $id    = (int)$parts[0];
            if ($id > (int)$channel_product_code) {
                $cnt++;
                if ($cnt > $limit) {
                    break;
                }
                $data['channel']['success'] = true;
                foreach ($data['variants'] as $k => $v) {
                    $data['variants'][$k]['channel']['success'] = true;
                }
                foreach ($data['images'] as $k => $v) {
                    $data['images'][$k]['channel']['success'] = true;
                }
                $results['channel_products'][] = $data;
            }
        }
        return new Share\DTO\ChannelProducts($results);
    }

    public function getByCode(
        Share\DTO\ChannelProducts $channelProducts,
        Share\DTO\Channel         $channel
    ): Share\DTO\ChannelProducts {
        Helper::setDataDir();
        foreach ($channelProducts->channel_products as $product) {
            try {
                $file = file_get_contents(Helper::getProductPath($product));
                $data = json_decode($file, true);
            } catch (\Exception) {
                $data = [];
            }
            $existing         = new Share\DTO\ChannelProduct($data);
            $product->success = ($existing->channel_product_code === $product->channel_product_code);
            foreach ($product->variants as $variant) {
                $found = false;
                foreach ($existing->variants as $existingVariant) {
                    if ($existingVariant->channel_variant_code === $variant->channel_variant_code) {
                        $found = true;
                        break;
                    }
                }
                $variant->success = $found;
            }
            foreach ($product->images as $image) {
                $found = false;
                foreach ($existing->images as $existingImage) {
                    if ($existingImage->channel_image_code === $image->channel_image_code) {
                        $found = true;
                        break;
                    }
                }
                $image->success = $found;
            }
        }
        return $channelProducts;
    }
}
