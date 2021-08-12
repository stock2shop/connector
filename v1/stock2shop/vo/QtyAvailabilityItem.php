<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class QtyAvailabilityItem extends ValueObject
{
    /** @var string $description */
    public $description;

    /** @var float $qty */
    public $qty;

    /**
     * QtyAvailabilityItem constructor.
     * @param array $data
     */
    function __construct(array $data) {
        $this->description = self::stringFrom($data, "description");
        $this->qty = self::intFrom($data, "qty");
    }

    /**
     * Creates the data object to spec.
     *
     * @param array $data
     *
     * @return ValueObject
     */
    public function create(array $data): ValueObject {
        $this->description = (string)$data['description'];
        $this->qty = (int)$data['qty'];

        return $this;
    }

    /**
     * Creates an array of this class
     * @param array $data
     * @return QtyAvailabilityItem[]
     */
    static function createArray(array $data): array {
        $a = [];
        foreach ($data as $item) {
            $pmd = new QtyAvailabilityItem((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }
}
