<?php
namespace stock2shop\dal\channel;

use stock2shop\vo;

/**
 * Interface defining methods for interacting (syncing / fetching)
 * product data for a channel instance.
 *
 * @package stock2shop\dal\channel
 */
interface Products {

    /**
     * Syncs products to channel.
     *
     * This means that ChannelProducts must be persisted (or removed depending on the instruction)
     * to the channel. Regardless of the current state of the channel, after this sync function
     * is called, the channels state should represent the ChannelProducts given.
     *
     * This method should:-
     *
     * - Allow for products to be configured on the channel, depending on channel settings (Channel->meta)
     * - Make request to the channel to update product state on the channel.
     * - Update ChannelProducts, marking which products have updated successfully.
     *
     * A ChannelProduct consists of a product with variants and images.
     * The product, variants and images are treated separately and need to be
     * marked as successfully synced independently.
     *
     * Each of the above data classes have the following properties:-
     *
     * - success (did this product update to the channel)
     * - delete (if true, remove it from the channel)
     *
     * How to define a successful sync for a ChannelProduct?
     *
     * For the product the following properties must be set on ChannelProduct:
     *
     * - ChannelProduct->success = true
     * - ChannelProduct->channel_product_code = "channel's unique id for the product"
     *
     * For the variants the following properties must be set on ChannelProduct:
     *
     * - ChannelProduct->success = true
     * - ChannelProduct->channel_product_code = "channel's unique id for the product"
     * - ChannelProduct->variants[]->success = true
     * - ChannelProduct->variants[]->channel_variant_code = "channel's unique id for the variant"
     *
     * For the images the following properties must be set on ChannelProduct:
     *
     * - ChannelProduct->success = true
     * - ChannelProduct->channel_product_code = "channel's unique id for the product"
     * - ChannelProduct->images[]->success = true
     * - ChannelProduct->images[]->channel_image_code = "channel's unique id for the image"
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @param array $flagsMap vo\Flags::createMap
     * @return vo\ChannelProduct[]
     */
    public function sync(array $channelProducts, vo\Channel $channel, array $flagsMap): array;

    /**
     * Verify that products exist on a channel, given their
     * ChannelProduct->channel_product_code (channels unique identifier for the product).
     *
     * To confirm that a product, its variants and images are synced with
     * a channel, set the following properties:-
     *
     * - ChannelProduct->success = true
     * - ChannelProduct->variants[]->success = true
     * - ChannelProduct->images[]->success = true
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[]
     */
    public function getByCode(array $channelProducts, vo\Channel $channel): array;

    /**
     * Used so we can page through products on a channel and return their unique identifiers.
     * In other words, a way for us to see what exists on a channel.
     *
     * Paging:
     * The results must be sorted by ChannelProduct->channel_product_code ascending.
     * ChannelProduct->channel_product_code is the unique identifier on the channel for the product.
     * We use channel_product_code as a pointer and only products with a
     * greater channel_product_code should be returned.
     *
     * For example, if your channel uses an integer for channel_product_code, and it had 22 products in sequence,
     * then paging would look like this.
     * 1. get('', 10, $channel) -> last product returned has channel_product_code=10
     * 2. get('10', 10, $channel) -> last product returned has channel_product_code=20
     * 3. get('20', 10, $channel) -> last product returned has channel_product_code=22
     * 4. get('22', 10, $channel) -> no more products returned
     *
     * The following properties must be set on the returned ChannelProducts:-
     * - ChannelProduct->channel_product_code
     * - ChannelProduct->variant[]->channel_variant_code
     * - ChannelProduct->variant[]->sku
     *
     * You can optionally set ChannelProduct->images[]->channel_image_code if the image exists.
     *
     * If $channel_product_code is blank, the first products in the ordered list must be returned.
     *
     * @param string $token only return results greater than this
     * @param int $limit max records to return
     * @param vo\Channel $channel
     * @return vo\ChannelProductGet
     */
    public function get(string $token, int $limit, vo\Channel $channel): vo\ChannelProductGet;

}

