<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Share\DTO;
use Stock2Shop\Share;

class Utils
{
    public static function render($template, $object): string
    {
        $mustache = new \Mustache_Engine();

        if (
            (is_object($object) && !isset($object->getPriceWithoutDiscount)) ||
            (is_array($object) && !isset($object['getPriceWithoutDiscount']))
        ) {
            $getPriceWithoutDiscount = function ($value, \Mustache_LambdaHelper $helper) {
                $expression = $helper->render($value);
                $split      = explode("-", $expression);
                return (int)$split[0] - (int)$split[1] - (int)$split[2];
            };
            if (is_object($object)) {
                $object->getPriceWithoutDiscount = $getPriceWithoutDiscount;
            } else {
                $object['getPriceWithoutDiscount'] = $getPriceWithoutDiscount;
            }
        }

        if (
            (is_object($object) && !isset($object->getPriceWithoutTax)) ||
            (is_array($object) && !isset($object['getPriceWithoutTax']))
        ) {
            $getPriceWithoutTax = function ($value, \Mustache_LambdaHelper $helper) {
                $expression = $helper->render($value);
                $price      = null;
                preg_match("@^[^-]*@", $expression, $price);
                $tax = null;
                preg_match("@(?<=\*\s\()(.*?)(?=\s\/)@", $expression, $tax);
                return (int)$price[0] - ((int)$price[0] * ((int)$tax[0] / 100));
            };
            if (is_object($object)) {
                $object->getPriceWithoutTax = $getPriceWithoutTax;
            } else {
                $object['getPriceWithoutTax'] = $getPriceWithoutTax;
            }
        }

        return $mustache->render($template, $object);
    }
}
