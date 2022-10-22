<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\Log;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use InvalidArgumentException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Maxbanton\Cwh\Handler\CloudWatch;
use Stock2Shop\Connector\Config\Environment;
use Stock2Shop\Share\DTO;
use Stock2Shop\Share\Utils\Date;

class Writer
{
    public const LOG_LEVEL_MAP = [
        DTO\Log::LOG_LEVEL_ERROR    => Logger::ERROR,
        DTO\Log::LOG_LEVEL_DEBUG    => Logger::DEBUG,
        DTO\Log::LOG_LEVEL_INFO     => Logger::INFO,
        DTO\Log::LOG_LEVEL_CRITICAL => Logger::CRITICAL,
        DTO\Log::LOG_LEVEL_WARNING  => Logger::WARNING,
    ];

    public Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger(Environment::getLogChannel());
        if (
            Environment::getCWKey() &&
            Environment::getCWSecret()
        ) {
            $handler = $this->handlerCloudWatch();
        } else {
            if (
                Environment::getLogFSDIR() &&
                Environment::getLogFSFileName()
            ) {
                $handler = $this->handlerFile();
            }
        }
        if (!isset($handler)) {
            throw new InvalidArgumentException('Logging not configured');
        }
        $formatter = new JsonFormatter();
        $handler->setFormatter($formatter);
        $this->logger->pushHandler($handler);
    }

    public function write(DTO\Log $log): void
    {
        $level = self::LOG_LEVEL_MAP[$log->level];
        $this->logger->addRecord(
            $level,
            $log->message,
            (array)$log
        );
    }

    private function handlerCloudWatch(): CloudWatch
    {
        $client = new CloudWatchLogsClient([
            'version'     => Environment::getCWVersion(),
            'region'      => Environment::getCWRegion(),
            'credentials' => [
                'key'    => Environment::getCWKey(),
                'secret' => Environment::getCWSecret()
            ]
        ]);
        return new CloudWatch(
            $client,
            Environment::getCWGroupName(),
            substr(Date::getDateString(Date::FORMAT), 0, 10),
            Environment::getCWRetentionDays(),
            Environment::getCWBatchSize()
        );
    }

    private function handlerFile(): StreamHandler
    {
        $dir  = Environment::getLogFSDIR();
        $file = Environment::getLogFSFileName();
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $path = sprintf('%s/%s', $dir, $file);
        return new StreamHandler($path);
    }
}
