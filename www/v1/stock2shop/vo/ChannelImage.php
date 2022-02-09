<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;
use stock2shop\exceptions\UnprocessableEntity;

/**
 * Channel Image
 *
 * This is the Value Object for an image on a Stock2Shop Channel.
 * An image is always associated with a ChannelProduct.
 *
 * @package stock2shop\vo
 */
class ChannelImage extends ValueObject
{
    /** @var int|null $id */
    public $id;

    /** @var string|null $src */
    public $src;

    /** @var bool|null $active */
    public $active;

    /** @var string|null $channel_image_code */
    public $channel_image_code;

    /** @var bool|null $delete */
    public $delete;

    /** @var bool|null $success */
    public $success;

    /**
     * Default Constructor
     *
     * @param array $data
     * @throws UnprocessableEntity
     */
    public function __construct(array $data)
    {
        $this->id = self::intFrom($data, 'id');
        $this->active = self::boolFrom($data, 'active');
        $this->src = self::stringFrom($data, 'src');
        $this->channel_image_code = self::stringFrom($data, "channel_image_code");
        $this->delete = self::boolFrom($data, 'delete');
        $this->success = self::boolFrom($data, 'success');
    }

    /**
     * Valid
     *
     * Checks if the channel image is valid.
     * Valid means that the minimum required fields are set.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return (
            is_bool($this->success) &&
            is_string($this->channel_image_code) &&
            $this->channel_image_code !== ""
        );
    }

    /**
     * Create Array
     *
     * Creates an array of this class.
     *
     * @param array $data
     * @return ChannelImage[]
     * @throws UnprocessableEntity
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $ci = new ChannelImage((array)$item);
            $a[] = $ci;
        }
        return $a;
    }

}
