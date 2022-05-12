<?php

namespace stock2shop\lib;

use stock2shop\vo;
use stock2shop\exceptions;

/**
 * Log Writer Factory
 * @package stock2shop\lib
 */
final class LogWriterFactory
{
    /** @var string The base class path for log writers. */
    const LOG_WRITER_CLASS_PATH = 'stock2shop\\lib\\LogWriter';

    /**
     * Create
     *
     * Returns a LogWriter object of the specified type.
     *
     * @param string $type
     * @param vo\Channel $channel
     * @return LogWriter
     * @throws exceptions\NotImplemented
     */
    public static function create(string $type, vo\Channel $channel): LogWriter
    {
        $writerClass = self::LOG_WRITER_CLASS_PATH .  ucfirst($type);
        if (!class_exists($writerClass)) {
            throw new exceptions\NotImplemented('Unable to create log writer object - class does not exist yet.');
        }
        return new $writerClass($channel);
    }
}