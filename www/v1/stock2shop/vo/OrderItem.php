<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class OrderItem extends ValueObject
{
    /** @var int|null $order_id */
    public $order_id;

    /** @var int|null $client_id */
    public $client_id;

    /** @var int|null $product_id */
    public $product_id;

    /** @var int|null $variant_id */
    public $variant_id;

    /** @var int|null $source_id */
    public $source_id;

    /** @var string|null $source_variant_code */
    public $source_variant_code;

    /** @var string|null $created */
    public $created;

    /** @var string|null $modified */
    public $modified;

    /** @var string|null $barcode */
    public $barcode;

    /** @var string|null $sku */
    public $sku;

    /** @var string|null $title */
    public $title;

    /** @var int|null $grams */
    public $grams;

    /** @var string|null $code */
    public $code;

    /** @var int|null $qty */
    public $qty;

    /** @var float|null $price */
    public $price;

    /** @var string|null $price_display */
    public $price_display;

    /** @var float|null $total_discount */
    public $total_discount;

    /** @var string|null $total_discount_display */
    public $total_discount_display;

    /** @var float|null $tax_per_unit */
    public $tax_per_unit;

    /** @var string|null $tax_per_unit_display */
    public $tax_per_unit_display;

    /** @var float|null $tax */
    public $tax;

    /** @var string|null $tax_display */
    public $tax_display;

    /** @var float|null $sub_total */
    public $sub_total;

    /** @var string|null $sub_total_display */
    public $sub_total_display;

    /** @var float|null $total */
    public $total;

    /** @var string|null $total_display */
    public $total_display;

    /** @var Fulfillment $fulfillments */
    public $fulfillments;

    /** @var TaxLine $tax_lines */
    public $tax_lines;

    /**
     * OrderItem constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    function __construct(array $data)
    {
        $this->order_id = self::intFrom($data, 'order_id');
        $this->client_id = self::intFrom($data, 'client_id');
        $this->product_id = self::intFrom($data, 'product_id');
        $this->variant_id = self::intFrom($data, 'variant_id');
        $this->source_id = self::intFrom($data, 'source_id');
        $this->source_variant_code = self::stringFrom($data, 'source_variant_code');
        $this->created = self::stringFrom($data, 'created');
        $this->modified = self::stringFrom($data, 'modified');
        $this->barcode = self::stringFrom($data, 'barcode');
        $this->sku = self::stringFrom($data, 'sku');
        $this->title = self::stringFrom($data, 'title');
        $this->grams = self::intFrom($data, 'grams');
        $this->code = self::stringFrom($data, 'code');
        $this->qty = self::intFrom($data, 'qty');
        $this->price = self::floatFrom($data, 'price');
        $this->price_display = self::stringFrom($data, 'price_display');
        $this->total_discount = self::floatFrom($data, 'total_discount');
        $this->total_discount_display = self::stringFrom($data, 'total_discount_display');
        $this->tax_per_unit = self::floatFrom($data, 'tax_per_unit');
        $this->tax_per_unit_display = self::stringFrom($data, 'tax_per_unit_display');
        $this->tax = self::floatFrom($data, 'tax');
        $this->tax_display = self::stringFrom($data, 'tax_display');
        $this->sub_total = self::floatFrom($data, 'sub_total');
        $this->sub_total_display = self::stringFrom($data, 'sub_total_display');
        $this->total = self::floatFrom($data, 'total');
        $this->total_display = self::stringFrom($data, 'total_display');
        $this->fulfillments = Fulfillment::createArray(self::arrayFrom($data, 'fulfillments'));
        $this->tax_lines = TaxLine::createArray(self::arrayFrom($data, 'tax_lines'));
    }

    /**
     * @param array $data
     * @return OrderItem[]
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $pmd = new OrderItem((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }
}
