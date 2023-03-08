<?php

declare(strict_types=1);

namespace Stock2Shop\Tests\Connector\integration;

use Generator;
use Stock2Shop\Connector\ChannelOrders;
use Stock2Shop\Connector\Meta;
use Stock2Shop\Share\DTO;
use Stock2Shop\Tests\Connector\Base;

final class ChannelOrderTest extends Base
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @dataProvider transformDataProvider
     * @param DTO\ChannelOrderWebhook[] $webhooks
     * @param DTO\ChannelOrder[] $dtoResults
     * @param DTO\Channel $channel
     */

    public function testTransform(array $webhooks, array $dtoResults, DTO\Channel $channel): void
    {
        $co      = new ChannelOrders();
        $orders  = $co->transform($webhooks, $channel);
        $this->assertEqualsCanonicalizing($dtoResults, $orders);
    }

    private function transformDataProvider(): Generator
    {
        $order1         = $this->getTestDataOrderWebhook1();
        $order2         = $this->getTestDataOrderWebhook2();
        $webhook1 = new DTO\ChannelOrderWebhook([
            'storage_code' => 'foo',
            'payload'      => json_encode($order1)
        ]);
        $webhook2 = new DTO\ChannelOrderWebhook([
            'storage_code' => 'bar',
            'payload'      => json_encode($order2)
        ]);
        $channel = new DTO\Channel($this->getTestDataChannel());
        $resultingDTO1 = new DTO\ChannelOrder($this->getTestDataOrderDTO1());
        $resultingDTO2 = new DTO\ChannelOrder($this->getTestDataOrderDTO2());
        $resultingDTO1WithTemplate = new DTO\ChannelOrder($this->getTestDataOrderDTO1WithTemplate());
        yield 'multiple orders' => [
            [
                $webhook1,
                $webhook2
            ],
            [
                $resultingDTO1,
                $resultingDTO2
            ],
            $channel
        ];
        yield 'single order' => [
            [
                $webhook1
            ],
            [
                $resultingDTO1
            ],
            $channel
        ];

        // add in map
        $channel->meta = DTO\Meta::createArray(
            [
                [
                    'key'   => Meta::CHANNEL_ORDER_TEMPLATE,
                    'value' => $this->getTestChannelOrderTemplate()
                ]
            ]
        );
        yield 'single order template' => [
            [
                $webhook1
            ],
            [
                $resultingDTO1WithTemplate
            ],
            $channel
        ];
    }
}
