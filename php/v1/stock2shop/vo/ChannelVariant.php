<?php

namespace stock2shop\vo;

class ChannelVariant extends Variant
{
    /** @var int $id */
    public $id;

    /** @var int $product_id */
    public $product_id;

    /** @var string $channel_variant_code */
    public $channel_variant_code;

    /** @var bool $delete */
    public $delete;

    /** @var bool $success */
    public $success;

    /**
    * Creates the data object to spec.
    *
    * @param array $data
    *
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
     * Computes a hash of the ChannelVariant
     *
     * @return string
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
     * Creates an array of this class
     * @param array $data
     * @return ChannelVariant[]
     */
    static function createArray(array $data): array {
        $a = [];
        foreach ($data as $item) {
            $cv = new ChannelVariant((array)$item);
            $a[] = $cv;
        }
        return $a;
    }
}
