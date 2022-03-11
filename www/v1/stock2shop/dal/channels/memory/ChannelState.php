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
    private static $products = [];

    /** @var MemoryImage[] Associative array with key as the MemoryImage->id and value as MemoryImage. */
    private static $images = [];

    /**
     * Create Image
     *
     * This method creates an image on the channel.
     *
     * @param MemoryImage $image
     * @return string $id
     */
    public static function createImage(MemoryImage $image) {

        $productImages = [];

        // We need to add the ids for the
        // images that the product has on
        // the channel first.

        foreach(self::$images as $i) {
            if($image->product_id === $i->product_id) {
                $productImages[] = $i;
            }
        }

        $lastId = count($productImages) + 1;
        $url = "http://stock2shoptestchannel/$image->product_id/$lastId";
        $image->id = $url;
        self::$images[$url] = $image;

    }

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
//        $last = end(self::$products);

        if(count(self::$products) === 0) {

            // This is the first product.
            $product->id = '0';

        } else {

            // Increment and cast to string.
//            $product->id = (string) ((int)$last->id++);
            $end = self::$products[count(self::$products) - 1];
            $id = (int)$end->id;
            $id++;
            $product->id = (string) $id;

        }

        // Create product on channel.
        self::$products[$product->id] = $product;
        return $product->id;

    }

    /**
     * Update
     *
     * This method updates a batch of products if they
     * exist in the $products class property.
     *
     * @param MemoryProduct[] $products
     */
    public static function update(array $products): array
    {
        // Array of updated IDs.
        $ids = [];

        // Check whether the product exists.
        foreach($products as $i) {
            if(array_key_exists($i->id, self::$products)) {
                self::$products[$i->id] = $i;
                $ids[] = $i->id;
            }
        }
        return $ids;
    }

    /**
     * Update Images
     *
     * This method updates images if found in the
     * $images class property.
     *
     * @param MemoryImage[] $images
     */
    public static function updateImages(array $images): array
    {
        // Array of updated IDs.
        $ids = [];

        // Check whether the image exists.
        foreach($images as $i) {
            if(array_key_exists($i->id, self::$images)) {
                self::$images[$i->id] = $i;
                $ids[] = $i->id;
            }
        }
        return $ids;
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
        $products = [];

        // Slice array
        // - preserve indices
        $list = array_slice(self::$products, $offset, $limit, true);

        foreach($list as $item) {
            $products[] = $item;
        }

        return $products;

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
            self::$products[$id] = null;
//            unset(self::$products[$id]);
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
            self::$images[$id] = null;
        }
    }

    /**
     * Clean
     *
     * Removes all products and images from the
     * channel's state.
     *
     * @return void
     */
    public static function clean() {
        self::$products = null;
        self::$images = null;
    }

}