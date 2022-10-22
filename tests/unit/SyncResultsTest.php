<?php

declare(strict_types=1);

namespace Stock2Shop\Tests\Connector\unit;

use Stock2Shop\Connector\DemoAPI;
use Stock2Shop\Connector\SyncResults;
use Stock2Shop\Share\DTO;
use Stock2Shop\Tests\Connector\Base;

final class SyncResultsTest extends Base
{
    public array $channelProducts = [
        [
            'variants' => [
                [
                    'sku' => '1'
                ],
                [
                    'sku' => '2'
                ]
            ],
            'images'   => [
                [
                    'src' => 'https://a.com/1.jpg'
                ]
            ]
        ],
        [
            'variants' => [
                [
                    'sku' => '3'
                ]
            ],
            'images'   => [
                [
                    'src' => 'https://a.com/2.jpg'
                ],
                [
                    'src' => 'https://a.com/3.jpg'
                ]
            ]
        ]
    ];
    public array $demoProducts = [
        [
            'id'      => 'p-1',
            'name'    => 'Title 1',
            'options' => [
                [
                    'id'  => 'o-1',
                    'sku' => '1'
                ],
                [
                    'id'  => 'o-2',
                    'sku' => '2'
                ]
            ],
            'images'  => [
                [
                    'id'  => 'i-1',
                    'url' => 'https://a.com/1.jpg'
                ]
            ]
        ],
        [
            'id'      => 'p-2',
            'name'    => 'Title 2',
            'options' => [
                [
                    'id'  => 'o-3',
                    'sku' => '3'
                ]
            ],
            'images'  => [
                [
                    'id'  => 'i-2',
                    'url' => 'https://a.com/2.jpg'
                ],
                [
                    'id'  => 'i-3',
                    'url' => 'https://a.com/3.jpg'
                ]
            ]
        ]
    ];

    public function testSetSuccess()
    {
        $cps = DTO\ChannelProduct::createArray($this->channelProducts);
        $dps = DemoAPI\Product::createArray($this->demoProducts);
        SyncResults::setSuccess($cps, $dps);
        $cnt       = 1;
        $cntSKU    = 1;
        $cntImages = 1;
        foreach ($cps as $cp) {
            $this->assertTrue($cp->success);
            $this->assertEquals('p-' . $cnt, $cp->channel_product_code);
            foreach ($cp->variants as $cv) {
                $this->assertTrue($cv->success);
                $this->assertEquals('o-' . $cntSKU, $cv->channel_variant_code);
                $cntSKU++;
            }
            foreach ($cp->images as $ci) {
                $this->assertTrue($ci->success);
                $this->assertEquals('i-' . $cntImages, $ci->channel_image_code);
                $cntImages++;
            }
            $cnt++;
        }
    }
}
