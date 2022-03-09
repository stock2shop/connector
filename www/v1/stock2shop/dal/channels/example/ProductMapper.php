<?php

namespace stock2shop\dal\channels\example;

use stock2shop\vo;

/**
 * Example showing how to build an ExampleProduct from a S2S ChannelProduct.
 * We also show how you can use Channel Meta (configuration) to make the
 * resulting ExampleProduct configurable.
 *
 * @package stock2shop\dal\example
 */
class ProductMapper
{

    /**
     * @var ExampleProduct
     */
    private $product;

    /**
     * @param vo\ChannelProduct $cp
     * @param vo\ChannelVariant $cv
     * @param string|bool $template
     */
    public function __construct(vo\ChannelProduct $cp, vo\ChannelVariant $cv, $template)
    {
        // render using mustache
        if ($template) {
            // todo
        } else {
            $ep                   = new ExampleProduct();
            $ep->id               = $cv->sku;
            $ep->product_group_id = $cp->source_product_code;
            $ep->name             = $cp->title;
            $ep->price            = $cv->price;
            $ep->quantity         = $cv->qty;
            $this->product        = $ep;
        }
    }

    /**
     * @return ExampleProduct
     */
    public function get(): ExampleProduct
    {
        return $this->product;
    }

}