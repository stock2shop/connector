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

    protected function getTestDataOrderWebhook1(): array
    {
        return $this->loadJSON('orderWebhook1.json');
    }

    protected function getTestDataOrderWebhook2(): array
    {
        return $this->loadJSON('orderWebhook2.json');
    }

    protected function getTestDataOrderDTO1(): array
    {
        return $this->loadJSON('orderDTO1.json');
    }

    protected function getTestDataOrderDTO1WithTemplate(): array
    {
        return $this->loadJSON('orderDTO1_template.json');
    }

    protected function getTestDataOrderDTO2(): array
    {
        return $this->loadJSON('orderDTO2.json');
    }

    protected function getTestChannelOrderTemplate(): string
    {
        return $this->loadJString('channelOrderTemplate.json');
    }

    private function loadJSON(string $filename): array
    {
        $path = __DIR__ . '/data/' . $filename;
        return json_decode(file_get_contents($path), true);
    }

    private function loadJString(string $filename): string
    {
        $path = __DIR__ . '/data/' . $filename;
        return file_get_contents($path);
    }
}
