<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
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
            Log::syncChannelProductsFailed($channelProducts->channel_products);
            return $channelProducts;
        }

        // batch updates and deletes
        /** @var Share\DTO\ChannelProduct[] $toDelete */
        $toDelete = [];
        /** @var Share\DTO\ChannelProduct[] $toTouch */
        $toTouch = [];
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
        Log::syncChannelProductsSuccess(array_merge($toDelete, $toTouch));
        return $channelProducts;
    }

    public function get(
        string $channel_product_code,
        int $limit,
        Share\DTO\Channel $channel
    ): Share\DTO\ChannelProducts {
        $meta = new Meta($channel);
        $url  = $meta->get(Meta::CHANNEL_META_URL_KEY);
        if (!$url) {
            Log::channelException(
                new InvalidArgumentException(sprintf('Missing Meta %s', Meta::CHANNEL_META_URL_KEY)),
                $channel->id,
                $channel->client_id
            );
            return new Share\DTO\ChannelProducts([]);
        }

        // Get product data from the channel specified
        $api = new DemoAPI\API($url);
        try {
            $products = $api->getProducts($channel_product_code, $limit);
        } catch (GuzzleException $e) {
            Log::channelException($e, $channel->id, $channel->client_id);
            return new Share\DTO\ChannelProducts([]);
        }

        // Transform DemoProduct data into ChannelProducts
        return Transform::getChannelProducts($products);
    }

    public function getByCode(
        Share\DTO\ChannelProducts $channelProducts,
        Share\DTO\Channel $channel
    ): Share\DTO\ChannelProducts {
        $meta = new Meta($channel);
        $url  = $meta->get(Meta::CHANNEL_META_URL_KEY);
        if (!$url) {
            Log::channelException(
                new InvalidArgumentException(sprintf('Missing Meta %s', Meta::CHANNEL_META_URL_KEY)),
                $channel->id,
                $channel->client_id
            );
            return new Share\DTO\ChannelProducts([]);
        }

        // Demo API fetches products by ID
        $ids = Transform::getDemoProductIDS($channelProducts->channel_products);
        $api = new DemoAPI\API($url);
        try {
            $products = $api->getProductsByIDS($ids);
        } catch (GuzzleException $e) {
            Log::channelException($e, $channel->id, $channel->client_id);
            return new Share\DTO\ChannelProducts([]);
        }

        // Transform DemoProduct data into ChannelProducts
        return Transform::getChannelProducts($products);
    }
}
