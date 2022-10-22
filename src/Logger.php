<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Connector\Log\Writer;
use Stock2Shop\Share\DTO;
use Stock2Shop\Share;

class Logger
{

    public static function LogProductSync(string $level, string $message, array $channelProducts): void
    {
        $log = new DTO\Log([
            'channel_id' => $channelProducts[0]->channel_id,
            'client_id'  => $channelProducts[0]->client_id,
            'log_to_es'  => true,
            'level'      => $level,
            'message'    => $message,
            'metric'     => count($channelProducts),
            'origin'     => 'Demo',
            'tags'       => ['sync_channel_products']
        ]);
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
