<?php
namespace stock2shop\dal\channel;

use stock2shop\vo;

/**
 * Interface Products
 * @package stock2shop\dal\channel
 */
interface Products {

    /**
     * Syncs products to channel.
     *
     * For successful sync the following properties must be set on ChannelProduct
     *
     * - ChannelProduct.synced : microsecond time stamp when it was updated
     * - ChannelProduct.success : true for successful update
     * - ChannelProduct.channel_product_code : channel's unique id for the product
     * - ChannelProduct.variants[].success : true for successful update
     * - ChannelProduct.variants[].channel_variant_code : channel's unique id for the variant
     * - ChannelProduct.images[].success : true for successful update
     * - ChannelProduct.images[].channel_image_code : channel's unique id for the image
     *
     * If a ChannelProduct.success is false, we will not consider variants or images as have being updated.
     * i.e. For us to mark an image as updated, both ChannelProduct.success and
     * ChannelProduct.images[].success must be true
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @param array $flagsMap vo\Flags::createMap
     * @return vo\ChannelProduct[]
     */
    public function sync(array $channelProducts, vo\Channel $channel, array $flagsMap): array;

    /**
     * The following properties must be returned on the ChannelProducts:-
     * - product.channel_product_code
     * - product.variant[].channel_variant_code
     * - product.variant[].sku
     *
     * @param vo\ChannelProduct[] $channelProducts
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[]
     * @throws exceptions\NotImplemented
     */
    public function getByCode(array $channelProducts, vo\Channel $channel): array;

    /**
     *
     * The following properties must be returned on the ChannelProducts:-
     * - product.channel_product_code
     * - product.variant[].channel_variant_code
     * - product.variant[].sku
     *
     * Resulting products must be pageable and ordered by token.
     * Think of the token as a page number. Some channels may not support page numbering
     * by int but rather ordering by date or sorted alphabetically.
     * If the channel does not support paging, throw exceptions\NotImplemented.
     *
     * If $token is blank, the first products in the ordered list must be returned.
     *
     * @param string $token page numbering string, see above
     * @param int $limit max records to return
     * @param vo\Channel $channel
     * @return vo\ChannelProduct[]
     * @throws exceptions\NotImplemented
     */
    public function get(string $token, int $limit, vo\Channel $channel): array;

}

