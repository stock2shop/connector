<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\Log;

use Monolog\Formatter;
use Stock2Shop\Share\DTO\Log;

class JsonFormatter extends Formatter\JsonFormatter
{
    public function format(array $record): string
    {
        $record = $this->normalize($record);
        $log    = new Log($record["context"]);
        // Transform record to be consistent with Stock2Shop.
        // Property names must be the same.
        // Flatten structure
        foreach ($log as $k => $v) {
            // ignore nulls / empty and context
            if (!empty($v) && $k !== 'context') {
                $record[$k] = $v;
            }
        }
        $record['context'] = $log->context;
        return $this->toJson($record, true) . ($this->appendNewline ? "\n" : '');
    }
}
