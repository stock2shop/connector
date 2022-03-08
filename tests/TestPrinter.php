<?php

namespace tests;

use stock2shop\vo;

/**
 * Test Printer
 *
 * This class defines the behavior and state for test printer.
 * Based on the original 'channel.php' test from the connector
 * repository.
 *
 * @package tests
 */
class TestPrinter
{
    const DEBUG_ENV_VAR = 'S2S_TEST_DEBUG'; // if set to 'true' will print data objects

    /** @var int $defaultPadding */
    public $defaultPadding = 55;

    /** @var int $headingPadding */
    public $headingPadding = 135;

    /** @var string $headingPadString */
    public $headingPadString = "-";

    /** @var string $defaultPadString */
    public $defaultPadString = "=";

    /** @var array $lines */
    public $lines = [];

    /** @var string $output */
    public $output = "";

    /** @var string $section */
    public $section = "";

    /**
     * Add Line
     *
     * @param string $section
     * @param string $name
     * @param string $value
     * @return void
     */
    public function addLine(string $name, $value=null) {
        $this->lines[$this->section][$name] = (is_array($value) ? json_encode($value) : $value);
    }

    /**
     * Add Heading
     *
     * @param string $name
     * @return void
     */
    public function addHeading(string $name) {
        $this->lines[$name] = [];
        $this->section = $name;
    }

    /**
     * Output Heading
     *
     * @param string $heading
     * @return void
     */
    private function outputHeading(string $heading) {
        $this->output .= PHP_EOL;
        $this->output .= str_pad('', $this->headingPadding, $this->defaultPadString) . PHP_EOL;
        $this->output .= str_pad($heading, $this->headingPadding, $this->headingPadString, STR_PAD_BOTH) . PHP_EOL;
        $this->output .= str_pad('', $this->headingPadding, $this->defaultPadString) . PHP_EOL;
        $this->output .= PHP_EOL;
    }

    /**
     * Output Line
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    private function outputLine(string $name, $value, $value2=null) {
        $outputValue = $value;
        $outputValue2 = $value2;
        if(is_bool($value)) {
            if($value === true) {
                $outputValue = "true";
            } else {
                $outputValue = "false";
            }
        }
        if(is_null($value)) {
            $outputValue = "null";
        }
        if(is_null($value2)) {
            $outputValue2 = "null";
        }



        $this->output .= str_pad($name, $this->defaultPadding) . ' ' . $this->defaultPadString . ' ' . str_pad($this->prepareValue($outputValue), $this->defaultPadding) . ' ' . $this->defaultPadString . ' ' . $this->prepareValue($outputValue2) . PHP_EOL;
    }

    /**
     * Prepare Value
     *
     * Used to shorten a string if necessary, otherwise
     * returns the variable as is.
     *
     * @param mixed $value
     * @return mixed The shortened string or the original value.
     */
    private function prepareValue($value) {
        if(is_string($value) && strlen($value) >= $this->defaultPadding) {
            $value = substr($value,0, ($this->defaultPadding - 10)) . "..";
        }
        return $value;
    }

    /**
     * Prepare
     *
     * Prints the content for a section to the terminal/stdout.
     * The output format is defined by the constants in this class.
     *
     * @return void
     */
    private function prepare() {

        // Iterate over the lines property/map.
        foreach($this->lines as $heading => $lines) {
            $this->outputHeading($heading);
            foreach($lines as $lKey => $lVal) {
                $this->outputLine($lKey, $lVal);
            }
        }

        // Clear the lines property.
        unset($this->lines);
        $this->lines = [];

    }

    /**
     * Print
     *
     * Outputs the content in 'output'.
     *
     * @return void
     */
    public function print($return=false) {

        $debug = getenv(self::DEBUG_ENV_VAR);
        if($debug !== 'true') {
            $this->prepare();

            // Register filter.
            stream_filter_register("TestPrinterStream", "tests\TestStreamFilter");
            $stdout = fopen('php://stdout', 'w');

            // Append stream to stdout.
            stream_filter_append($stdout, 'TestPrinterStream');

            // Write content.
            fwrite($stdout, $this->output);

            // Output.
            print(TestStreamFilter::$cache);
            unset($this->output);
            $this->output = "";
        }
    }

    /**
     * Send Products To Printer
     *
     * @param vo\ChannelProduct[] $products
     * @param vo\ChannelProduct[] $responses
     * @return void
     */
    public function sendProductsToPrinter(array $products, array $responses, string $heading) {
        $this->addHeading($heading);
        $cProducts = count($responses);
        if($cProducts === 0) {
            return;
        }

        for($pKey=0; $pKey!==$cProducts; $pKey++) {
            $this->addLine('product[' . $pKey . ']->id', $products[$pKey]->id, $responses[$pKey]->id);
            $this->addLine('product[' . $pKey . ']->channel_product_code', $products[$pKey]->channel_product_code, $responses[$pKey]->channel_product_code);
            $this->addLine('product[' . $pKey . ']->success', $products[$pKey]->success, $responses[$pKey]->success);
            $this->addLine('product[' . $pKey . ']->delete', $products[$pKey]->delete, $responses[$pKey]->delete);

            for($vKey=0; $vKey!==count($products[$pKey]->variants); $vKey++) {
                $this->addLine('product[' . $pKey . ']->variant[' . $vKey . ']->id', $products[$pKey]->variants[$vKey]->id, $responses[$pKey]->variants[$vKey]->id);
                $this->addLine('product[' . $pKey . ']->variant[' . $vKey . ']->channel_variant_code', $products[$pKey]->variants[$vKey]->channel_variant_code, $responses[$pKey]->variants[$vKey]->channel_variant_code);
                $this->addLine('product[' . $pKey . ']->variant[' . $vKey . ']->success', $products[$pKey]->variants[$vKey]->success, $responses[$pKey]->variants[$vKey]->success);
                $this->addLine('product[' . $pKey . ']->variant[' . $vKey . ']->delete', $products[$pKey]->variants[$vKey]->delete, $responses[$pKey]->variants[$vKey]->delete);
                $this->addLine('product[' . $pKey . ']->variant[' . $vKey . ']->sku', $products[$pKey]->variants[$vKey]->sku, $responses[$pKey]->variants[$vKey]->sku);
            }
            unset($vKey);

            for($iKey=0; $iKey!==count($products[$pKey]->images); $iKey++) {
                $this->addLine('product[' . $pKey . ']->image[' . $iKey . ']->id', $products[$pKey]->images[$iKey]->id, $responses[$pKey]->images[$iKey]->id);
                $this->addLine('product[' . $pKey . ']->image[' . $iKey . ']->success', $products[$pKey]->images[$iKey]->success, $responses[$pKey]->images[$iKey]->success);
                $this->addLine('product[' . $pKey . ']->image[' . $iKey . ']->delete', $products[$pKey]->images[$iKey]->delete, $responses[$pKey]->images[$iKey]->delete);
                $this->addLine('product[' . $pKey . ']->image[' . $iKey . ']->channel_image_code', $products[$pKey]->images[$iKey]->channel_image_code, $responses[$pKey]->images[$iKey]->channel_image_code);
                $this->addLine('product[' . $pKey . ']->image[' . $iKey . ']->src', $products[$pKey]->images[$iKey]->src, $responses[$pKey]->images[$iKey]->src);
                $this->addLine('product[' . $pKey . ']->image[' . $iKey . ']->active', $products[$pKey]->images[$iKey]->active, $responses[$pKey]->images[$iKey]->active);
            }
            unset($iKey);
            $this->addLine('', '');
        }
        unset($pKey);
    }

}