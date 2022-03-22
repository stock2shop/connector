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
     * a channel, set the following properties on the returned vo\ChannelProductCode:-
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
     * In order to use getByCode() correctly, your channel needs to support cursor based pagination
     * or offset based pagination.
     *
     * ChannelProductGet->channel_products[]->channel_product_code is the unique identifier on the channel
     * for the product. Your pagination should return a unique channel_product_code for each product during
     * the pagination process.
     *
     * Token:
     * Token is our cursor used for the pagination process (we use cursor based pagination).
     * You need to return the token with your results so that when used in the next iteration
     * a next set of products would be returned, and so on.
     *
     * For example, if your channel uses page based pagination. The token value would be the page_number.
     * If you had 22 products in total, the sequence of requests to the get() function may look like this:-
     *
     * 1. get('', 10, $channel) -> token returned = 1 (for page_number 1) 10 products returned (fetches 10 products for page 0)
     * 2. get('1', 10, $channel) -> token returned = 2, 10 products returned (fetches 10 products for page 1)
     * 3. get('2', 10, $channel) -> token returned = 3, 2 products returned (fetches 10 products for page 2)
     *
     * In the last request, the returned products is less than the limit (10), so we stop processing.
     * In an example where you have 10 products in total, the sequence is:-
     *
     * 1. get('', 10, $channel) -> token returned = 1, 10 products returned (fetches all products for page 0)
     * 2. get('1', 10, $channel) -> token returned = 1, 0 products returned (fetches all products for page 1)
     *
     * Since zero products are returned on the last step above, we stop processing.
     * The token returned is not relevant.
     *
     * The same process works if your channel uses cursor based paging.
     * For example, consider your channel allows fetching products where sku is greater than x.
     * Internally, the channels results would need to be sorted by sku ascending.
     *
     * Our paging in this case, may look like this:-
     * 1. get('', 10, $channel) -> token returned = abc123 (the last products sku) 10 products returned
     * 2. get('abc123', 10, $channel) -> token returned = edf456, 10 products returned (fetches 10 products where sku greater than abc123)
     * 3. get('edf456', 10, $channel) -> token returned = xyz789, 2 products returned (fetches 2 products where sku greater than edf456)
     *
     * The following properties must be set on the returned ChannelProducts:-
     * - ChannelProduct->channel_product_code
     * - ChannelProduct->variant[]->channel_variant_code
     * - ChannelProduct->variant[]->sku
     *
     * You can optionally set ChannelProduct->images[]->channel_image_code if the image exists.
     *
     * If $token is blank, the first products in the ordered list must be returned.
     *
     * @param string $token only return results greater than this
     * @param int $limit max records to return
     * @param vo\Channel $channel
     * @return vo\ChannelProductGet
     */
    public function get(string $token, int $limit, vo\Channel $channel): vo\ChannelProductGet;

}

