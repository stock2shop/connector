<?php


declare(strict_types=1);

namespace Stock2Shop\Connector\Config;

class Environment
{
    private string|false $log_channel = false;
    private string|false $log_cw_key = false;
    private string|false $log_cw_secret = false;
    private string|false $log_cw_version = false;
    private string|false $log_cw_region = false;
    private string|false $log_cw_group_name = false;
    private string|false $log_cw_retention_days = false;
    private string|false $log_cw_batch_size = false;
    private string|false $log_fs_dir = false;
    private string|false $log_fs_file_name = false;

    public function __construct($conf)
    {
        // set config if applicable
        foreach ($conf as $key => $value) {
            $prop = strtolower($key);
            if (property_exists(Environment::class, $prop)) {
                $this->{$prop} = $value;
            }
        }
    }

    public function getLogChannel(): string|false
    {
        return $this->log_channel;
    }

    public function getCWKey(): string|false
    {
        return $this->log_cw_key;
    }

    public function getCWSecret(): string|false
    {
        return $this->log_cw_secret;
    }

    public function getCWVersion(): string|false
    {
        return $this->log_cw_version;
    }

    public function getCWRegion(): string|false
    {
        return $this->log_cw_region;
    }

    public function getCWGroupName(): string|false
    {
        return $this->log_cw_group_name;
    }

    public function getCWRetentionDays(): string|false
    {
        return $this->log_cw_retention_days;
    }

    public function getCWBatchSize(): string|false
    {
        return $this->log_cw_batch_size;
    }

    public function getLogFSDIR(): string|false
    {
        return $this->log_fs_dir;
    }

    public function getLogFSFileName(): string|false
    {
        return $this->log_fs_file_name;
    }
}