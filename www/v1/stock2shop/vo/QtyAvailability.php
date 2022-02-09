<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;
use stock2shop\exceptions\UnprocessableEntity;

class QtyAvailability extends ValueObject
{

    /** @var string|null $description */
    public $description;

    /** @var float|null $qty */
    public $qty;

    /**
     * QtyAvailabilityItem constructor.
     * @param array $data
     * @throws UnprocessableEntity
     */
    public function __construct(array $data)
    {
        $this->description = self::stringFrom($data, "description");
        $this->qty = self::intFrom($data, "qty");
    }

    /**
     * Create Array
     *
     * Creates an array from this object.
     *
     * @param array $data
     * @return QtyAvailability[]
     * @throws UnprocessableEntity
     */
    public static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $pmd = new QtyAvailability((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }

}
