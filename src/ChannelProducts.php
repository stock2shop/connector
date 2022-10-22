<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use GuzzleHttp\Exception\GuzzleException;
use Stock2Shop\Share\DTO;
use Stock2Shop\Share;

class ChannelProducts implements Share\Channel\ChannelProductsInterface
{

    public function sync(
        Share\DTO\ChannelProducts $channelProducts,
        Share\DTO\Channel $channel
    ): Share\DTO\ChannelProducts {
        $meta = new Meta($channel);
        $url  = $meta->get(Meta::CHANNEL_META_URL_KEY);
        if (!$url) {
            SyncResults::setFailed($channelProducts->channel_products);
            Logger::LogProductSync(
                DTO\Log::LOG_LEVEL_ERROR,
                'Invalid URL',
                $channelProducts->channel_products
            );
            return $channelProducts;
        }
        $toDelete = [];
        $toTouch  = [];
        foreach ($channelProducts->channel_products as $product) {
            if ($product->delete) {
                $toDelete[] = $product;
            } else {
                $toTouch[] = $product;
            }
        }
        $api = new DemoAPI\API($url);
        Sync::touchProducts($api, $toTouch);
        Sync::deleteProducts($api, $toDelete);
        return $channelProducts;
    }

    public function get(
        string $channel_product_code,
        int $limit,
        DTO\Channel $channel
    ): DTO\ChannelProducts {
        $meta = new Meta($channel);
        $url  = $meta->get(Meta::CHANNEL_META_URL_KEY);
        if (!$url) {
            Logger::LogProductGet(
                DTO\Log::LOG_LEVEL_ERROR,
                'Invalid URL',
                $channel
            );
            return new DTO\ChannelProducts([]);
        }

        $api = new DemoAPI\API($url);
        try {
            // get product data from the channel specified
            $products = $api->getProducts($channel_product_code, $limit);

            // transform DemoProduct data into ChannelProducts
            return Transform::DemoProductToDto($products);
        } catch (GuzzleException $e) {
            return new DTO\ChannelProducts([]);
        }
    }

    public function getByCode(
        DTO\ChannelProducts $channelProducts,
        DTO\Channel $channel
    ): DTO\ChannelProducts {
        $meta = new Meta($channel);
        $url  = $meta->get(Meta::CHANNEL_META_URL_KEY);
        if (!$url) {
            Logger::LogProductGet(
                DTO\Log::LOG_LEVEL_ERROR,
                'Invalid URL',
                $channel
            );
            return new DTO\ChannelProducts([]);
        }

        // channel only allows us to read products by channel_product_code or in pages
        // get channel_product_codes
        $codes = [];
        foreach ($channelProducts->channel_products as $product) {
            if (isset($product->channel_product_code)) {
                $codes[] = $product->channel_product_code;
            }
        }

        $api = new DemoAPI\API($url);
        try {
            // get product data from the channel specified
            $products = $api->getProductsByCodes($codes);

            // transform DemoProduct data into ChannelProducts
            return Transform::DemoProductToDto($products);
        } catch (GuzzleException $e) {
            return new DTO\ChannelProducts([]);
        }
    }


}
