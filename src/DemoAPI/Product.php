<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

/**
 * @psalm-import-type OptionData from Option
 * @psalm-import-type ImageData from Image
 * @psalm-type ProductData = array{
 *     id: ?string,
 *     name: string,
 *     options: OptionData[],
 *     images: ImageData[]
 * }
 */
class Product extends Base
{
    public ?string $id;
    public string $name;
    /** @var Option[] */
    public array $options;
    /** @var Image[] */
    public array $images;

    /** @param ProductData $data */
    public function __construct(array $data)
    {
        $options = self::arrayFrom($data, 'options');
        $images  = self::arrayFrom($data, 'images');

        $this->id      = self::stringFrom($data, 'id');
        $this->name    = self::stringFrom($data, 'name');
        $this->options = Option::createArray($options);
        $this->images  = Image::createArray($images);
    }

    /**
     * @param ProductData[] $data
     * @return Product[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $a[] = new Product((array)$item);
        }
        return $a;
    }
}
