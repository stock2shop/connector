<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;
use stock2shop\exceptions;

class Segment extends ValueObject
{
    /** @var string $type */
    public $type;

    /** @var string $key */
    public $key;

    /** @var string $operator */
    public $operator;

    /** @var string $value */
    public $value;

    /** @var string $owner */
    public $owner;

    /** @var string */
    const TYPE_PRODUCTS = 'products';

    /** @var string */
    const TYPE_CUSTOMERS = 'customers';

    /** @var string */
    const TYPE_ORDERS = 'orders';

    /** @var array allowed segment types */
    const ALLOWED_TYPES = [
        self::TYPE_PRODUCTS,
        self::TYPE_CUSTOMERS,
        self::TYPE_ORDERS
    ];

    /** @var string */
    const OPERATOR_EQUAL = 'equal';

    /** @var string */
    const OPERATOR_GREATER_THAN = 'greater than';

    /** @var string */
    const OPERATOR_LESS_THAN = 'less than';

    /** @var array allowed segment operators */
    const ALLOWED_OPERATORS = [
        self::OPERATOR_EQUAL,
        self::OPERATOR_GREATER_THAN,
        self::OPERATOR_LESS_THAN
    ];

    /** @var string */
    const OWNER_SOURCE = 'source';

    /** @var string */
    const OWNER_SYSTEM = 'system';

    /** @var array allowed owners */
    const ALLOWED_OWNERS = [
        self::OWNER_SOURCE,
        self::OWNER_SYSTEM
    ];

    /**
     * Segment constructor.
     * @param array $data
     * @throws exceptions\Validation
     */
    function __construct(array $data) {
        $this->type      = self::stringFrom($data, "type");
        $this->operator  = self::stringFrom($data, "operator");
        $this->key       = self::stringFrom($data, "key");
        $this->value     = self::stringFrom($data, "value");
        $this->owner     = self::stringFrom($data, "owner");

        // validate
        if(!self::isValidType($this->type)) {
            throw new exceptions\Validation("Invalid segment type " . $this->type);
        };
        if(!self::isValidOperator($this->operator)) {
            throw new exceptions\Validation("Invalid segment operator " . $this->operator);
        }
        if(!self::isValidOwner($this->owner)) {
            throw new exceptions\Validation("Invalid segment owner " . $this->owner);
        }
    }

    /**
     * @param string $type
     * @return bool
     */
    static function isValidType(string $type): bool {
        return in_array($type, self::ALLOWED_TYPES);
    }

    /**
     * @param string $operator
     * @return bool
     */
    static function isValidOperator(string $operator): bool {
        return in_array($operator, self::ALLOWED_OPERATORS);
    }

    /**
     * @param string $owner
     * @return bool
     */
    static function isValidOwner(string $owner): bool {
        return in_array($owner, self::ALLOWED_OWNERS);
    }

    /**
     * Creates an array of Segments
     *
     * @param array $data
     * @return Segment[]
     * @throws exceptions\Validation
     */
    static function createArray(array $data): array {
        $a = [];
        foreach ($data as $item) {
            $pmd = new Segment((array)$item);
            $a[] = $pmd;
        }
         return $a;
    }

    /**
     * @return string
     * @throws exceptions\Validation
     */
    public function computeHash(): string {
        $p = new Segment((array)$this);
        $json = json_encode($p);
        return md5($json);
    }
}
