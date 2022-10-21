<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\Log;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use InvalidArgumentException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Maxbanton\Cwh\Handler\CloudWatch;
use Stock2Shop\Connector\Config\Environment;
use Stock2Shop\Connector\ENV;
use Stock2Shop\Share\Config;
use Stock2Shop\Share\DTO\Log;
use Stock2Shop\Share\Utils\Date;

class Writer
{
    public Logger $logger;

    public function __construct(Environment $env)
    {
        $this->logger = new Logger($env->getLogChannel());
        if (
            $env->getCWKey() &&
            $env->getCWSecret()
        ) {
            $handler = $this->handlerCloudWatch($env);
        } else {
            if (
                $env->getLogFSDIR() &&
                $env->getLogFSFileName()
            ) {
                $handler = $this->handlerFile($env);
            }
        }
        if (!isset($handler)) {
            throw new InvalidArgumentException('Logging not configured');
        }
        $formatter = new JsonFormatter();
        $handler->setFormatter($formatter);
        $this->logger->pushHandler($handler);
    }

    public function write(int $level, Log $log): void
    {
        $this->logger->addRecord($level, $log->message, (array)$log);
    }

    private function handlerCloudWatch(Environment $env): CloudWatch
    {
        $client = new CloudWatchLogsClient([
            'version'     => $env->getCWVersion(),
            'region'      => $env->getCWRegion(),
            'credentials' => [
                'key'    => $env->getCWKey(),
                'secret' => $env->getCWSecret()
            ]
        ]);
        return new CloudWatch(
            $client,
            $env->getCWGroupName(),
            substr(Date::getDateString(Date::FORMAT), 0, 10),
            $env->getCWRetentionDays(),
            $env->getCWBatchSize()
        );
    }

    private function handlerFile(Environment $env): StreamHandler
    {
        $dir  = $env->getLogFSDIR();
        $file = $env->getLogFSFileName();
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $path = sprintf('%s/%s', $dir, $file);
        return new StreamHandler($path);
    }
}
