<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use GuzzleHttp\Exception\GuzzleException;
use Stock2Shop\Environment\Env;
use Stock2Shop\Share\DTO;
use Stock2Shop\Share;
use Stock2Shop\Logger;

class ChannelProducts implements Share\Channel\ChannelProductsInterface
{
    public function sync(
        Share\DTO\ChannelProducts $channelProducts,
        Share\DTO\Channel         $channel
    ): Share\DTO\ChannelProducts {
        $meta = new Meta($channel);
        $url  = $meta->get(Meta::CHANNEL_META_URL_KEY);
        if (!$url) {
            SyncResults::setFailed($channelProducts->channel_products);
            Logger\ChannelProductsFail::log($channelProducts->channel_products);
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
        Logger\ChannelProductsSuccess::log((array_merge($toDelete, $toTouch)));
        return $channelProducts;
    }

    public function get(
        string      $channel_product_code,
        int         $limit,
        DTO\Channel $channel
    ): DTO\ChannelProducts {
        $meta = new Meta($channel);
        $url  = $meta->get(Meta::CHANNEL_META_URL_KEY);
        if (!$url) {
            // todo - add to logging library?
            Logger\Custom::log([
                'message'    => 'Invalid URL',
                'level'      => Logger\Domain\Log::LOG_LEVEL_ERROR,
                'origin'     => Env::get(EnvKey::LOG_CHANNEL),
                'channel_id' => $channel->id,
                'client_id'  => $channel->client_id,
                'log_to_es'  => true,
                'tags'       => [
                    'get_channel_products'
                ]
            ]);
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
        DTO\Channel         $channel
    ): DTO\ChannelProducts {
        $meta = new Meta($channel);
        $url  = $meta->get(Meta::CHANNEL_META_URL_KEY);
        if (!$url) {
            Logger\Custom::log([
                'message'    => 'Invalid URL',
                'level'      => Logger\Domain\Log::LOG_LEVEL_ERROR,
                'origin'     => Env::get(EnvKey::LOG_CHANNEL),
                'channel_id' => $channel->id,
                'client_id'  => $channel->client_id,
                'log_to_es'  => true,
                'tags'       => [
                    'get_channel_products'
                ]
            ]);
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
