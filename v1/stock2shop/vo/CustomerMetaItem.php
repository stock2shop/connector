<?php

namespace stock2shop\vo;

use stock2shop\base\ValueObject;

class CustomerMetaItem extends ValueObject
{
    /** @var string $key */
    public $key;

    /** @var string $template_name */
    public $template_name;

    /** @var string $value */
    public $value;

    /**
     * Creates the data object to spec.
     *
     * @param array $data
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->key           = self::stringFrom($data, 'key');
        $this->template_name = self::stringFrom($data, 'template_name');
        $this->value         = self::stringFrom($data, 'value');
    }

    /**
     * Creates an array of this class.
     *
     * @param array $data
     *
     * @return CustomerMetaItem[]
     */
    static function createArray(array $data): array
    {
        $returnable = [];
        foreach ($data as $item) {
            $returnable[] = new CustomerMetaItem((array)$item);
        }
        return $returnable;
    }
}
