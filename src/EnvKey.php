<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

final class EnvKey
{
    /** See monolog channels */
    public const LOG_CHANNEL = 'LOG_CHANNEL';

    /** Cloud watch logging configuration */
    public const LOG_CW_ENABLED = 'LOG_CW_ENABLED';
    public const LOG_CW_KEY = 'LOG_CW_KEY';
    public const LOG_CW_SECRET = 'LOG_CW_SECRET';
    public const LOG_CW_VERSION = 'LOG_CW_VERSION';
    public const LOG_CW_REGION = 'LOG_CW_REGION';
    public const LOG_CW_GROUP_NAME = 'LOG_CW_GROUP_NAME';
    public const LOG_CW_RETENTION_DAYS = 'LOG_CW_RETENTION_DAYS';
    public const LOG_CW_BATCH_SIZE = 'LOG_CW_BATCH_SIZE';

    /** file system logging configuration */
    public const LOG_FS_ENABLED = 'LOG_FS_ENABLED';
    public const LOG_FS_DIR = 'LOG_FS_DIR';
    public const LOG_FS_FILE_NAME = 'LOG_FS_FILE_NAME';
}
