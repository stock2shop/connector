<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Share;

class DemoAPI
{
    public function __construct(
        private readonly string $url
    )
    {
    }


    public function getProducts(string $fromID, int $limit) {
    }

    public function postProducts($body) {}

    public function deleteProducts($body) {}




}
