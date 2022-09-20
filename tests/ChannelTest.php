<?php

declare(strict_types=1);

namespace Stock2Shop\Tests\Connector;

use Stock2Shop\Connector\ChannelCreator;
use Stock2Shop\Share;

final class ChannelTest extends Base
{

    public function testSync()
    {
        $creator         = new ChannelCreator();
        $con             = $creator->createChannelProducts();
        $channel         = new Share\DTO\Channel($this->getTestDataChannel());
        $channelProducts = new Share\DTO\ChannelProducts($this->getTestDataChannelProducts());

        // sync all
        $cps = $con->sync($channelProducts, $channel);
        $this->assertTrue($cps->channel_products[0]->channel->success);
        $this->assertNotEmpty($cps->channel_products[0]->channel->channel_product_code);
        $this->assertTrue($cps->channel_products[0]->variants[0]->channel->success);
        $this->assertNotEmpty($cps->channel_products[0]->variants[0]->channel->channel_variant_code);
        $this->assertTrue($cps->channel_products[0]->images[0]->channel->success);
        $this->assertNotEmpty($cps->channel_products[0]->images[0]->channel->channel_image_code);
        $this->assertTrue($cps->channel_products[1]->channel->success);
        $this->assertNotEmpty($cps->channel_products[1]->channel->channel_product_code);
        $this->assertTrue($cps->channel_products[1]->variants[0]->channel->success);
        $this->assertTrue($cps->channel_products[1]->variants[1]->channel->success);
        $this->assertNotEmpty($cps->channel_products[1]->variants[0]->channel->channel_variant_code);
        $this->assertNotEmpty($cps->channel_products[1]->variants[1]->channel->channel_variant_code);
        $this->assertTrue($cps->channel_products[1]->images[0]->channel->success);
        $this->assertTrue($cps->channel_products[1]->images[1]->channel->success);
        $this->assertNotEmpty($cps->channel_products[1]->images[0]->channel->channel_image_code);
        $this->assertNotEmpty($cps->channel_products[1]->images[1]->channel->channel_image_code);

        // sync one, delete the other
        $data                                             = $this->getTestDataChannelProducts();
        $data['channel_products'][1]['channel']['delete'] = true;
        $cps2                                             = $con->sync(new Share\DTO\ChannelProducts($data), $channel);
        $this->assertTrue($cps->channel_products[0]->channel->success);
        $this->assertNotEmpty($cps2->channel_products[0]->channel->channel_product_code);
        $this->assertTrue($cps2->channel_products[0]->variants[0]->channel->success);
        $this->assertNotEmpty($cps2->channel_products[0]->variants[0]->channel->channel_variant_code);
        $this->assertTrue($cps2->channel_products[0]->images[0]->channel->success);
        $this->assertNotEmpty($cps2->channel_products[0]->images[0]->channel->channel_image_code);
        $this->assertTrue($cps2->channel_products[1]->channel->success);
        $this->assertEmpty($cps2->channel_products[1]->channel->channel_product_code);
        $this->assertTrue($cps2->channel_products[1]->variants[0]->channel->success);
        $this->assertTrue($cps2->channel_products[1]->variants[1]->channel->success);
        $this->assertEmpty($cps2->channel_products[1]->variants[0]->channel->channel_variant_code);
        $this->assertEmpty($cps2->channel_products[1]->variants[1]->channel->channel_variant_code);
        $this->assertTrue($cps2->channel_products[1]->images[0]->channel->success);
        $this->assertTrue($cps2->channel_products[1]->images[1]->channel->success);
        $this->assertEmpty($cps2->channel_products[1]->images[0]->channel->channel_image_code);
        $this->assertEmpty($cps2->channel_products[1]->images[1]->channel->channel_image_code);
    }

    public function testGetByCode()
    {
        $creator = new ChannelCreator();
        $con     = $creator->createChannelProducts();
        $channel = new Share\DTO\Channel($this->getTestDataChannel());
        $con->sync(
            new Share\DTO\ChannelProducts($this->getTestDataChannelProducts()),
            $channel
        );

        // get channel codes and reset flags
        $cps = $con->get('', 2, $channel);
        foreach ($cps->channel_products as $cp) {
            $cp->channel->success = null;
            foreach ($cp->variants as $v) {
                $v->channel->success = null;
            }
            foreach ($cp->images as $i) {
                $i->channel->success = null;
            }
        }
        $existing = $con->getByCode($cps, $channel);
        foreach ($cps->channel_products as $k => $cp) {
            $this->assertTrue($existing->channel_products[$k]->channel->success);
            $this->assertEquals(
                $cp->channel->channel_product_code,
                $existing->channel_products[$k]->channel->channel_product_code
            );
            foreach ($cp->variants as $kv => $v) {
                $this->assertTrue($existing->channel_products[$k]->variants[$kv]->channel->success);
                $this->assertEquals(
                    $v->channel->channel_variant_code,
                    $existing->channel_products[$k]->variants[$kv]->channel->channel_variant_code
                );
            }
            foreach ($cp->images as $ki => $i) {
                $this->assertTrue($existing->channel_products[$k]->images[$ki]->channel->success);
                $this->assertEquals(
                    $i->channel->channel_image_code,
                    $existing->channel_products[$k]->images[$ki]->channel->channel_image_code
                );
            }
        }
    }

    public function testGet()
    {
        $creator         = new ChannelCreator();
        $con             = $creator->createChannelProducts();
        $channel         = new Share\DTO\Channel($this->getTestDataChannel());
        $channelProducts = new Share\DTO\ChannelProducts($this->getTestDataChannelProducts());
        $con->sync($channelProducts, $channel);

        $result1 = $con->get('', 1, $channel);
        $result2 = $con->get($result1->channel_products[0]->channel->channel_product_code, 1, $channel);
        $result3 = $con->get($result2->channel_products[0]->channel->channel_product_code, 1, $channel);
        $this->assertCount(1, $result1->channel_products);
        $this->assertCount(1, $result2->channel_products);
        $this->assertCount(0, $result3->channel_products);
        $this->assertTrue($result1->channel_products[0]->channel->success);
        $this->assertTrue($result2->channel_products[0]->channel->success);
        $this->assertGreaterThan('', $result1->channel_products[0]->channel->channel_product_code);
        $this->assertGreaterThan(
            $result1->channel_products[0]->channel->channel_product_code,
            $result2->channel_products[0]->channel->channel_product_code
        );
    }
}