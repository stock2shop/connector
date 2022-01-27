<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class SystemImage extends ValueObject
{

    /** @var int $id */
    public $id;

    /** @var bool $active */
    public $active;

    /** @var string $src */
    public $src;

    /**
     * Image constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    public function __construct(array $data)
    {
        $this->active = self::boolFrom($data, "active");
        $this->id     = self::intFrom($data, 'id');
        $this->src    = self::stringFrom($data, 'src');
    }

    /**
     * @param array $data
     * @return SystemImage[]
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $pmd = new SystemImage((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }
}
