<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Logger;
use Stock2Shop\Share\DTO\ChannelProduct;
use Throwable;

class Log
{
    /**
     * Log failed and successful
     * @param ChannelProduct[] $channelProducts
     */
    public static function syncChannelProductsResults(array $channelProducts): void
    {
        /** @var ChannelProduct[] $failed */
        $failed = [];
        /** @var ChannelProduct[] $successful */
        $successful = [];
        foreach ($channelProducts as $channelProduct) {
            if ($channelProduct->success) {
                $successful[] = $channelProduct;
            } else {
                $failed[] = $channelProduct;
            }
        }
        if (!empty($failed)) {
            Logger\ChannelProductsFail::log($failed);
        }
        if (!empty($successful)) {
            Logger\ChannelProductsSuccess::log($channelProducts);
        }
    }


    /**
     * @param ChannelProduct[] $channelProducts
     */
    public static function syncChannelProductsFailed(array $channelProducts): void
    {
        Logger\ChannelProductsFail::log($channelProducts);
    }

    /**
     * @param ChannelProduct[] $channelProducts
     */
    public static function syncChannelProductsSuccess(array $channelProducts): void
    {
        Logger\ChannelProductsSuccess::log($channelProducts);
    }

    public static function channelException(Throwable $e, int $channel_id, int $client_id): void
    {
        Logger\Exception::log(
            $e,
            [
                'channel_id' => $channel_id,
                'client_id'  => $client_id
            ]
        );
    }
}
