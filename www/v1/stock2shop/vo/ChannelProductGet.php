<?php

namespace stock2shop\vo;

class ChannelProductGet extends ChannelProduct
{
    /** @var string $token */
    public $token;

    /**
     * Creates the data object to spec.
     *
     * @param array $data
     *
     * @return void
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->token = self::stringFrom($data, 'token');
    }

}