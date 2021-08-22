<?php
namespace stock2shop\dal\channel;

use stock2shop\vo\ChannelOrder;
use stock2shop\vo\MetaItem;
use stock2shop\vo\ChannelFulfillmentsSync;
use stock2shop\vo\ChannelProductsSync;
use stock2shop\vo\ChannelProductGet;

interface Connector {

    /**
     * Syncs products from S2S to channel
     *
     * @param ChannelProductsSync $channelProductsSync
     * @return ChannelProductsSync
     */
    public function syncProducts(ChannelProductsSync $channelProductsSync): ChannelProductsSync;

    /**
     * The following properties must be set:-
     * - product.channel_product_code
     * - product.variant[].channel_variant_code
     * - product.variant[].sku
     *
     * @param ChannelProductsSync $channelProductsSync
     * @return ChannelProductsSync
     */
    public function getProductsByCode(ChannelProductsSync $channelProductsSync): ChannelProductsSync;

    /**
     *
     * The following properties must be set:-
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
     * The following properties must be set:-
     * - channel_order_code
     *
     * @param string $token
     * @param int $limit
     * @param MetaItem[] $meta
     * @return ChannelOrder[]
     */
    public function getOrders(string $token, int $limit, array $meta): array;

    /**
     *
     * The following properties must be set:-
     * - channel_order_code
     *
     * @param ChannelOrder[] $orders
     * @param MetaItem[] $meta
     * @return ChannelOrder[]
     */
    public function getOrdersByCode(array $orders, array $meta): array;

    /**
     *
     * Transform should convert the order "webhook" sent by the
     * channel into a "ChannelOrder" object.
     *
     * @param mixed $webhookOrder
     * @param MetaItem[] $meta
     * @return ChannelOrder
     */
    public function transformOrder($webhookOrder, array $meta): ChannelOrder;

    /**
     * Syncs fulfillments from S2S to channel
     *
     * @param ChannelFulfillmentsSync $ChannelFulfillmentsSync
     * @return ChannelFulfillmentsSync
     */
    public function syncFulfillments(ChannelFulfillmentsSync $ChannelFulfillmentsSync): ChannelFulfillmentsSync;

    /**
     *
     * The following properties must be set:-
     * - channel_fulfillment_code
     *
     * @param ChannelFulfillmentsSync $ChannelFulfillmentsSync
     * @return ChannelFulfillments[]
     */
    public function getFulfillmentsByOrderCode(ChannelFulfillmentsSync $ChannelFulfillmentsSync): array;
}

