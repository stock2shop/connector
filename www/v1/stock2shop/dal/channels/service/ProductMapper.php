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
     *     'name': '{{ChannelProduct.title}}',
     *     'brand': '{{ChannelProduct.title}}',
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
        $this->defaultChannelProductMap = helpers\Meta::get($channel->meta, "default_product_map");

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

        // In this method we are going to extract the variants
        // one by one and instantiate a `ServiceProduct` object
        // for each variant. Hence, a `vo\ChannelProduct` object
        // may yield more than one `ServiceProduct` object,
        // although only the fields belonging to the variants[]
        // property of the product will be unique.

        $flattenedChannelProducts = [];

        // First we need to extract the property that has the variants.
        // After that the variants and the product properties will be
        // merged into arrays which we'll use the template to map to
        // `ServiceProduct` objects.
        $channelVariants = array_column($channelProduct, "variants");
        $cProduct = (array)$channelProduct;
        $this->prefixArrayKeys("ChannelProduct", $cProduct);

        foreach($channelVariants as $cVariant) {

            // Prefix variant keys.
            $this->prefixArrayKeys("ChannelVariant", $cVariant);
            $sp = array_merge($cVariant, $cProduct);

            // New service product.
            $m = new \Mustache_Engine();
            $serviceProducts[] = $m->render($this->defaultChannelProductMap, new ServiceProduct());

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
     * @param array $flattenedChannelProduct
     * @return array $serviceProductData
     */
//    public function renderTemplate(string $rawTemplate, array $flattenedChannelProduct): array {
//
//
//
//    }

    /**
     * Prefix Array Keys
     *
     * Adds a prefix to the keys of the array and updates the original
     * array without copying it by reference. Dot notation is added in
     * between so that it may be used with the Mustache template library.
     *
     * @param string $prefix
     * @param array $array
     * @return void
     */
    private function prefixArrayKeys(string $prefix, array $array) {
        $format = $prefix . '.%s';
        array_flip(array_map(function ($key) use($format) {
            return sprintf($format, $key);
        }, array_flip($array)));
    }

    /**
     * Flatten
     *
     * This function uses the built-in 'RecursiveIteratorIterator'
     * and 'RecursiveArrayIterator' classes to flatten a Value
     * Object array.
     *
     * @param array $arr
     * @return array
     */
    public static function flatten(array $arr): array
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr));
        $keys = [];
        foreach ($iterator as $key => $value) {
            for ($i = $iterator->getDepth() - 1; $i >= 0; $i--) {
                $parentKey = $iterator->getSubIterator($i)->key();
                if (!is_numeric($parentKey)) {
                    $key = $parentKey . '.' . $key;
                }
            }
            $key .= '.' . $value;
            $keys[] = $key;
        }
        sort($keys);
        return $keys;
    }

}