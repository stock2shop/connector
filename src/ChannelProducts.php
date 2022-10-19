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
        // keep track of which codes need to be deleted
        $deleteCodes   = [];
        $touchProducts = [];
        foreach ($channelProducts->channel_products as $product) {
            if ($product->delete) {
                // product is to be deleted from the channel
                if (isset($product->channel_product_code)) {
                    // we can only delete by channel_product_code
                    // if it is not set we assume that the product has
                    // not yet been synced
                    $deleteCodes[] = $product->channel_product_code;
                } else {
                    // if no channel_product_code is set,
                    // treat the product as if it has been
                    // successfully deleted
                    Helper::setDeleted($product);
                }
            } else {
                $touchProducts[] = Helper::createPayloadProduct($product);
            }
        }

        // create/update products
        if (count($touchProducts) > 0) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:1234/products');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($touchProducts));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($server_output);
            if ($server_output) {
                Helper::setChannelProductFields($channelProducts, (array)$data);
            }
        }


        // delete products
        if (count($deleteCodes) > 0) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:1234/products');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($deleteCodes));
            curl_setopt($ch, CURLOPT_NOBODY, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            $code          = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ((int)$code == 202) {
                Helper::markAsDeleted($channelProducts, $deleteCodes);
            }
            $data = json_decode($server_output);
            if ($server_output) {
                Helper::setChannelProductFields($channelProducts, (array)$data);
            }
        }

        return $channelProducts;
    }

    public function get(
        string            $channel_product_code,
        int               $limit,
        Share\DTO\Channel $channel
    ): Share\DTO\ChannelProducts {
        // create url with params
        $endpoint = 'http://localhost:1234/products/page?';
        $params   = [
            'channel_product_code' => $channel_product_code,
            'limit'                => $limit,
        ];
        $url      = $endpoint . http_build_query($params);

        // make get request to fetch product data from channel
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $server_output = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($server_output);

        // create Share\DTO\ChannelProducts from response
        $results = [
            'channel_products' => []
        ];
        foreach ($data as $p) {
            $cp                       = new Share\DTO\ChannelProduct([]);
            $cp->success              = true;
            $cp->channel_product_code = $p->id;
            foreach ($p->variants as $variant) {
                $cp->variants[] = new Share\DTO\ChannelVariant(
                    [
                        'sku'                  => $variant->sku,
                        'success'              => true,
                        'channel_variant_code' => $variant->id
                    ]
                );
            }
            foreach ($p->images as $image) {
                $cp->images[] = new Share\DTO\ChannelImage(
                    [
                        'src'                => $image->source,
                        'success'            => true,
                        'channel_image_code' => $image->id
                    ]
                );
            }
            $results['channel_products'][] = $cp;
        }
        return new Share\DTO\ChannelProducts($results);
    }

    public function getByCode(
        Share\DTO\ChannelProducts $channelProducts,
        Share\DTO\Channel         $channel
    ): Share\DTO\ChannelProducts {
        // channel only allows us to read products by channel_product_code or in pages
        // get channel_product_codes
        $codes = [];
        foreach ($channelProducts->channel_products as $product) {
            if (isset($product->channel_product_code)) {
                $codes[] = $product->channel_product_code;
            }
        }

        // make get request to fetch product data from channel
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:1234/products');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($codes));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($server_output);

        foreach ($channelProducts->channel_products as $value => $product) {
            $product->success = false;
            foreach ($data as $p) {
                if ($product->channel_product_code == $p->id) {
                    $product->success = true;
                    foreach ($product->variants as $value => $variant) {
                        $variant->success = false;
                        foreach ($p->variants as $v) {
                            if ($variant->channel_variant_code === $v->id) {
                                $variant->success = true;
                                break;
                            }
                        }
                    }
                    foreach ($product->images as $value => $image) {
                        $image->success = false;
                        foreach ($p->images as $i) {
                            if ($image->channel_image_code === $i->id) {
                                $image->success = true;
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $channelProducts;
    }
}
