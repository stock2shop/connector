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
            Logger::LogProductSyncFailed($channelProducts->channel_products, 'Invalid URL', $channel);
            return $channelProducts;
        }

        // batch updates and deletes
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
        Sync::touchProducts($api, $toTouch, $channel);
        Sync::deleteProducts($api, $toDelete, $channel);
        Logger::LogProductSync(array_merge($toDelete, $toTouch), $channel);
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

        // Get product data from the channel specified
        $api = new DemoAPI\API($url);
        try {
            $products = $api->getProducts($channel_product_code, $limit);
        } catch (GuzzleException) {
            return new DTO\ChannelProducts([]);
        }

        // Transform DemoProduct data into ChannelProducts
        return TransformProducts::getChannelProducts($products);
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

        // Demo API fetches products by ID
        $ids = TransformProducts::getDemoProductIDS($channelProducts->channel_products);
        $api = new DemoAPI\API($url);
        try {
            $products = $api->getProductsByIDS($ids);
        } catch (GuzzleException $e) {
            return new DTO\ChannelProducts([]);
        }

        // Transform DemoProduct data into ChannelProducts
        return TransformProducts::getChannelProducts($products);
    }
}
