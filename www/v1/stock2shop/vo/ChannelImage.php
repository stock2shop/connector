<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

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
     * ChannelImage constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    public function __construct(array $data)
    {
        $this->id                 = self::intFrom($data, 'id');
        $this->active             = self::boolFrom($data, 'active');
        $this->src                = self::stringFrom($data, 'src');
        $this->channel_image_code = self::stringFrom($data, "channel_image_code");
        $this->delete             = self::boolFrom($data, 'delete');
        $this->success            = self::boolFrom($data, 'success');
    }

    /**
     * Returns true if the image is synced with a channel.
     *
     * @return bool
     */
    public function hasSyncedToChannel(): bool
    {
        return (
            $this->success &&
            !is_null($this->channel_image_code) &&
            $this->channel_image_code !== ""
        );
    }

    /**
     * Creates an array of this class
     * @param array $data
     * @return ChannelImage[]
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $ci  = new ChannelImage((array)$item);
            $a[] = $ci;
        }
        return $a;
    }
}
