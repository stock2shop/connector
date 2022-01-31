<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

/**
 * Flag
 *
 * This is the Value Object for a Flag item on the system.
 *
 * Flags determine if a product and variant property should be
 * updated on the channel. You can override product properties
 * in your channel by setting its flag to "channel". This is done
 * by configuring the $code class property of an object of the
 * Flag class.
 *
 * @property $client_id
 * @property $channel_id
 * @property $source_id
 * @property $table
 * @property $column
 * @property $description
 * @property $code
 */
class Flag extends ValueObject
{
    /** @var int|null $client_id */
    public $client_id;

    /** @var int|null $channel_id */
    public $channel_id;

    /** @var int|null $source_id */
    public $source_id;

    /** @var string|null |null $table */
    public $table;

    /** @var string|null |null $column */
    public $column;

    /** @var string|null |null $description */
    public $description;

    /** @var string|null |null $code system|channel|source */
    public $code;

    /**
     * Flag constructor.
     * @param array $data
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    public function __construct(array $data)
    {
        $this->description = self::stringFrom($data, 'description');
        $this->client_id   = self::intFrom($data, 'client_id');
        $this->table       = self::stringFrom($data, 'table');
        $this->column      = self::stringFrom($data, 'column');
        $this->code        = self::stringFrom($data, 'code');
        $this->source_id   = self::intFrom($data, 'source_id');
        $this->channel_id  = self::intFrom($data, 'channel_id');
    }

    /**
     * @param array $data
     * @return Flag[]
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    static function createArray(array $data): array
    {
        $a = [];
        foreach ($data as $item) {
            $pmd = new Flag((array)$item);
            $a[] = $pmd;
        }
        return $a;
    }

    /**
     * @param array $data
     * @return array
     * @throws \stock2shop\exceptions\UnprocessableEntity
     */
    static function createMap(array $data): array
    {
        $map   = [];
        $flags = self::createArray($data);
        foreach ($flags as $flag) {
            if (!isset($map[$flag->table])) {
                $map[$flag->table] = [];
            }
            $map[$flag->table][$flag->column] = $flag;
        }
        return $map;
    }
}
