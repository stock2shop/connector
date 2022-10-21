<?php

declare(strict_types=1);

namespace Stock2Shop\Tests\Connector;

use GuzzleHttp\Client;
use Stock2Shop\Connector\DemoAPI\API;
use Stock2Shop\Connector\ChannelCreator;
use Stock2Shop\Connector\DemoAPI\Option;
use Stock2Shop\Connector\DemoAPI\Product;
use Stock2Shop\Connector\Helper;
use Stock2Shop\Share;

final class ChannelTest extends Base
{
    public function testSync()
    {
        self::cleanupDataDir();

        $creator         = new ChannelCreator();
        $con             = $creator->createChannelProducts();
        $channel         = new Share\DTO\Channel($this->getTestDataChannel());
        $channelProducts = new Share\DTO\ChannelProducts($this->getTestDataChannelProducts());

        // sync all
        $cps = $con->sync($channelProducts, $channel);
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

        // set one product to delete and sync again
        $cps->channel_products[1]->delete = true;
        $cps2                             = $con->sync($cps, $channel);
        $this->assertTrue($cps->channel_products[0]->success);
        $this->assertNotEmpty($cps2->channel_products[0]->channel_product_code);
        $this->assertTrue($cps2->channel_products[0]->variants[0]->success);
        $this->assertNotEmpty($cps2->channel_products[0]->variants[0]->channel_variant_code);
        $this->assertTrue($cps2->channel_products[0]->images[0]->success);
        $this->assertNotEmpty($cps2->channel_products[0]->images[0]->channel_image_code);
        $this->assertTrue($cps2->channel_products[1]->success);
        $this->assertEmpty($cps2->channel_products[1]->channel_product_code);
        $this->assertTrue($cps2->channel_products[1]->variants[0]->success);
        $this->assertTrue($cps2->channel_products[1]->variants[1]->success);
        $this->assertEmpty($cps2->channel_products[1]->variants[0]->channel_variant_code);
        $this->assertEmpty($cps2->channel_products[1]->variants[1]->channel_variant_code);
        $this->assertTrue($cps2->channel_products[1]->images[0]->success);
        $this->assertTrue($cps2->channel_products[1]->images[1]->success);
        $this->assertEmpty($cps2->channel_products[1]->images[0]->channel_image_code);
        $this->assertEmpty($cps2->channel_products[1]->images[1]->channel_image_code);
    }

    public function testGetByCode()
    {
        self::cleanupDataDir();

        $creator = new ChannelCreator();
        $con     = $creator->createChannelProducts();
        $channel = new Share\DTO\Channel($this->getTestDataChannel());
        $cps     = $con->sync(
            new Share\DTO\ChannelProducts($this->getTestDataChannelProducts()),
            $channel
        );

        // channel_product_codes should be set
        foreach ($cps->channel_products as $cp) {
            $this->assertNotEmpty($cp->channel_product_code);
        }

        // reset flags
        foreach ($cps->channel_products as $cp) {
            $cp->success = null;
            foreach ($cp->variants as $v) {
                $v->success = null;
            }
            foreach ($cp->images as $i) {
                $i->success = null;
            }
        }

        $existing = $con->getByCode($cps, $channel);
        $this->assertSameSize(
            $existing->channel_products,
            $cps->channel_products
        );


        foreach ($cps->channel_products as $k => $cp) {
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

    public function testGet()
    {
        self::cleanupDataDir();

        $creator         = new ChannelCreator();
        $con             = $creator->createChannelProducts();
        $channel         = new Share\DTO\Channel($this->getTestDataChannel());
        $channelProducts = new Share\DTO\ChannelProducts($this->getTestDataChannelProducts());
        $con->sync($channelProducts, $channel);

        $result1 = $con->get('', 1, $channel);
        $result2 = $con->get($result1->channel_products[0]->channel_product_code, 1, $channel);
        $result3 = $con->get($result2->channel_products[0]->channel_product_code, 1, $channel);
        $this->assertCount(1, $result1->channel_products);
        $this->assertCount(1, $result2->channel_products);
        $this->assertCount(0, $result3->channel_products);
        $this->assertGreaterThan('', $result1->channel_products[0]->channel_product_code);
        $this->assertGreaterThan(
            $result1->channel_products[0]->channel_product_code,
            $result2->channel_products[0]->channel_product_code
        );
    }

    private function cleanupDataDir()
    {
        // get channel so we can get the base url
        $channel = new Share\DTO\Channel($this->getTestDataChannel());
        foreach ($channel->meta as $m) {
            if ($m->key === 'api_url') {
                $client = new Client([
                    'base_uri' => $m->value
                ]);
                $client->request('DELETE', '/clean');
                return;
            }
        }
    }
}
