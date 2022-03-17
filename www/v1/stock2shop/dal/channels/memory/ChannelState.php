<?php

namespace stock2shop\dal\channels\memory;

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
     * Create Image
     *
     * This method creates an image on the channel.
     *
     * @param MemoryImage $image
     * @return string $id
     */
    public static function createImage(MemoryImage $image)
    {
        // Get id.
        $image->id = self::generateID();

        // Create image on channel.
        self::$stateImages[$image->id] = $image;
        return $image->id;
    }

    /**
     * Generate ID
     *
     * Creates a new ID for the product or image.
     *
     * @return string $id The unique ID string.
     * @throws \Exception
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
     *
     * @param MemoryProduct[] $items
     * @return MemoryProduct[] $updated
     */
    public static function update(array $items): array
    {
        $updated = [];
        foreach ($items as $item) {
            if (is_null($item->id)) {
                $item->id = self::generateID();
            }
            if (is_null($item->product_group_id)) {
                $item->product_group_id = self::generateID();
            }
            self::$stateProducts[$item->id] = $item;
            $updated[] = $item;
        }
        return $updated;
    }

    /**
     * Update Images
     *
     * Updates or inserts an image if not found
     *
     * @param MemoryImage[] $items An array of MemoryImage items to update.
     * @return MemoryImage[]|[] An array of IDs from MemoryImage items which have been updated.
     * @throws \Exception
     */
    public static function updateImages(array $items)
    {
        $updated = [];
        foreach ($items as $item) {
            if (is_null($item->id)) {
                $item->id = self::generateID();
            }
            self::$stateImages[$item->id] = $item;
            $updated[] = $item;
        }
        return $updated;
    }

    /**
     * Returns all grouped products (grouped by product_group_id)
     *
     * @return MemoryProduct[]
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
     * Get all products
     *
     * @return MemoryProduct[]
     */
    public static function getProducts()
    {
        return self::$stateProducts;
    }

    /**
     * Get Products By IDs
     *
     * Returns an array of MemoryProduct items which match the
     * IDs provided in the parameter.
     *
     * @param string[] $ids
     * @return MemoryProduct[] associative array, key being id and value being MemoryProduct
     */
    public static function getProductsByIDs(array $ids): array
    {
        $exampleProducts = [];
        foreach ($ids as $id) {
            if (isset(self::$stateProducts[$id])) {
                $exampleProducts[$id] = self::$stateProducts[$id];
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
     * @return MemoryImage[]|[]
     */
    public static function getImages()
    {
        $memoryImages = [];
        foreach (self::$stateImages as $miKey => $miValue) {
            $memoryImages[$miKey] = $miValue;
        }
        return $memoryImages;
    }

    /**
     * Delete Products By Ids
     *
     * Removes matching product objects by ID.
     *
     * @param string[] $ids
     */
    public static function deleteProductsByIDs(array $ids)
    {
        foreach ($ids as $id) {
            if (isset(self::$stateProducts[$id])) {
                unset(self::$stateProducts[$id]);
            }
        }
    }

    /**
     * Delete Products By Group IDs
     *
     * Removes matching product objects by ID.
     *
     * @param string[] $ids
     */
    public static function deleteProductsByGroupIDs(array $ids)
    {
        foreach (self::$stateProducts as $sp) {
            if (in_array($sp->product_group_id, $ids)) {
                self::deleteProductsByIDs([$sp->id]);
            }
        }
    }

    /**
     * Delete Images By Group IDs
     *
     * Removes matching image objects by ID.
     *
     * @param string[] $ids
     */
    public static function deleteImagesByGroupIDs(array $ids)
    {
        // Get image IDs.
        $imagesByGroupIDs = self::getImagesByGroupIDs($ids);

        // Build array of IDs to remove.
        $imageIdsToDelete = array_column($imagesByGroupIDs, 'id');

        // Remove the items from the channel.
        self::deleteImages($imageIdsToDelete);
    }

    /**
     * Get Products By Group ID
     *
     * Fetches products with matching product_group_id
     *
     * @param array $group_product_ids
     * @return MemoryProduct[]
     */
    public static function getProductsByGroupIDs(array $group_product_ids)
    {
        $products = [];
        foreach (self::$stateProducts as $id => $product) {
            if (in_array($product->product_group_id, $group_product_ids)) {
                $products[$id] = $product;
            }
        }
        ksort($products);
        return $products;
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
     * Get Images By Product IDs
     *
     * Returns all images linked to the product IDs
     * in the array.
     *
     * @param array $productIDs
     * @return array
     */
    public static function getImagesByProductIDs(array $productIDs)
    {
        $productImages = [];
        foreach (self::$stateImages as $key => $image) {
            if (in_array($image->product_id, $productIDs)) {
                $productImages[$key] = $image;
            }
        }
        return $productImages;
    }

    /**
     * Delete Images By Product IDs
     *
     * This method deletes multiple images which match
     * the product IDs passed in the parameter.
     *
     * @param array $ids Array of product IDs.
     * @return void
     */
    public static function deleteImagesByProductIDs(array $ids)
    {
        foreach (self::$stateImages as $stateImageKey => $stateImageValue) {
            if (in_array($stateImageValue->product_id, $ids)) {
                unset(self::$stateImages[$stateImageKey]);
            }
        }
    }

    /**
     * Delete Images
     *
     * Removes matching image objects by ID.
     *
     * @param string[] $ids
     */
    public static function deleteImages(array $ids)
    {
        foreach ($ids as $id) {
            if (isset(self::$stateImages[$id])) {
                unset(self::$stateImages[$id]);
            }
        }
    }

    /**
     * Delete Image By Url
     *
     * Delete an image by the src property.
     * This maps to a "vo\ChannelImage" object's
     * "src" property.
     *
     * @param string[] $url
     * @return string[] $memoryImageId
     */
    public static function deleteImageByUrl(array $urls): array
    {

        // Return image IDs.
        $deletedImageIDs = [];

        // Iterate over the images in the channel's state and remove if matching.
        foreach (self::$stateImages as $stateImageKey => $stateImageValue) {
            if (in_array($stateImageValue->url, $urls)) {
                unset(self::$stateImages[$stateImageKey]);
                $deletedImageIDs[] = $stateImageKey;
            }
        }
        return $deletedImageIDs;
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