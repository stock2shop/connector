<?php

namespace stock2shop\lib;

use stock2shop\vo;
use stock2shop\helpers;
use stock2shop\exceptions;

/**
 * Log Writer Files
 *
 * This class is a concrete implementation of the
 * LogWriter interface. It writes the log items to
 * a specified file path.
 *
 * @package stock2shop\lib
 */
class LogWriterFiles implements LogWriter
{
    /** @var string $filePath The path to write the file to. */
    public string $filePath;

    /** @var LogItem[] $items The log lines written to the target. */
    public array $items = [];

    /**
     * @inheritDoc
     */
    public function __construct(vo\Channel $channel)
    {
        $this->filePath = helpers\Meta::get($channel->meta, 'log_file_path');
        if (!$this->filePath) {
            throw new exceptions\Validation('Failed to create LogWriterFiles object - missing "log_file_path" config value.');
        }
    }

    /**
     * @inheritDoc
     */
    public function write(LogItem $item)
    {
        // Add LogItem to the items property for debugging.
        $item->sanitize();
        $this->items[] = $item;
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $file = time() . '-' . $this->filePath;
        $items = [];
        foreach ($this->items as $iKey => $item) {
            $item->sanitize();
            $items[] = $item;
            unset($this->items[$iKey]);
        }
        file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }
}