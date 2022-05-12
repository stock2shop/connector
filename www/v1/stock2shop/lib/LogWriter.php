<?php

namespace stock2shop\lib;

use \Monolog\Logger;
use stock2shop\vo;

/**
 * Log Writer Interface
 *
 * This interface prescribes a common interface which all
 * LogWriter objects must implement in order to be used
 * by connector code.
 *
 * Stock2Shop has very specific logging conventions, which
 * include:-
 *
 * - Logs must have a flat key value structure with as little
 *   nested properties as possible.
 *
 * - Logs must include the "client_id" property in order to be
 *   searchable.
 *
 * - Logs must set the "origin" property when the information
 *   being logged corresponds to a CWL logging group.
 *
 * - Logs may set the "tags" property - it must always be an
 *   array of strings. This is used to filter log lines on
 *   our admin console application.
 *
 * - Logs may set the 'route' property when an API route is
 *   applicable.
 *
 * - Log all errors in your code, but try to keep info logs to
 *   a minimum.
 *
 * - Logs must never include sensitive information such as
 *   passwords or secrets. Use the `sanitize()` method on data
 *   before writing the log line.
 */
interface LogWriter
{
    /** @var int Log level error. */
    const LOG_LEVEL_ERROR = Logger::ERROR;

    /** @var int Log level warning. */
    const LOG_LEVEL_WARNING = Logger::WARNING;

    /** @var int Log level info. */
    const LOG_LEVEL_INFO = Logger::INFO;

    /** @var int Log level debug. */
    const LOG_LEVEL_DEBUG = Logger::DEBUG;

    /** @var string Default string mask to use in sanitize replacement. */
    const LOG_WRITER_SANITIZE_MASK = 'xxx';

    /** @var array Constant defining which patterns to replace when sanitizing data. */
    const LOG_WRITER_SANITIZE_PATTERNS = [
        'token',
        'consumer_secret',
        'consumer_key',
        'password',
        'username',
        'secret',
        'key',
        'api_key',
        'api_secret',
        'login',
        'hmac_shared_secret',
        'ftp_user_password',
        'ftp_user_name'
//        'soap_username'
//        'soap_password',
    ];

    const LOG_WRITER_SEVERITY_LEVELS = [
        LogWriter::LOG_LEVEL_INFO => 'INFO',
        LogWriter::LOG_LEVEL_DEBUG => 'DEBUG',
        LogWriter::LOG_LEVEL_ERROR => 'ERROR',
        LogWriter::LOG_LEVEL_WARNING => 'WARN'
    ];

    /**
     * Construct
     *
     * Sets up the log writer with optional config parameters.
     *
     * @param vo\Channel $channel
     * @return void
     */
    public function __construct(vo\Channel $channel);

    /**
     * Write
     *
     * Writes a log to the configured logging handler.
     * First set the values on a LogItem object and then
     * pass it to this method.
     *
     * This method must define the workflow for creating
     * a new log line using the target.
     *
     * @param LogItem $item
     * @return void
     */
    public function write(LogItem $item);

    /**
     * Flush
     *
     * This method flushes the items in the "items" class
     * property to the configured logging target of the
     * LogWriter class.
     *
     * This method must write the logs to the destination
     * using the $handler configured and clear the $items
     * property.
     *
     * @return void
     */
    public function flush();
}