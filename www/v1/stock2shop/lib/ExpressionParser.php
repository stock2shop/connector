<?php
// Code copied from here:
// http://stackoverflow.com/a/27077376/639133

namespace stock2shop\lib;

class ExpressionParser
{
    const PATTERN = '/(?:\-?\d+(?:\.?\d+)?[\+\-\*\/])+\-?\d+(?:\.?\d+)?/';

    const PARENTHESIS_DEPTH = 10;

    public function calculate($input) {
        if (strpos($input, '+') != null || strpos($input, '-') != null || strpos($input, '/') != null || strpos($input, '*') != null) {
            //  Remove white spaces and invalid math chars
            $input = str_replace(',', '.', $input);
            $input = preg_replace('[^0-9\.\+\-\*\/\(\)]', '', $input);

            // remove leading zeros
            $input = self::clearLeadingZeroes($input);

            // double negative
            $input = str_replace('--', '+', $input);

            //  Calculate each of the parenthesis from the top
            $i = 0;
            while (strpos($input, '(') || strpos($input, ')')) {
                $input = preg_replace_callback('/\(([^\(\)]+)\)/', 'self::callback', $input);

                $i++;
                if ($i > self::PARENTHESIS_DEPTH) {
                    break;
                }
            }

            //  Calculate the result
            if (preg_match(self::PATTERN, $input, $match)) {
                return $this->compute($match[0]);
            }

            return 0;
        }

        return $input;
    }

    private static function clearLeadingZeroes($input) {
        $previousError = false;
        $cleanedInput  = "";
        $chars         = str_split($input);
        foreach ($chars as $i => $char) {

            // Check if following char is a number
            if (
                $char === "0" &&
                isset($chars[$i + 1]) &&
                is_numeric($chars[$i + 1])
            ) {

                // remove zero if previous char is a not a number or error
                if (
                    !isset($chars[$i - 1]) ||
                    !is_numeric($chars[$i - 1]) ||
                    $previousError
                ) {
                    $previousError = true;
                } else {
                    $cleanedInput  .= $char;
                    $previousError = false;
                }
            } else {
                $cleanedInput  .= $char;
                $previousError = false;
            }
        }
        return $cleanedInput;
    }

    private function compute($input) {
        $code = 'return ' . $input . ';';
        $compute = create_function('', $code);
        return 0 + $compute();
    }

    private function callback($input) {
        if (is_numeric($input[1])) {
            return $input[1];
        } elseif (preg_match(self::PATTERN, $input[1], $match)) {
            return $this->compute($match[0]);
        }

        return 0;
    }
}