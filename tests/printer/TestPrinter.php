<?php

namespace tests\printer;

use tests\printer\TestStreamFilter;

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
    /** @var int $defaultPadding */
    public $defaultPadding = 50;

    /** @var int $headingPadding */
    public $headingPadding = 100;

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
    private function outputLine(string $name, $value) {
        $outputValue = $value;
        if(is_bool($value)) {
            if($value === true) {
                $outputValue = "true";
            } else {
                $outputValue = "false";
            }
        }
        $this->output .= str_pad($name, $this->defaultPadding) . ' ' . $this->defaultPadString . ' ' . $outputValue . PHP_EOL;
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
        foreach($this->lines as $heading => $lines) {
            $this->outputHeading($heading);
            foreach($lines as $lKey => $lVal) {
                $this->outputLine($lKey, $lVal);
            }
        }
    }

    /**
     * Print
     *
     * Outputs the content in 'output'.
     *
     * @return void
     */
    public function print($return=false) {
        if($this->output === "") {
            $this->prepare();
        }

        // Register filter.
        stream_filter_register("TestPrinterStream", "tests\printer\TestStreamFilter");
        $stdout = fopen('php://stdout', 'w');

        // Append stream to stdout.
        stream_filter_append($stdout, 'TestPrinterStream');

        // Write content.
        fwrite($stdout, $this->output);

        // Output.
        print(TestStreamFilter::$cache);

    }

}