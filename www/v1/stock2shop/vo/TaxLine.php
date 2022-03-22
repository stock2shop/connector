<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class TaxLine extends ValueObject
{
    /** @var int|null $client_id */
    public $client_id;

    /** @var int|null $orderitem_id */
    public $orderitem_id;

    /** @var string|null $created */
    public $created;

    /** @var string|null $modified */
    public $modified;

    /** @var string|null $title */
    public $title;

    /** @var string|null $code */
    public $code;

    /** @var float|null $price */
    public $price;

    /** @var float|null $rate */
    public $rate;

    /**
     * TaxLine constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    function __construct(array $data)
    {
        $this->client_id = self::intFrom($data, 'client_id');
        $this->orderitem_id = self::intFrom($data, 'orderitem_id');
        $this->created = self::stringFrom($data, 'created');
        $this->modified = self::stringFrom($data, 'modified');
        $this->title = self::stringFrom($data, 'title');
        $this->code = self::stringFrom($data, 'code');
        $this->price = self::floatFrom($data, 'price');
        $this->rate = self::floatFrom($data, 'rate');
    }

    /**
     * @param array $data
     * @return TaxLine[]
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $pmd = new TaxLine((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }
}
