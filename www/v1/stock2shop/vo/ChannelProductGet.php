<?php

namespace stock2shop\vo;

/**
 * Channel Product Get
 *
 * This Value Object represents a ChannelProduct with a token which
 * is used in the get() method to identify the position of a product
 * in a channel.
 *
 * Use this VO in the get() class method of your implementation of
 * the stock2shop\dal\channel\Products interface when you code your
 * connector integration to add a filter token to each ChannelProduct
 * item.
 *
 * @package stock2shop\vo
 */
class ChannelProductGet extends ChannelProduct
{
    /** @var string $token */
    public $token;

    /**
     * Creates the data object to spec.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->token = self::stringFrom($data, 'token');
    }

}