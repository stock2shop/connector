<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Stock2Shop\Share\DTO;
use Stock2Shop\Share;

class Sync
{
    /**
     * @param DTO\ChannelProduct[] $channelProducts
     */
    public static function touchProducts(DemoAPI\API $api, array $channelProducts, DTO\Channel $channel): array
    {
        SyncResults::setFailed($channelProducts);

        /** @var Share\DTO\ChannelProduct[] $delete */
        $delete = [];
        /** @var Share\DTO\ChannelProduct[] $touch */
        $touch = [];
        foreach ($channelProducts as $product) {
            if ($product->delete) {
                $delete[] = $product;
            } else {
                $touch[] = $product;
            }
        }
        if (!empty($delete)) {
            self::deleteProducts($api, $delete, $channel);
        }

        if (!empty($touch)) {
            // transform
            try {
                $body = Transform::getDemoProducts($touch);
            } catch (Exception $e) {
                Log::channelException($e, $channel->id, $channel->client_id);
                return array_merge($delete, $touch);
            }

            // Post to Demo API
            try {
                $dps = $api->postProducts($body);
            } catch (GuzzleException $e) {
                Log::channelException($e, $channel->id, $channel->client_id);
                return array_merge($delete, $touch);
            }
            SyncResults::setSuccess($channelProducts, $dps);
        }
        return array_merge($touch, $delete);
    }

    /**
     * @param DTO\ChannelProduct[] $channelProducts
     */
    private static function deleteProducts(DemoAPI\API $api, array $channelProducts, DTO\Channel $channel): void
    {
        // transform
        try {
            $body = Transform::getDemoProductIDS($channelProducts);
        } catch (Exception $e) {
            Log::channelException($e, $channel->id, $channel->client_id);
            return;
        }

        // Post to Demo API
        try {
            $api->deleteProducts($body);
        } catch (GuzzleException $e) {
            Log::channelException($e, $channel->id, $channel->client_id);
            return;
        }
        SyncResults::setDeleteSuccess($channelProducts);
    }
}
