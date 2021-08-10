<?php
namespace stock2shop\dal\channel;

use stock2shop\vo\ChannelProduct;
use stock2shop\vo\SyncChannelProducts;

interface Connector {

    /**
     * Syncs products from S2S to channel
     *
     * @param SyncChannelProducts $syncChannelProducts
     * @return SyncChannelProducts
     */
    public function syncProducts(SyncChannelProducts $syncChannelProducts): SyncChannelProducts;

    /**
     * This must return ChannelProduct with:-
     * - channel_product_code
     * and corresponding ChannelVariant's with:-
     * - channel_variant_code
     * - sku
     *
     * @param SyncChannelProducts $syncChannelProducts
     * @return SyncChannelProducts
     */
    public function getProductsByCode(SyncChannelProducts $SyncChannelProducts): SyncChannelProducts;

    /**
     *
     * This must return ChannelProduct with:-
     * - channel_product_code
     * and corresponding ChannelVariant's with:-
     * - channel_variant_code
     * - sku
     *
     * @param int $page
     * @param int $limit
     * @return array|ChannelProduct[]
     */
    public function getProducts(int $page, int $limit): array;

    /**
     *
     * This must return ChannelProduct with:-
     * - channel_product_code
     * and corresponding ChannelVariant's with:-
     * - channel_variant_code
     * - sku
     *
     * @param int $page
     * @param int $limit
     * @return array|ChannelProduct[]
     */
    public function getOrders(int $page, int $limit): array;

    /**
     *
     * This must return ChannelProduct with:-
     * - channel_product_code
     * and corresponding ChannelVariant's with:-
     * - channel_variant_code
     * - sku
     *
     * TODO define input VO
     *
     * @return array|ChannelProduct[]
     */
    public function getOrdersByCode(): array;

    /**
     * TODO should return systemOrder VO
     *
     * @param \stdClass $channelOrder
     * @return mixed
     */
    public function transformOrder(\stdClass $channelOrder);
}

