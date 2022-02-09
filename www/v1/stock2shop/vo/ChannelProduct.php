<?php

namespace stock2shop\vo;

use stock2shop\exceptions\UnprocessableEntity;

/**
 * Channel Product
 *
 * This is the ChannelProduct class.
 * It extends the Product base class.
 *
 * @package stock2shop\vo
 */
class ChannelProduct extends Product
{
    /** @var int|null $id */
    public $id;

    /** @var string|null $source_product_code */
    public $source_product_code;

    /** @var int|null $channel_id */
    public $channel_id;

    /** @var int|null $client_id */
    public $client_id;

    /** @var string $channel_product_code */
    public $channel_product_code;

    /** @var ChannelImage[] $images */
    public $images;

    /** @var ChannelVariant[] $variants */
    public $variants;

    /** @var bool|null $delete */
    public $delete;

    /** @var bool|null $success */
    public $success;

    /** @var string|null $success */
    public $synced;

    /**
     * Default Constructor
     *
     * @param array $data
     * @throws UnprocessableEntity
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->id = self::intFrom($data, 'id');
        $this->source_product_code = self::stringFrom($data, 'source_product_code');
        $this->channel_id = self::intFrom($data, 'channel_id');
        $this->client_id = self::intFrom($data, 'client_id');
        $this->channel_product_code = self::stringFrom($data, 'channel_product_code');
        $this->variants = ChannelVariant::createArray(self::arrayFrom($data, 'variants'));
        $this->images = ChannelImage::createArray(self::arrayFrom($data, 'images'));
        $this->delete = self::boolFrom($data, 'delete');
        $this->success = self::boolFrom($data, 'success');
        $this->synced = self::stringFrom($data, 'synced');
    }

    /**
     * Valid
     *
     * Checks if the channel product is valid.
     * Valid means that the minimum required fields are set.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return (
            is_bool($this->success) &&
            !is_null($this->channel_product_code) &&
            $this->channel_product_code !== "" &&
            is_string($this->synced) && // TODO we could add n date format check to see if has milliseconds?
            $this->synced !== ""
        );
    }

    /**
     * Sort
     *
     * Sorts the array properties of ChannelProduct.
     *
     * @return void
     */
    public function sort()
    {
        $this->sortArray($this->images, "id");
        $this->sortArray($this->variants, "id");
    }

    /**
     * Compute Hash
     *
     * Computes a hash of the ChannelProduct
     *
     * @return string
     * @throws UnprocessableEntity
     */
    public function computeHash(): string
    {
        $productHash = parent::computeHash();

        $this->sort();

        // More properties to include in the hash?
        // Order is important.
        // DO NOT include Stock2Shop DB IDs,
        // auto-increment PK might be replaced by KSUID
        $productHash .= "\nchannel_product_code=$this->channel_product_code";
        foreach ($this->images as $i) {
            // src contains a hash of the image content
            $productHash .= "\nimage_$i->id=" . $i->src;
        }
        foreach ($this->variants as $v) {
            $productHash .= "\nvariant_$v->id=" . $v->computeHash();
        }

        return md5($productHash);
    }

    /**
     * Create Array
     *
     * Creates an array from this object.
     *
     * @param array $data
     * @return ChannelProduct[]
     * @throws UnprocessableEntity
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $cv = new ChannelProduct((array)$item);
            $a[] = $cv;
        }
        return $a;
    }

}