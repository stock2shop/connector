<?php

namespace stock2shop\dal\channels\example;

use stock2shop\vo;

/**
 * Stores the state for the channel in memory as an example
 * i.e. products, orders ad fulfillments
 *
 * @package stock2shop\dal\example
 */
class ChannelState
{

    /**
     * @var ExampleProduct[]
     */
    private static $products = [];

    /**
     * @param ExampleProduct[] $cp
     */
    static function setProducts(array $cp) {
        self::$products = $cp;
    }

    /**
     * @param string[] $codes
     * @return ExampleProduct[]
     */
    static function getProductsByCode(array $codes): array {
        $exampleProducts = [];
        foreach (self::$products as $p) {
            if(in_array($p->id, $codes)) {
                $exampleProducts[] = $p;
            }
        }
        return $exampleProducts;
    }

}