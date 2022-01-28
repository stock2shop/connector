<?php

namespace stock2shop\vo;

class SystemSegment extends Segment
{
    /** @var int $id */
    public $id;

    /** @var int $user_id */
    public $user_id;

    /** @var int $client_id */
    public $client_id;

    /**
     * SystemSegment constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\Validation
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->id        = static::intFrom($data, 'id');
        $this->user_id   = static::intFrom($data, 'user_id');
        $this->client_id = static::intFrom($data, 'client_id');
    }

    /**
     * Creates an array of Segments
     *
     * @param array $data
     * @return SystemSegment[]
     * @throws \stock2shop\exceptions\Validation
     */
    static function createArray(array $data): array {
        $a = [];
        foreach ($data as $item) {
            $pmd = new SystemSegment((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }

    /**
     * @return string
     * @throws \stock2shop\exceptions\Validation
     */
    public function computeHash(): string
    {
        return parent::computeHash();
    }
}
