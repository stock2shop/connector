<?php
namespace stock2shop\dal\channel;

use stock2shop\vo\ChannelProduct;
use stock2shop\vo\MetaItem;
use stock2shop\vo\Order;
use stock2shop\vo\SyncChannelProducts;
use stock2shop\vo\GetChannelProduct;

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
     * This must return ChannelProductGet with the following properties set:-
     * - product.channel_product_code
     * - product.variant[].channel_variant_code
     * - product.variant[].sku
     *
     * Resulting products must be pageable and ordered by token.
     * Think of the token as a page number. Some channels may not support page numbering
     * by int but rather ordering by date or sorted alphabetically.
     * If the channel does not support paging, throw an exception.
     *
     * If $token is blank, the first products in the ordered list must be returned.
     *
     * @param string $token page numbering string, see above
     * @param int $limit max records to return
     * @param MetaItem[] $meta
     * @return ChannelProductGet[]
     */
    public function getProducts(string $token, int $limit, array $meta): array;

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
     *
     * Transform should convert the order "webhook" sent by the
     * channel into a "Order" object.
     *
     * @param \stdClass $webhookOrder
     * @param MetaItem[] $meta
     * @return Order
     */
    public function transformOrder(\stdClass $webhookOrder, array $meta): Order;
}

