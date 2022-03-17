<?php

namespace stock2shop\dal\channels\memory;

use Exception;

/**
 * Stores the state for the channel in memory as an example
 * i.e. products, orders ad fulfillments
 *
 * @package stock2shop\dal\memory
 */
class ChannelState
{

    /** @var MemoryProduct[] Associative array with key as MemoryProduct->id and value as MemoryProduct. */
    private static $stateProducts = [];

    /** @var MemoryImage[] Associative array with key as the MemoryImage->id and value as MemoryImage. */
    private static $stateImages = [];

    /**
     * Generate ID
     *
     * Creates a new ID for the product or image.
     *
     * @return string $id The unique ID string.
     * @throws Exception
     */
    public static function generateID(): string
    {
        $length = 50;
        $pieces = [];
        $keyspace = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces [] = $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    /**
     * Update
     *
     * This method updates a batch of products if they
     * exist otherwise generate a new one and create unique id.
     * A unique ID is also generated for the "product_group_id"
     * if it does not exist.
     *
     * @param MemoryProduct[] $items
     */
    public static function updateProducts(array $items)
    {
        foreach ($items as $item) {
            if (!$item->id) {
                $item->id = self::generateID();
            }
            if (!$item->product_group_id) {
                $item->product_group_id = self::generateID();
            }
            self::$stateProducts[$item->id] = $item;
        }
    }

    /**
     * Update Images
     *
     * Updates or inserts an image if not found.
     *
     * @param MemoryImage[] $memory_images
     * @throws Exception
     */
    public static function updateImages(array $memory_images)
    {
        foreach ($memory_images as $item) {
            if (!$item->id) {
                $item->id = self::generateID();
            }
            self::$stateImages[$item->id] = $item;
        }
    }

    /**
     * Get Product Groups
     *
     * Returns a map of MemoryProduct[]
     * keyed on "product_group_id".
     *
     * @return array
     */
    public static function getProductGroups(): array
    {
        $groups = [];
        foreach (self::$stateProducts as $product) {
            if (!isset($groups[$product->product_group_id])) {
                $groups[$product->product_group_id] = [];
            }
            $groups[$product->product_group_id][] = $product;
        }
        ksort($groups);
        return $groups;
    }

    /**
     * Get Products
     *
     * Accessor method for $stateProducts property.
     *
     * @return array
     */
    public static function getProducts(): array
    {
        return self::$stateProducts;
    }

    /**
     * Get Images By Ids
     *
     * Returns matching image objects by ID.
     *
     * @param string[] $ids
     * @return MemoryImage[] associative array, key being id and value being ExampleImage
     */
    public static function getImagesByIDs(array $ids): array
    {
        $exampleImages = [];
        foreach ($ids as $id) {
            if (isset(self::$stateImages[$id])) {
                $exampleImages[$id] = self::$stateImages[$id];
            }
        }
        return $exampleImages;
    }

    /**
     * Get Images
     *
     * Returns all images on the channel.
     *
     * @return MemoryImage[]
     */
    public static function getImages(): array
    {
        return self::$stateImages;
    }

    /**
     * Delete Products By Ids
     *
     * Removes matching product objects by ID.
     *
     * @param string[] $memory_product_ids
     */
    public static function deleteProducts(array $memory_product_ids)
    {
        foreach ($memory_product_ids as $id) {
            if (isset(self::$stateProducts[$id])) {
                unset(self::$stateProducts[$id]);
            }
        }
    }

    /**
     * Delete Images
     *
     * Removes matching image objects by ID.
     *
     * @param string[] $memory_image_ids
     */
    public static function deleteImages(array $memory_image_ids)
    {
        foreach ($memory_image_ids as $id) {
            if (isset(self::$stateImages[$id])) {
                unset(self::$stateImages[$id]);
            }
        }
    }

    /**
     * Get Images By Group IDs
     *
     * Return images from the channel which match the group IDs.
     *
     * @param array $group_ids
     * @return MemoryImage[] $images
     */
    public static function getImagesByGroupIDs(array $group_ids): array
    {
        $images = [];
        foreach (self::$stateImages as $stateImage) {
            if (in_array($stateImage->product_group_id, $group_ids)) {
                $images[] = $stateImage;
            }
        }
        return $images;
    }

    /**
     * Delete Products By Group IDs
     *
     * Removes matching MemoryProduct objects by ID.
     *
     * @param string[] $ids
     */
    public static function deleteProductsByGroupIDs(array $ids)
    {
        foreach (self::$stateProducts as $sp) {
            if (in_array($sp->product_group_id, $ids)) {
                self::deleteProducts([$sp->id]);
            }
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
    public static function clean()
    {
        self::$stateProducts = [];
        self::$stateImages = [];
    }

}