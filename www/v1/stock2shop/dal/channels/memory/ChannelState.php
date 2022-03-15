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
     * Create
     *
     * This method creates a product on the channel.
     *
     * @param MemoryProduct $product
     * @return string $id
     */
    public static function create(MemoryProduct $product): string
    {
        // Get id.
//        $product->id = ChannelState::nextInsertId('products');

        $product->id = self::generateID();

        // Create product on channel.
        self::$stateProducts[$product->id] = $product;
        return $product->id;
    }

    /**
     * Update
     *
     * This method updates a batch of products if they
     * exist in the $stateProducts class property.
     *
     * @param MemoryProduct[] $items The MemoryProduct items to update.
     * @return array An array of IDs of updated MemoryProduct items.
     */
    public static function update(array $items): array
    {
        $updated = [];
        foreach ($items as $item) {
            if(!$item->id) {
                $item->id = self::generateID();
            }
            if(!$item->product_group_id) {
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
     * This method updates images if found in the
     * $images class property.
     *
     * @param MemoryImages[] $items An array of MemoryImage items to update.
     * @return array An array of IDs from MemoryImage items which have been updated.
     */
    public static function updateImages(array $items)
    {
        $updated = [];
        foreach ($items as $i) {
            self::$stateImages[$i->id] = $i;
            $updated[] = $i->id;
        }
        return $updated;
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
    public static function getProductsList(string $offset, int $limit): array
    {
        /** @var MemoryProduct[] $list */
        $products = [];
        $start = ($offset === '')? 0: $offset;
        $list = array_slice(self::$stateProducts, $start, $limit, true);
        foreach ($list as $item) {
            $products[] = $item;
        }
        return $products;
    }

    public static function getAllProducts() {
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
            if(isset(self::$stateProducts[$id])) {
                unset(self::$stateProducts[$id]);
            }
        }
    }

    /**
     * Delete Products By Ids
     *
     * Removes matching product objects by ID.
     *
     * @param string[] $ids
     */
    public static function deleteProductsByGroupIDs(array $ids)
    {
        foreach (self::$stateProducts as $sp) {
            if(in_array($sp->product_group_id, $ids)) {
                ChannelState::deleteProductsByIDs([$sp->id]);
            }
        }
    }

    /**
     * Get Products By Group ID
     *
     * Fetches products with matching product_group_id
     *
     * @param array $ids
     * @return MemoryProduct[]
     */
    public static function getProductsByGroupID(array $ids)
    {
        $products = [];
        foreach (self::$stateProducts as $sp) {
            if(in_array($sp->product_group_id, $ids)) {
                $products [] = ChannelState::getProductsByIDs([$sp->id]);
            }
        }
        return $products;
    }

    /**
     * Get Images By Group IDs
     *
     * Return images from the channel which match the group IDs.
     *
     * @param array $ids The array of "product_group_ids" to return images for.
     * @return MemoryImage[] $images The MemoryImages associated with the "product_group_ids".
     */
    public static function getImagesByGroupIDs(array $ids)
    {
        // Get all products matching the group IDs.
        $products = self::getProductsByGroupID($ids);
        $productIds = [];
        foreach($products as $prKey => $prValue) {
            $productIds[] = array_values($prValue)[0]->id;
        }

        // Loop over the images and add the ones which
        // match the product id in "productIds".
        $images = [];
        foreach (self::$stateImages as $si) {
            if(in_array($si->product_id, $productIds)) {
                $images[] = $si;
            }
        }
        return $images;
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
            if(isset(self::$stateImages[$id])) {
                unset(self::$stateImages[$id]);
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