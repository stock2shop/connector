<?php

declare(strict_types=1);

namespace Stock2Shop\Connector\DemoAPI;

use Stock2Shop\Share;

class LineItem extends Base
{
    public ?int $id;
    public ?string $image;
    public ?string $name;
    public ?int $price;
    public ?int $total_discount;
    public ?int $total_tax;
    public ?int $price_with_tax;
    public ?int $qty;
    public ?string $sku;
    public ?int $tax_rate;
    public ?string $url;

    public function __construct(array $data)
    {
        $this->id             = self::intFrom($data, 'id');
        $this->image          = self::stringFrom($data, 'image');
        $this->name           = self::stringFrom($data, 'name');
        $this->price          = self::intFrom($data, 'price');
        $this->total_discount = self::intFrom($data, 'total_discount');
        $this->total_tax      = self::intFrom($data, 'total_tax');
        $this->price_with_tax = self::intFrom($data, 'price_with_tax');
        $this->qty            = self::intFrom($data, 'qty');
        $this->sku            = self::stringFrom($data, 'sku');
        $this->tax_rate       = self::intFrom($data, 'tax_rate');
        $this->url            = self::stringFrom($data, 'url');
    }

    /**
     * @return LineItem[]
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $a[] = new LineItem((array)$item);
        }
        return $a;
    }
}
