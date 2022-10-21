<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use GuzzleHttp\Exception\GuzzleException;
use Stock2Shop\Share;

class ChannelProducts implements Share\Channel\ChannelProductsInterface
{
    public const CHANNEL_META_URL_KEY = 'api_url';

    public function sync(
        Share\DTO\ChannelProducts $channelProducts,
        Share\DTO\Channel         $channel
    ): Share\DTO\ChannelProducts {
        $url = self::getServerUrlFromChannelMeta($channel->meta);
        if ($url == "") {
            return new Share\DTO\ChannelProducts([]);
        }
        $api = new DemoAPI\API($url);

        // create payloads
        $toDelete = [];
        $toTouch  = [];
        foreach ($channelProducts->channel_products as $product) {
            if ($product->delete) {
                // product is to be deleted from the channel
                if (isset($product->channel_product_code)) {
                    // we can only delete by channel_product_code
                    // if it is not set we assume that the product has
                    // not yet been synced
                    $toDelete[] = $product->channel_product_code;
                }
            } else {
                $toTouch[] = Transform::DtoToDemoProduct($product);
            }
        }

        // create/update products
        if (count($toTouch) > 0) {
            try {
                $dps = $api->postProducts($toTouch);
                Transform::SetChannelCodesFromDemoProducts($channelProducts, $dps);
            } catch (GuzzleException $e) {
                return new Share\DTO\ChannelProducts([]);
            }
        }

        // delete products
        if (count($toDelete) > 0) {
            try {
                $code = $api->deleteProducts($toDelete);
                if ($code > 200 && $code < 300) {
                    Transform::UnsetChannelProperties($channelProducts, $toDelete);
                }
            } catch (GuzzleException $e) {
                return new Share\DTO\ChannelProducts([]);
            }
        }

        return $channelProducts;
    }

    public function get(
        string            $channel_product_code,
        int               $limit,
        Share\DTO\Channel $channel
    ): Share\DTO\ChannelProducts {
        $url = self::getServerUrlFromChannelMeta($channel->meta);
        if ($url == "") {
            return new Share\DTO\ChannelProducts([]);
        }

        $api = new DemoAPI\API($url);
        try {
            // get product data from the channel specified
            $products = $api->getProducts($channel_product_code, $limit);

            // transform DemoProduct data into ChannelProducts
            return Transform::DemoProductToDto($products);
        } catch (GuzzleException $e) {
            return new Share\DTO\ChannelProducts([]);
        }
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

        $url = self::getServerUrlFromChannelMeta($channel->meta);
        if ($url == "") {
            return new Share\DTO\ChannelProducts([]);
        }

        $api = new DemoAPI\API($url);
        try {
            // get product data from the channel specified
            $products = $api->getProductsByCodes($codes);

            // transform DemoProduct data into ChannelProducts
            return Transform::DemoProductToDto($products);
        } catch (GuzzleException $e) {
            return new Share\DTO\ChannelProducts([]);
        }
    }

    /**
     * @param Share\DTO\Meta[] $meta
     */
    private function getServerUrlFromChannelMeta(array $meta): string
    {
        foreach ($meta as $m) {
            if ($m->key === self::CHANNEL_META_URL_KEY) {
                return $m->value;
            }
        }

        return "";
    }
}
