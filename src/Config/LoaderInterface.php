<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\Config;

/**
 * Must set environment Vars
 * Must not override existing
 */
interface LoaderInterface
{
    public function set(): void;
}
