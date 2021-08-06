<?php

namespace stock2shop\dal\channel;

abstract class Creator
{

    /**
     * @return Connector
     */
    abstract public function getChannel(): Connector;

}

