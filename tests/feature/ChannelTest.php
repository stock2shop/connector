<?php

declare(strict_types=1);

namespace Stock2Shop\Tests\Connector\feature;

use GuzzleHttp\Client;
use Stock2Shop\Connector\ChannelCreator;
use Stock2Shop\Connector\Config\Environment;
use Stock2Shop\Connector\Config\LoaderArray;
use Stock2Shop\Connector\Meta;
use Stock2Shop\Share\Channel\ChannelProductsInterface;
use Stock2Shop\Share\DTO;
use Stock2Shop\Tests\Connector\Base;

final class ChannelTest extends Base
{
    /**
     * Feature test
     * Syncs product data to channel.
     * Uses get and getBy to ensure products exist on channel
     * Then runs same again with update and delete
     *
     * @return void
     */
    public function testSync()
    {
        Environment::set(
            new LoaderArray([
                'LOG_CHANNEL'      => 'Share',
                'LOG_FS_DIR'       => sprintf('%s/../output/', __DIR__),
                'LOG_FS_FILE_NAME' => 'system.log'
            ])
        );
        self::cleanup();

        $creator         = new ChannelCreator();
        $con             = $creator->createChannelProducts();
        $channel         = new DTO\Channel($this->getTestDataChannel());
        $channelProducts = new DTO\ChannelProducts($this->getTestDataChannelProducts());

        // sync all
        $cps = $con->sync($channelProducts, $channel);

        // check success flags and codes set
        $this->assertTrue($cps->channel_products[0]->success);
        $this->assertNotEmpty($cps->channel_products[0]->channel_product_code);
        $this->assertTrue($cps->channel_products[0]->variants[0]->success);
        $this->assertNotEmpty($cps->channel_products[0]->variants[0]->channel_variant_code);
        $this->assertTrue($cps->channel_products[0]->images[0]->success);
        $this->assertNotEmpty($cps->channel_products[0]->images[0]->channel_image_code);
        $this->assertTrue($cps->channel_products[1]->success);
        $this->assertNotEmpty($cps->channel_products[1]->channel_product_code);
        $this->assertTrue($cps->channel_products[1]->variants[0]->success);
        $this->assertTrue($cps->channel_products[1]->variants[1]->success);
        $this->assertNotEmpty($cps->channel_products[1]->variants[0]->channel_variant_code);
        $this->assertNotEmpty($cps->channel_products[1]->variants[1]->channel_variant_code);
        $this->assertTrue($cps->channel_products[1]->images[0]->success);
        $this->assertTrue($cps->channel_products[1]->images[1]->success);
        $this->assertNotEmpty($cps->channel_products[1]->images[0]->channel_image_code);
        $this->assertNotEmpty($cps->channel_products[1]->images[1]->channel_image_code);

        // check products exist on channel and logs written
        $this->assertPagingThroughChannel($con, $channel, $cps);
        $this->assertCodesExistOnChannel($con, $channel, $cps);
        $this->assertSyncProductsLogsWritten(1);

        // run sync again
        $cps2 = $con->sync($cps, $channel);
        $this->assertPagingThroughChannel($con, $channel, $cps2);
        $this->assertCodesExistOnChannel($con, $channel, $cps2);
        $this->assertSyncProductsLogsWritten(2);

        // remove one product
        $cps2->channel_products[1]->delete = true;
        $cps3                              = $con->sync($cps2, $channel);
        $this->assertTrue($cps3->channel_products[0]->success);
        $this->assertNotEmpty($cps3->channel_products[0]->channel_product_code);
        $this->assertTrue($cps3->channel_products[0]->variants[0]->success);
        $this->assertNotEmpty($cps3->channel_products[0]->variants[0]->channel_variant_code);
        $this->assertTrue($cps3->channel_products[0]->images[0]->success);
        $this->assertNotEmpty($cps3->channel_products[0]->images[0]->channel_image_code);
        $this->assertTrue($cps3->channel_products[1]->success);
        $this->assertEmpty($cps3->channel_products[1]->channel_product_code);
        $this->assertTrue($cps3->channel_products[1]->variants[0]->success);
        $this->assertTrue($cps3->channel_products[1]->variants[1]->success);
        $this->assertEmpty($cps3->channel_products[1]->variants[0]->channel_variant_code);
        $this->assertEmpty($cps3->channel_products[1]->variants[1]->channel_variant_code);
        $this->assertTrue($cps3->channel_products[1]->images[0]->success);
        $this->assertTrue($cps3->channel_products[1]->images[1]->success);
        $this->assertEmpty($cps3->channel_products[1]->images[0]->channel_image_code);
        $this->assertEmpty($cps3->channel_products[1]->images[1]->channel_image_code);

        // check products exist on channel and logs written
        $this->assertPagingThroughChannel($con, $channel, $cps);
        $this->assertCodesExistOnChannel($con, $channel, $cps);
        $this->assertSyncProductsLogsWritten(3);
    }

