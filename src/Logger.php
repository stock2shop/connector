<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Connector\Log\Writer;
use Stock2Shop\Share\DTO;
use Stock2Shop\Share;

class Logger
{
    /**
     * @param DTO\ChannelProduct[] $channelProducts
     */
    public static function LogProductSync(array $channelProducts, DTO\Channel $channel): void
    {
        $log          = self::getChannelProductsBaseLog($channelProducts, $channel);
        $log->message = 'Products Updated';
        $log->tags[]  = 'update';
        self::Write($log);
    }

    public static function LogProductSyncFailed(array $channelProducts, $message, DTO\Channel $channel): void
    {
        $log          = self::getChannelProductsBaseLog($channelProducts, $channel);
        $log->level   = DTO\Log::LOG_LEVEL_ERROR;
        $log->message = $message;
        self::Write($log);
    }

    public static function LogProductGet(string $level, string $message, DTO\Channel $channel): void
    {
        $log = new DTO\Log([
            'channel_id' => $channel->id,
            'client_id'  => $channel->client_id,
            'log_to_es'  => true,
            'level'      => $level,
            'message'    => $message,
            'origin'     => 'Demo',
            'tags'       => ['get_channel_products']
        ]);
        self::Write($log);
    }

    /**
     * @param DTO\ChannelProduct[] $channelProducts
     */
    private static function getChannelProductsBaseLog(array $channelProducts, DTO\Channel $channel): DTO\Log
    {
        return new DTO\Log([
            'channel_id' => $channel->id,
            'client_id'  => $channel->client_id,
            'log_to_es'  => true,
            'message'    => '',
            'level'      => DTO\Log::LOG_LEVEL_INFO,
            'metric'     => count($channelProducts),
            'origin'     => 'Demo',
            'tags'       => ['sync_channel_products']
        ]);
    }

    private static function Write(DTO\Log $log): void
    {
        $writer = new Writer();
        // log writing should not stop the application
        try {
            $writer->write($log);
        } catch (\Exception) {
        }
    }
}
