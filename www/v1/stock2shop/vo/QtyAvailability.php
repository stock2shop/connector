<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class QtyAvailability extends ValueObject
{

    /** @var string|null $description */
    public $description;

    /** @var float|null $qty */
    public $qty;

    /**
     * QtyAvailabilityItem constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    function __construct(array $data)
    {
        $this->description = self::stringFrom($data, "description");
        $this->qty         = self::intFrom($data, "qty");
    }

    /**
     * @param array $data
     * @return QtyAvailability[]
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $pmd = new QtyAvailability((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }

}
