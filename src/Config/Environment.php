<?php


declare(strict_types=1);

namespace Stock2Shop\Connector\Config;

class Environment
{
    public static function set(LoaderInterface $loader): void
    {
        $loader->set();
    }

    public static function getLogChannel(): string|false
    {
        return self::get('LOG_CHANNEL');
    }

    public static function getCWKey(): string|false
    {
        return self::get('LOG_CW_KEY');
    }

    public static function getCWSecret(): string|false
    {
        return self::get('LOG_CW_SECRET');
    }

    public static function getCWVersion(): string|false
    {
        return self::get('LOG_CW_VERSION');
    }

    public static function getCWRegion(): string|false
    {
        return self::get('LOG_CW_REGION');
    }

    public static function getCWGroupName(): string|false
    {
        return self::get('LOG_CW_GROUP_NAME');
    }

    public static function getCWRetentionDays(): string|false
    {
        return self::get('LOG_CW_RETENTION_DAYS');
    }

    public static function getCWBatchSize(): string|false
    {
        return self::get('LOG_CW_BATCH_SIZE');
    }

    public static function getLogFSDIR(): string|false
    {
        return self::get('LOG_FS_DIR');
    }

    public static function getLogFSFileName(): string|false
    {
        return self::get('LOG_FS_FILE_NAME');
    }

    public static function get(string $key): string|false
    {
        if (
            isset($_SERVER[$key]) &&
            is_string($_SERVER[$key]) &&
            $_SERVER[$key] !== ''
        ) {
            return $_SERVER[$key];
        }
        return false;
    }
}
