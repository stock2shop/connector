<?php

namespace stock2shop\dal\channels\example;

use stock2shop\dal\channel\Products as ProductsInterface;
use stock2shop\exceptions;
use stock2shop\lib;
use stock2shop\vo;

/**
 * Example showing how to build an ExampleProduct from a S2S ChannelProduct.
 * We also show how you can use Channel Meta (configuration) to make the
 * resulting ExampleProduct configurable.
 *
 * @package stock2shop\dal\example
 */
class ProductMapper
{

    private $default_map = '';

    function ConvertS2SToExampleProduct() {

    }

}