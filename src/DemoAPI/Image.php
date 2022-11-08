<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

/** @psalm-type ImageData = array{
 *     id: ?string,
 *     url: string
 * }
 */
class Image extends Base
{
    public ?string $id;
    public string $url;

    /** @param ImageData $data */
    public function __construct(array $data)
    {
        $this->id  = self::stringFrom($data, 'id');
        $this->url = self::stringFrom($data, 'url');
    }

    /**
     * @param ImageData[] $data
     * @return Image[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $a[] = new Image((array)$item);
        }
        return $a;
    }
}
