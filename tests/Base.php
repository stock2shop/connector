<?php

declare(strict_types=1);

namespace Stock2Shop\Tests\Connector;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Stock2Shop\Connector\Meta;
use Stock2Shop\Environment\Env;
use Stock2Shop\Environment\LoaderDotenv;
use Stock2Shop\Logger;
use Stock2Shop\Share;

class Base extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        Env::set(
            new LoaderDotenv(__DIR__ . '/../')
        );

        // Delete products from channel
        $channel = new Share\DTO\Channel($this->getTestDataChannel());
        $meta    = new Meta($channel);
        $client  = new Client([
            'base_uri' => $meta->get(Meta::CHANNEL_META_URL_KEY)
        ]);
        $client->request('DELETE', '/clean');

        // Clear logs
        if (file_exists($this->getLogPath())) {
            unlink($this->getLogPath());
        }
    }

    protected function getLogPath(): string
    {
        return sprintf(
            '%s/%s',
            Env::get(Logger\EnvKey::LOG_FS_DIR),
            Env::get(Logger\EnvKey::LOG_FS_FILE_NAME)
        );
    }

    protected function getLogs(): array
    {
        $logs  = file_get_contents($this->getLogPath());
        $parts = explode("\n", $logs);
        // last log is empty line
        array_pop($parts);
        return $parts;
    }

    protected function getTestDataChannel(): array
    {
        return $this->loadJSON('channel.json');
    }

    protected function getTestDataChannelProducts(): array
    {
        return $this->loadJSON('channelProducts.json');
    }

    private function loadJSON(string $filename): array
    {
        $path = __DIR__ . '/data/' . $filename;
        return json_decode(file_get_contents($path), true);
    }
}
