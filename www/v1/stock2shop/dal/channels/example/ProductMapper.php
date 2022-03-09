<?php

namespace stock2shop\dal\channels\example;

use stock2shop\vo;
use stock2shop\helpers;

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
        // Render with Mustache.
        if ($template) {

            // Build product data array.
            $productData = [];

            // Get product meta.
            unset($cp->variants, $cp->images, $cp->meta, $cp->options);

            // Add product and variant values.
            $productData['ChannelProduct'] = (array)$cp;
            $productData['ChannelVariant'] = (array)$cv;

            // Create the product from template.
            $epData = $this->renderTemplate($template, $productData);
            $ep = new ExampleProduct($epData);
            $this->product = $ep;

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