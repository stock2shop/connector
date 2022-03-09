<?php

namespace stock2shop\dal\channels\service;

use stock2shop\vo;
use stock2shop\helpers;

use Mustache_Engine as TemplateEngine;

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
     * We expect the channel product map to have the following structure:
     *
     * {
     *     'id': '{{ChannelVariant.channel_variant_code}}',
     *     'name': '{{meta.title}}',
     *     'brand': '{{meta.brand}}',
     *     'quantity': '{{ChannelProduct.qty}}',
     *     'group': '{{ChannelProduct.channel_product_code}}',
     *     'images': '{{ChannelProduct.images}}',
     *     'price': '{{ChannelVariant.price}}'
     * }
     *
     * @var string
     */
    private $defaultChannelProductMap = "";

    /**
     * Default Constructor
     *
     * The default constructor for the mapper class accepts a `vo\Channel` object
     * as it's only parameter. The 'channel' object is used to configure the
     * mapper class using 'channel meta'.
     *
     * @param vo\Channel $channel
     */
    public function __construct(vo\Channel $channel) {

        // Get the meta map from the channel meta.
        $this->defaultChannelProductMap = helpers\Meta::get($channel->meta, "default_channel_product_map");

    }

    /**
     * Map
     *
     * This method defines the workflow for the mapping/transformation
     * of a `vo\ChannelProduct` object to `ServiceProduct`
     * object.
     *
     * @param vo\ChannelProduct $channelProduct
     * @return ServiceProduct[] $serviceProducts
     */
    public function map(vo\ChannelProduct $channelProduct) {

        /** @var ServiceProduct[] $serviceProducts */
        $serviceProducts = [];

        // First we need to extract the property that has the variants.
        // After that the variants and the product properties will be
        // merged into arrays which we'll use the template to map to
        // `ServiceProduct` objects.
        $productVariants = $channelProduct->variants;
        $productMeta = helpers\Meta::map($channelProduct->meta);
        $productImages = $channelProduct->images;

        // Build the product.
        $productData = [];
        $productData['Meta'] = $productMeta;

        // Unset array/object properties.
        unset($channelProduct->variants, $channelProduct->images, $channelProduct->meta, $channelProduct->options);
        $productData['ChannelProduct'] = (array)$channelProduct;

        // Iterate over variants.
        foreach($productVariants as $cVariant) {

            $productData['ChannelVariant'] = (array)$cVariant;
            $sp = $this->renderTemplate($this->defaultChannelProductMap, $productData);

            // New service product.
            $serviceProduct = new ServiceProduct();
            foreach($sp as $k => $v) {
                $serviceProduct->$k = $v;
            }

            // Iterate over the ChannelProduct images.
            foreach($productImages as $cpImage) {
                $serviceProduct->images[] = $cpImage->src;
            }

            /**
             * Here we are expecting to have a complete `ServiceProduct`
             * object which matches the structure of a product on the
             * channel:
             *
             *  serviceProduct
             *
             *   id       =   ChannelVariant->channel_variant_code
             *   name     =   Meta[name='title']->value
             *   brand    =   Meta[name='brand']->value
             *   price    =   ChannelVariant->price
             *   quantity =   ChannelVariant->qty
             *   quantity =   ChannelVariant->qty
             *   group    =   ChannelProduct->channel_product_code
             *   images   =   ChannelProduct->images
             */

            $serviceProducts[] = $serviceProduct;

        }

        return $serviceProducts;

    }

    /**
     * Render Template
     *
     * This method uses the template configured in the default constructor
     * of this class and the product data provided to populate an associative
     * array of `ServiceProduct` data.
     *
     * @param string $rawTemplate
     * @param array $data
     * @return array
     */
    public function renderTemplate(string $rawTemplate, array $data): array {
        $mustache = new \Mustache_Engine();
        return json_decode($mustache->render($rawTemplate, $data), true);
    }

}