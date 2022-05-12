<?php

namespace stock2shop\lib;

use stock2shop\vo;
use stock2shop\exceptions;

/**
 * Log Writer Debug
 *
 * This is a concrete class implementation
 * of the LogWriter interface which logs all
 * LogItems to stdout.
 *
 * @package stock2shop\lib
 */
class LogWriterDebug implements LogWriter
{
    /** @var LogItem[] $items The log lines written to the target. */
    public array $items = [];

    /**
     * @inheritDoc
     */
    public function __construct(vo\Channel $channel)
    {
        // TODO: add config for the log writer type.
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
        foreach ($this->items as $iKey => $item) {
            print_r(json_encode(['level'=>$item->getLevel(), 'message'=>$item->message, 'context'=>$item->context]));
            print_r(PHP_EOL);
            unset($this->items[$iKey]);
        }
    }
}