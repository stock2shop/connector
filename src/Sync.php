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
     * Runs updates to channel.
     * Mutates $channelProducts with success results
     *
     * @param DTO\ChannelProduct[] $channelProducts
     */
    public static function touchProducts(DemoAPI\API $api, array $channelProducts, DTO\Channel $channel): void
    {
        // make sure all success flags are set to false
        SyncResults::setFailed($channelProducts);

        // create reference to items which are for delete and for update
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

        // run deletes
        if (!empty($delete)) {
            self::deleteProducts($api, $delete, $channel);
        }

        // run updates
        if (!empty($touch)) {
            try {
                $body = Transform::getDemoProducts($touch);
            } catch (Exception $e) {
                Log::channelException($e, $channel->id, $channel->client_id);
                return;
            }
            try {
                $dps = $api->postProducts($body);
            } catch (GuzzleException $e) {
                Log::channelException($e, $channel->id, $channel->client_id);
                return;
            }
            SyncResults::setSuccess($touch, $dps);
        }
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