    public function testFailedSync()
    {
        Environment::set(
            new LoaderArray([
                'LOG_CHANNEL'      => 'Share',
                'LOG_FS_DIR'       => sprintf('%s/../output/', __DIR__),
                'LOG_FS_FILE_NAME' => 'system.log'
            ])
        );
        self::cleanup();

        // create channel without correct meta
        $creator         = new ChannelCreator();
        $con             = $creator->createChannelProducts();
        $channel         = new DTO\Channel([]);
        $channelProducts = new DTO\ChannelProducts($this->getTestDataChannelProducts());
        $cps             = $con->sync($channelProducts, $channel);
        $this->assertFalse($cps->channel_products[0]->success);
        $this->assertEmpty($cps->channel_products[0]->channel_product_code);
        $this->assertFalse($cps->channel_products[0]->variants[0]->success);
        $this->assertEmpty($cps->channel_products[0]->variants[0]->channel_variant_code);
        $this->assertFalse($cps->channel_products[0]->images[0]->success);
        $this->assertEmpty($cps->channel_products[0]->images[0]->channel_image_code);
        $this->assertFalse($cps->channel_products[1]->success);
        $this->assertEmpty($cps->channel_products[1]->channel_product_code);
        $this->assertFalse($cps->channel_products[1]->variants[0]->success);
        $this->assertFalse($cps->channel_products[1]->variants[1]->success);
        $this->assertEmpty($cps->channel_products[1]->variants[0]->channel_variant_code);
        $this->assertEmpty($cps->channel_products[1]->variants[1]->channel_variant_code);
        $this->assertFalse($cps->channel_products[1]->images[0]->success);
        $this->assertFalse($cps->channel_products[1]->images[1]->success);
        $this->assertEmpty($cps->channel_products[1]->images[0]->channel_image_code);
        $this->assertEmpty($cps->channel_products[1]->images[1]->channel_image_code);
        $this->assertFailedSyncProductsLogsWritten(1, $channelProducts);
    }

    private function assertSyncProductsLogsWritten(int $syncCount)
    {
        $logs  = file_get_contents(Environment::getLogFSDIR() . Environment::getLogFSFileName());
        $parts = explode("\n", $logs);
        // extra line at end of logs
        $this->assertCount($syncCount + 1, $parts);
        $this->assertEquals('', $parts[$syncCount]);
        foreach ($parts as $part) {
            if ($part === '') {
                break;
            }
            $obj = json_decode($part, true);
            $this->assertEquals(DTO\Log::LOG_LEVEL_INFO, $obj['level']);
            $this->assertEquals(21, $obj['client_id']);
        }
    }

    private function assertFailedSyncProductsLogsWritten(int $syncCount, DTO\ChannelProducts $channelProducts)
    {
        $logs  = file_get_contents(Environment::getLogFSDIR() . Environment::getLogFSFileName());
        $parts = explode("\n", $logs);
        // extra line at end of logs
        $this->assertCount($syncCount + 1, $parts);
        $this->assertEquals('', $parts[$syncCount]);
        foreach ($parts as $part) {
            if ($part === '') {
                break;
            }
            $obj = json_decode($part, true);
            $this->assertEquals(DTO\Log::LOG_LEVEL_ERROR, $obj['level']);
            $this->assertEquals(count($channelProducts->channel_products), $obj['metric']);
            $this->assertEquals(21, $obj['client_id']);
        }
    }

    private function assertCodesExistOnChannel(
        ChannelProductsInterface $con,
        DTO\Channel $channel,
        DTO\ChannelProducts $channelProducts
    ) {
        $active = [];
        foreach ($channelProducts->channel_products as $cp) {
            if (!$cp->delete) {
                $active[] = $cp;
            }
        }
        $existing = $con->getByCode($channelProducts, $channel);
        $this->assertSameSize(
            $existing->channel_products,
            $active
        );
        foreach ($active as $k => $cp) {
            $this->assertEquals(
                $cp->channel_product_code,
                $existing->channel_products[$k]->channel_product_code
            );
            foreach ($cp->variants as $kv => $v) {
                $this->assertEquals(
                    $v->channel_variant_code,
                    $existing->channel_products[$k]->variants[$kv]->channel_variant_code
                );
            }
            foreach ($cp->images as $ki => $i) {
                $this->assertEquals(
                    $i->channel_image_code,
                    $existing->channel_products[$k]->images[$ki]->channel_image_code
                );
            }
        }
    }

    /**
     * Loops through fetching products one at a time
     */
    private function assertPagingThroughChannel(
        ChannelProductsInterface $con,
        DTO\Channel $channel,
        DTO\ChannelProducts $existingProducts
    ) {
        usort($existingProducts->channel_products, function (DTO\ChannelProduct $a, DTO\ChannelProduct $b) {
            return $a->channel_product_code <=> $b->channel_product_code;
        });
        $channel_product_code = '';
        foreach ($existingProducts->channel_products as $existingProduct) {
            if (!$existingProduct->delete) {
                $result = $con->get($channel_product_code, 1, $channel);
                $this->assertCount(1, $result->channel_products);
                $this->assertGreaterThan($channel_product_code, $result->channel_products[0]->channel_product_code);
                $channel_product_code = $result->channel_products[0]->channel_product_code;
            }
        }
        // last one should return no products
        $result = $con->get($channel_product_code, 1, $channel);
        $this->assertCount(0, $result->channel_products);
    }

    private function cleanup()
    {
        // Delete products from channel
        $channel = new DTO\Channel($this->getTestDataChannel());
        $meta    = new Meta($channel);
        $client  = new Client([
            'base_uri' => $meta->get(Meta::CHANNEL_META_URL_KEY)
        ]);
        $client->request('DELETE', '/clean');

        // clear logs
        $logsPath = Environment::getLogFSDIR() . Environment::getLogFSFileName();
        if (file_exists($logsPath)) {
            unlink($logsPath);
        }
    }
}
