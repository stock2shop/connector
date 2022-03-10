<?php

namespace stock2shop\dal\channels\memory;

use stock2shop\vo;

/**
 * Stores the state for the channel in memory as an example
 * i.e. products, orders ad fulfillments
 *
 * @package stock2shop\dal\memory
 */
class ChannelState
{

    /**
     * @var array associative array with key as MemoryProduct->id and value as MemoryProduct
     */
    public static $products = [];

    /**
     * @var array associative array with key as MemoryImage->id and value as MemoryImage
     */
    public static $images = [];

    /**
     * @param MemoryProduct[] $products
     */
    public static function update(array $products) {
        foreach ($products as $p) {
            self::$products[$p->id] = $p;
        }
    }

    /**
     * @param MemoryImage[] $images
     */
    public static function updateImages(array $images) {
        foreach ($images as $i) {
            self::$images[$i->id] = $i;
        }
    }

    /**
     * @param string[] $ids
     * @return array associative array, key being id and value being MemoryProduct
     */
    public static function getProductsByIDs(array $ids): array {
        $exampleProducts = [];
        foreach ($ids as $id) {
            if(isset(self::$products[$id])) {
                $exampleProducts[$id] = self::$products[$id];
            }
        }
        return $exampleProducts;
    }

    /**
     * Get Images By Ids
     *
     * Returns matching image objects by ID.
     *
     * @param string[] $ids
     * @return array associative array, key being id and value being ExampleImage
     */
    public static function getImagesByIDs(array $ids): array {
        $exampleImages = [];
        foreach ($ids as $id) {
            if(isset(self::$images[$id])) {
                $exampleImages[$id] = self::$images[$id];
            }
        }
        return $exampleImages;
    }

    /**
     * Delete Products By Ids
     *
     * Removes matching product objects by ID.
     *
     * @param string[] $ids
     */
    public static function deleteProductsByIDs(array $ids) {
        foreach ($ids as $id) {
            unset(self::$products[$id]);
        }
    }

    /**
     * Delete Images
     *
     * Removes matching image objects by ID.
     *
     * @param string[] $ids
     */
    public static function deleteImages(array $ids) {
        foreach ($ids as $id) {
            unset(self::$images[$id]);
        }
    }

}