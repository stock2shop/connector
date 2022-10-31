<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Share\DTO;
use Stock2Shop\Share;

class ChannelOrders implements Share\Channel\ChannelOrdersInterface
{
    private const CUSTOM_TRANSFORM_PREFIX = 'order_transform_';

    /**
     * @param DTO\ChannelOrderWebhook[] $channelOrderWebhooks
     * @return DTO\ChannelOrder[]
     */
    public function transform(array $channelOrderWebhooks, DTO\Channel $channel): array
    {
        $demoOrders = TransformOrders::getDemoOrders($channelOrderWebhooks);
        if ($demoOrders == []) {
            Logger::LogOrderTransformFailed(null, "unable to read order data", $channel);
        }
        $channelOrders = TransformOrders::getChannelOrders($demoOrders);

        foreach ($channel->meta as $cm) {
            if (str_contains($cm->key, self::CUSTOM_TRANSFORM_PREFIX)) {
                // we need to do a custom transform
                foreach ($channelOrders as $index => $co) {
                    // get field that needs a custom value to be set
                    $field                 = substr($cm->key, strlen(self::CUSTOM_TRANSFORM_PREFIX));
                    $data                  = (array)$co;
                    $data[$field]          = $cm->value;
                    $orderAsArray          = json_decode(json_encode($demoOrders[$index]), true);
                    $transform             = TransformOrders::getChannelOrdersTemplate(json_encode($data), $orderAsArray);
                    $channelOrders[$index] = new DTO\ChannelOrder(json_decode($transform, true));
                }
            }
        }

        Logger::LogOrderTransform($channelOrders, $channel);
        return $channelOrders;
    }
}
