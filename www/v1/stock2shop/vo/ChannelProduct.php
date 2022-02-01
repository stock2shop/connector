<?php

namespace stock2shop\vo;

use stock2shop\vo\Product;
use stock2shop\vo\ChannelVariant;
use stock2shop\vo\ChannelImage;

/**
 * Channel Product
 *
 * This is the Value Object for a product from a Stock2Shop channel.
 * Use this class to model the products from your connector source
 * in terms of Stock2Shop channels.
 */
class ChannelProduct extends Product
{
    /** @var int $id */
    public $id;

    /** @var int $channel_id */
    public $channel_id;

    /** @var string $channel_product_code */
    public $channel_product_code;

    /** @var ChannelImage[] $images */
    public $images;

    /** @var ChannelVariant[] $variants */
    public $variants;

    /** @var bool $delete */
    public $delete;

    /** @var bool $success */
    public $success;

    /** @var string $success */
    public $synced;

    /**
     * Class Constructor
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data) {

        parent::__construct($data);

        $this->id = self::intFrom($data, 'id');
        $this->channel_id = self::intFrom($data, 'channel_id');
        $this->channel_product_code = self::stringFrom($data, 'channel_product_code');
        $this->images = ChannelImage::createArray(self::arrayFrom($data, 'images'));
        $this->variants = ChannelVariant::createArray(self::arrayFrom($data, 'variants'));
        $this->delete = self::boolFrom($data, 'delete');
        $this->success = self::boolFrom($data, 'success');
        $this->synced = self::stringFrom($data, 'synced');

    }

    /**
     * @param string $date
     * @return bool
     */
    static function isValidSynced(string $date): bool
    {
        $format   = 'Y-m-d H:i:s';
        $d        = \DateTime::createFromFormat($format, $date);
        $timezone = $d->getTimezone()->getName();
        return $d && ($d->format($format) == $date) && ($timezone === 'UTC');
    }

    /**
     * Computes a hash of the ChannelProduct
     *
     * @return string
     */
    public function computeHash(): string
    {
        $productHash = parent::computeHash();
        // More properties to include in the hash?
        // Order is important.
        // DO NOT include Stock2Shop DB IDs,
        // auto-increment PK might be replaced by KSUID
        $productHash .= "\nchannel_product_code=$this->channel_product_code";
        return md5($productHash);
    }

    /**
     * Creates an array of this class
     * @param array $data
     * @return ChannelProduct[]
     */
    static function createArray(array $data): array {
        $a = [];
        foreach ($data as $item) {
            $cv = new ChannelProduct((array)$item);
            $a[] = $cv;
        }
        return $a;
    }
}
