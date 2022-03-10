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

    /** @var MemoryProduct[] Associative array with key as MemoryProduct->id and value as MemoryProduct. */
    public static $products = [];

    /** @var MemoryImage[] Associative array with key as the MemoryImage->id and value as MemoryImage. */
    public static $images = [];

    /**
     * Create
     *
     * This method creates a product on the channel.
     *
     * @param MemoryProduct $product
     * @return string $id
     */
    public static function create(MemoryProduct $product): string {

        // Get last insert ID.
        $lastInsertId = count(self::$products);

        // Increment and cast to string.
        $id = (string)$lastInsertId++;
        $product->id = $id;

        // Create product on channel.
        self::$products[$id] = $product;
        return $id;

    }

    /**
     * @param MemoryProduct[] $products
     */
    public static function update(array $products) {
        foreach ($products as $p) {
            // Check whether the product exists
            if(array_key_exists($p->id, self::$products)) {
                self::$products[$p->id] = $p;
            }
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
     * List Products
     *
     * This method returns paginated products.
     *
     * @param string $offset The channel_product_code value to start from.
     * @param int $limit The number of products to return from the channel.
     * @return MemoryProduct[] $list
     */
    public static function getProductsList(string $offset, int $limit): array {

        /** @var MemoryProduct[] $list */
        $list = [];

        // Get start index using a combination of array_keys() and array_search().
        // The needle will be the channel_product_code.
        $startIndex = array_search($offset, array_keys(self::$products)) ?? 0;

        // Iterate over products in state.
        if($offset !== "") {
            $list = array_values(array_merge(array_slice(self::$products, $startIndex, $limit, true)));
        } else {
            $count = 0;
            foreach(self::$products as $id => $productFromStart) {
                if($count === $limit) {
                    break;
                }
                $list[$id] = $productFromStart;
                $count++;
            }
        }

        return $list;

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