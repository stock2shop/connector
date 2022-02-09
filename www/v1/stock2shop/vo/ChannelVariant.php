<?php

namespace stock2shop\vo;

use stock2shop\exceptions\UnprocessableEntity;

/**
 * Channel Variant
 *
 * This is the ChannelVariant class.
 * It extends the Variant base class.
 *
 * Use this class to represent product variants.
 * You will add these objects in an array structure to
 * ChannelProduct objects when you code your DAL.
 *
 * @package stock2shop\vo
 */
class ChannelVariant extends Variant
{
    /** @var int $id This is the internal ID assigned to a variant by Stock2Shop. */
    public $id;

    /** @var int $product_id This is the ID of the ChannelProduct to which this variant belongs.  */
    public $product_id;

    /** @var string $channel_variant_code This is the unique identifier used by the source system. */
    public $channel_variant_code;

    /** @var bool $delete */
    public $delete;

    /** @var bool $success */
    public $success;

    /**
    * Creates the data object to spec.
    *
    * @param array $data
    * @return void
    */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->id = self::intFrom($data, 'id');
        $this->product_id = self::intFrom($data, 'product_id');
        $this->channel_variant_code = self::stringFrom($data, 'channel_variant_code');
        $this->delete = self::boolFrom($data, 'delete');
        $this->success = self::boolFrom($data, 'success');
    }

    /**
     * Compute Hash
     *
     * Computes a hash of the ChannelVariant.
     *
     * @return string
     * @throws UnprocessableEntity
     */
    public function computeHash(): string
    {
        $variantHash = parent::computeHash();
        // More properties to include in the hash?
        // Order is important.
        // DO NOT include Stock2Shop DB IDs,
        // auto-increment PK might be replaced by KSUID
        $variantHash .= "\nchannel_variant_code=$this->channel_variant_code";
        return md5($variantHash);
    }

    /**
     * Create Array
     *
     * Creates an array of this class.
     *
     * @param array $data
     * @return ChannelVariant[]
     */
    public static function createArray(array $data): array {
        $a = [];
        foreach ($data as $item) {
            $cv = new ChannelVariant((array)$item);
            $a[] = $cv;
        }
        return $a;
    }

}
