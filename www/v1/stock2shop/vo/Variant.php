<?php

namespace stock2shop\vo;

use stock2shop\vo\Meta;
use stock2shop\vo\QtyAvailability;
use stock2shop\vo\PriceTier;
use stock2shop\base\ValueObject;

class Variant extends ValueObject
{
    /** @var string $source_variant_code */
    public $source_variant_code;

    /** @var string $sku */
    public $sku;

    /** @var bool $active */
    public $active;

    /** @var int $qty */
    public $qty;

    /** @var QtyAvailability $qty_availability */
    public $qty_availability;

    /** @var float $price */
    public $price;

    /** @var PriceTier[] $price_tiers */
    public $price_tiers;

    /** @var string $barcode */
    public $barcode;

    /** @var bool $inventory_management */
    public $inventory_management;

    /** @var int $grams */
    public $grams;

    /** @var string $option1 */
    public $option1;

    /** @var string $option2 */
    public $option2;

    /** @var string $option3 */
    public $option3;

    /** @var MetaItem[] $meta */
    public $meta;

    /**
     * Variant constructor.
     * @param array $data
     */
    function __construct(array $data) {
        $this->source_variant_code =
            self::stringFrom($data, "source_variant_code");
        $this->sku = self::stringFrom($data, "sku");
        $this->active = self::boolFrom($data, "active");
        $this->qty = self::intFrom($data, "qty");

        // TODO: Replace this without another object.
        // TODO: Ask Chris.
        $this->qty_availability =
            QtyAvailability::createArray(self::arrayFrom($data, "qty_availability"));
        $this->price = self::floatFrom($data, "price");
        $this->price_tiers =
            PriceTier::createArray(self::arrayFrom($data, "price_tiers"));
        $this->barcode = self::stringFrom($data, "barcode");
        $this->inventory_management = self::boolFrom($data, "inventory_management");
        $this->grams = self::intFrom($data, "grams");
        $this->option1 = self::stringFrom($data, "option1");
        $this->option2 = self::stringFrom($data, "option2");
        $this->option3 = self::stringFrom($data, "option3");

        // TODO: Replace with Params?
        $this->meta = Meta::createArray(self::arrayFrom($data, "meta"));
    }

    /**
     * sort array properties of Variant
     */
    public function sort() {
        $this->sortArray($this->qty_availability, "description");
        $this->sortArray($this->price_tiers, "tier");
        $this->sortArray($this->meta, "key");
    }

    /**
     * Computes a hash of the system product excluding the variants.
     *
     * @return string
     */
    public function computeHash(): string {
        $v = new Variant((array)$this);
        $v->sort();
        $json = json_encode($v);

        return md5($json);
    }
}
