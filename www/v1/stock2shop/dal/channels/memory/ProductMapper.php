<?php

namespace stock2shop\dal\channels\memory;

use stock2shop\vo;
use stock2shop\helpers;

/**
 * Example showing how to build an MemoryProduct from a S2S ChannelProduct.
 * We also show how you can use Channel Meta (configuration) to make the
 * resulting MemoryProduct configurable.
 *
 * @package stock2shop\dal\memory
 */
class ProductMapper
{

    /**
     * @var MemoryProduct
     */
    private $product;

    /**
     * Default Constructor
     *
     * This mapper class transforms a `vo\ChannelProduct` and `vo\ChannelVariant` object
     * into a `MemoryProduct` object. An optional string template variable may be passed
     * which contains the JSON mapping for the fields.
     *
     * @param vo\ChannelProduct $cp
     * @param vo\ChannelVariant $cv
     * @param string|null $template
     */
    public function __construct(vo\ChannelProduct $cp, vo\ChannelVariant $cv, $template=null)
    {
        // Render with Mustache.
        if ($template) {

            // Build product data array.
            $productData = [];

            // Get product meta.

            // Add product and variant values.
            $productData['ChannelProduct'] = (array)$cp;
            $productData['ChannelVariant'] = (array)$cv;

            // Unset the array properties.
            unset(
                $productData['ChannelProduct']['variants'],
                $productData['ChannelProduct']['images'],
                $productData['ChannelProduct']['meta'],
                $productData['ChannelProduct']['options']
            );

            // Create the product from template.
            $epData = $this->renderTemplate($template, $productData);
            $ep = new MemoryProduct($epData);
            $this->product = $ep;

        } else {
            $ep                   = new MemoryProduct();
            $ep->id               = $cv->sku;
            $ep->product_group_id = $cp->channel_product_code;
            $ep->name             = $cp->title;
            $ep->price            = $cv->price;
            $ep->quantity         = $cv->qty;
            $this->product        = $ep;
        }
    }

    /**
     * @return MemoryProduct
     */
    public function get(): MemoryProduct
    {
        return $this->product;
    }

    /**
     * Render Template
     *
     * This method uses the template configured in the default constructor
     * of this class and the product data provided to populate an associative
     * array of data matching the channel's product schema.
     *
     * @param string $template
     * @param array $data
     * @return array
     */
    public function renderTemplate(string $template, array $data): array {
        $mustache = new \Mustache_Engine();
        return json_decode($mustache->render($template, $data), true);
    }

}