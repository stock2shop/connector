<?php

namespace stock2shop\vo;

class ChannelVariant extends Variant
{
    /** @var int|null $id */
    public $id;

    /** @var int|null $product_id */
    public $product_id;

    /** @var string|null $channel_variant_code */
    public $channel_variant_code;

    /** @var bool|null $delete */
    public $delete;

    /** @var bool|null $success */
    public $success;

    /**
     * ChannelVariant constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->id                   = self::intFrom($data, 'id');
        $this->product_id           = self::intFrom($data, 'product_id');
        $this->channel_variant_code = self::stringFrom($data, 'channel_variant_code');
        $this->delete               = self::boolFrom($data, 'delete');
        $this->success              = self::boolFrom($data, 'success');
    }

    /**
     * Checks if the channel variant is valid.
     * Valid means that the minimum required fields are set
     *
     * TODO not sure what other properties make a "valid" variant?
     *
     * @return bool
     */
    public function valid():bool {
        return (
            is_bool($this->success) &&
            is_string($this->channel_variant_code) &&
            $this->channel_variant_code !== ""
        );
    }

    /**
     * Computes a hash of the ChannelVariant
     * @return string
     * @throws \stock2shop\exceptions\UnprocessableEntity
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
     * @param array $data
     * @return ChannelVariant[]
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $cv  = new ChannelVariant((array)$item);
            $a[] = $cv;
        }
        return $a;
    }
}
