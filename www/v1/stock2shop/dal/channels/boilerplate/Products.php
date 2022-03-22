<?php

namespace stock2shop\dal\channels\boilerplate;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\vo;
use stock2shop\exceptions;

/**
 * See comments in stock2shop\dal\channel\Product
 * See readme.md on how to load custom configuration for your channel
 *
 * @package stock2shop\dal\example
 */
class Products implements ProductsInterface
{
    /**
     * See comments in stock2shop\dal\channel\Product::sync
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @param vo\Flag[] $flagsMap
     * @return vo\ChannelProduct[] $channelProducts
     * @throws exceptions\NotImplemented
     */
    public function sync(array $channelProducts, vo\Channel $channel, array $flagsMap): array
    {
        // Add your code here
        // See examples in:
        // stock2shop\dal\channels\os\Products->sync()
        // stock2shop\dal\channels\memory\Products->sync()
        throw new exceptions\NotImplemented();
    }

    /**
     * See comments in stock2shop\dal\channel\Product::get
     *
     * @param string $token
     * @param int $limit
     * @param vo\Channel $channel
     * @return vo\ChannelProductGet $channelProductsGet
     * @throws exceptions\NotImplemented
     */
    public function get(string $token, int $limit, vo\Channel $channel): vo\ChannelProductGet
    {
        // Add your code here
        // See examples in:
        // stock2shop\dal\channels\os\Products->get()
        // stock2shop\dal\channels\memory\Products->get()
        throw new exceptions\NotImplemented();
    }

    /**
     * See comments in ProductsInterface::getByCode
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[]
     */
    public function getByCode(array $channelProducts, vo\Channel $channel): array
    {
        // Add your code here
        // See examples in:
        // stock2shop\dal\channels\os\Products->getByCode()
        // stock2shop\dal\channels\memory\Products->getByCode()
        throw new exceptions\NotImplemented();
    }

}