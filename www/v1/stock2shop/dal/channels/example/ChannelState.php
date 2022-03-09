<?php

namespace stock2shop\dal\channels\example;

use stock2shop\vo;

/**
 * Stores the state for the channel in memory as an example
 * i.e. products, orders ad fulfillments
 *
 * @package stock2shop\dal\example
 */
class ChannelState
{

    /**
     * @var array associative array with key as ExampleProduct->id and value as ExampleProduct
     */
    public static $products = [];

    /**
     * @var array associative array with key as ExampleImage->id and value as ExampleImage
     */
    public static $images = [];

    /**
     * @param ExampleProduct[] $products
     */
    static function update(array $products) {
        foreach ($products as $p) {
            self::$products[$p->id] = $p;
        }
    }

    /**
     * @param ExampleImage[] $images
     */
    static function updateImages(array $images) {
        foreach ($images as $i) {
            self::$images[$i->id] = $i;
        }
    }

    /**
     * @param string[] $ids
     * @return array associative array, key being id and value being ExampleProduct
     */
    static function getProductsByIDs(array $ids): array {
        $exampleProducts = [];
        foreach ($ids as $id) {
            if(isset(self::$products[$id])) {
                $exampleProducts[$id] = self::$products[$id];
            }
        }
        return $exampleProducts;
    }

    /**
     * @param string[] $ids
     * @return array associative array, key being id and value being ExampleImage
     */
    static function getImagesByIDs(array $ids): array {
        $exampleImages = [];
        foreach ($ids as $id) {
            if(isset(self::$images[$id])) {
                $exampleImages[$id] = self::$images[$id];
            }
        }
        return $exampleImages;
    }

    /**
     * @param string[] $ids
     */
    static function deleteProductsByIDs(array $ids) {
        foreach ($ids as $id) {
            unset(self::$products[$id]);
        }
    }

    /**
     * @param string[] $ids
     */

    static function deleteImages(array $ids) {
        foreach ($ids as $id) {
            unset(self::$images[$id]);
        }
    }

}