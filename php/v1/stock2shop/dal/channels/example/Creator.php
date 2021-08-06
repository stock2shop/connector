<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel;

class Creator extends channel\Creator
{
    public function getChannel(): channel\Connector
    {
        return new Connector();
    }
}
