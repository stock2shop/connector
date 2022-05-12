<?php

namespace stock2shop\lib;

class Utils
{
    /**
     * Wrapper for Mustache template render().
     * This includes a "calculate" lambda, which can be used for basics arithmetic
     * Consider this: https://github.com/mustache/spec/issues/41
     *
     * We try to avoid logic inside of templates.
     *
     * https://github.com/bobthecow/mustache.php/wiki/FILTERS-pragma
     *
     * "Filters are not intended to replace a proper View or ViewModel.
     * While they can be (ab)used to add logic to your templates, please
     * resist the temptation and keep your logic in code."
     *
     *
     * @param $template
     * @param $object
     * @return string
     */
    static function render($template, $object)
    {
        $mustache = new \Mustache_Engine;

        // wrapper for basic arithmetic
        // the template could look like this:
        //
        // {{# calculate}} 1+2 {{/ calculate}}
        //
        // or
        //
        // {{# calculate}} {{product.variant.grams}} / 1000 {{/ calculate}}
        if (
            (is_object($object) && !isset($object->calculate)) ||
            (is_array($object) && !isset($object['calculate']))
        ) {
            $calculate = function ($value, \Mustache_LambdaHelper $helper) {
                $expression = $helper->render($value);
                $solution = 0;
                $expressionParser = new ExpressionParser();
                if (preg_match_all('/(\w+)/', $expression, $matches) !== false) {
                    $expression = preg_replace('/\s+/', '', $expression);
                    $solution = $expressionParser->calculate($expression);
                }
                return $solution;
            };
            if (is_object($object)) {
                $object->calculate = $calculate;
            } else {
                $object['calculate'] = $calculate;
            }
        }

        // Escape values for json
        // Use json_encode to ensure the value is correctly escaped.
        if (
            (is_object($object) && !isset($object->json_escape)) ||
            (is_array($object) && !isset($object['json_escape']))
        ) {
            $json_escape = function ($value, \Mustache_LambdaHelper $helper) {

                // render value
                $json_value = $helper->render($value);

                // encode string to get valid json
                $result = json_encode($json_value);

                // remove the wrapping double quotes
                return substr($result, 1, strlen($result) - 2);
            };
            if (is_object($object)) {
                $object->json_escape = $json_escape;
            } else {
                $object['json_escape'] = $json_escape;
            }
        }

        // Generate a random string of x characters
        if (
            (is_object($object) && !isset($object->generate_random)) ||
            (is_array($object) && !isset($object['generate_random']))
        ) {
            $generate_random = function ($value, \Mustache_LambdaHelper $helper) {
                $length = (int)$value;
                if ($length === 0) {
                    $length = 6;
                }
                return self::randomString($length);
            };
            if (is_object($object)) {
                $object->generate_random = $generate_random;
            } else {
                $object['generate_random'] = $generate_random;
            }
        }
        return $mustache->render($template, $object);
    }

}