<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;
use stock2shop\exceptions\UnprocessableEntity;

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
 * @package stock2shop\vo
 */
class Flag extends ValueObject
{
    /** @var int|null $client_id The client ID where the flag is applicable. */
    public $client_id;

    /** @var int|null $channel_id The channel ID where the flag is applicable. */
    public $channel_id;

    /** @var int|null $source_id The source ID where the flag is applicable. */
    public $source_id;

    /** @var string|null $table The table name of the column to flag. */
    public $table;

    /** @var string|null $column The name of the column to flag in the table. */
    public $column;

    /** @var string|null $description The description of the flag. */
    public $description;

    /** @var string|null $code system|channel|source */
    public $code;

    /**
     * Flag constructor.
     * @param array $data
     * @throws UnprocessableEntity
     */
    public function __construct(array $data)
    {
        $this->description = self::stringFrom($data, 'description');
        $this->client_id = self::intFrom($data, 'client_id');
        $this->table = self::stringFrom($data, 'table');
        $this->column = self::stringFrom($data, 'column');
        $this->code = self::stringFrom($data, 'code');
        $this->source_id = self::intFrom($data, 'source_id');
        $this->channel_id = self::intFrom($data, 'channel_id');
    }

    /**
     * @param array $data
     * @return Flag[]
     * @throws UnprocessableEntity
     */
    public static function createArray(array $data): array
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
     * @throws UnprocessableEntity
     */
    public static function createMap(array $data): array
    {
        $map = [];
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
