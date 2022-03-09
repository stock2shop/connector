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
    public $defaultPadding = 25;

    /** @var int $headingPadding */
    public $headingPadding = 135;

    /** @var string $headingPadString */
    public $headingPadString = "-";

    /** @var string $defaultPadString */
    public $defaultPadString = " ";

    /** @var string $output */
    public $output = "";

    /**
     * Output Heading
     *
     * @param string $heading
     * @return void
     */
    private function outputHeading(string $heading)
    {
        $this->output .= PHP_EOL;
        $this->output .= str_pad('', $this->headingPadding, $this->defaultPadString) . PHP_EOL;
        $this->output .= str_pad($heading, $this->headingPadding, $this->headingPadString, STR_PAD_BOTH) . PHP_EOL;
        $this->output .= str_pad('', $this->headingPadding, $this->defaultPadString) . PHP_EOL;
        $this->output .= PHP_EOL;
    }

    /**
     * Output Line
     *
     * @param array $line
     * @return void
     */
    private function outputLine(array $values)
    {
        foreach ($values as $value) {
            if (is_bool($value)) {
                if ($value === true) {
                    $value = "true";
                } else {
                    $value = "false";
                }
            } elseif (is_null($value)) {
                $value = 'null';
            }
            $o = str_pad($this->prepareValue($value), $this->defaultPadding);
            $this->output .= $o;
        }
        $this->output .= PHP_EOL;
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
    private function prepareValue($value)
    {
        if (is_string($value) && strlen($value) >= $this->defaultPadding) {
            $value = substr($value, 0, ($this->defaultPadding - 10)) . "..";
        }
        return $value;
    }

    /**
     * Print
     *
     * Outputs the content in 'output'.
     *
     * @return void
     */
    public function print()
    {
        $debug = getenv(self::DEBUG_ENV_VAR);
        if ($debug === 'true') {

            // Register filter.
            stream_filter_register("TestPrinterStream", "tests\TestStreamFilter");
            $stdout = fopen('php://stdout', 'w');

            // Append stream to stdout.
            stream_filter_append($stdout, 'TestPrinterStream');

            // Write content.
            fwrite($stdout, $this->output);

            // Output.
            print(TestStreamFilter::$cache);
            $this->output = "";
        }
    }

    /**
     * Send Products To Printer
     *
     * @param vo\ChannelProduct[] $products the request sent to sync()
     * @param vo\ChannelProduct[] $responses the response received from sync()
     * @param vo\ChannelProduct[] $existing the items on the channel fethced by getByCode()
     * @return void
     */
    public function sendProductsToPrinter(array $products, array $responses, array $existing, string $heading)
    {
        $this->outputHeading($heading);
        if(count($products) > 0) {

            // get a map of existing products
            $mapExistingProducts = [];
            $mapExistingVariants = [];
            $mapExistingImages = [];
            foreach ($existing as $p) {
                $mapExistingProducts[$p->channel_product_code] = $p;
                foreach ($p->variants as $v) {
                    $mapExistingVariants[$v->channel_variant_code] = $v;
                }
                foreach ($p->images as $img) {
                    $mapExistingImages[$img->channel_image_code] = $img;
                }
            }

            $this->outputLine([
                'TYPE',
                'CODE',
                'PROPERTY',
                'REQUEST',
                'RESPONSE',
                'ON CHANNEL',
            ]);
            foreach ($products as $k => $p) {
                $r = $responses[$k] ?? new vo\ChannelProduct([]);
                $ep = $mapExistingProducts[$p->channel_product_code] ?? new vo\ChannelProduct([]);
                $this->outputLine([
                    'product',
                    $p->channel_product_code,
                    'channel_product_code',
                    $p->channel_product_code,
                    $r->channel_product_code,
                    $ep->channel_product_code
                ]);
                $this->outputLine([
                    'product',
                    $p->channel_product_code,
                    'success',
                    $p->success,
                    $r->success,
                    $ep->success
                ]);
                $this->outputLine([
                    'product',
                    $p->channel_product_code,
                    'delete',
                    $p->delete,
                    $r->delete,
                    $ep->delete
                ]);
                foreach ($p->variants as $kv => $v) {
                    $rv = $responses[$k]->variants[$kv] ?? new vo\ChannelVariant([]);
                    $ep = $mapExistingVariants[$v->channel_variant_code] ?? new vo\ChannelVariant([]);
                    $this->outputLine([
                        'variant',
                        $v->channel_variant_code,
                        'channel_variant_code',
                        $v->channel_variant_code,
                        $rv->channel_variant_code,
                        $ep->channel_variant_code
                    ]);
                    $this->outputLine([
                        'variant',
                        $v->channel_variant_code,
                        'success',
                        $v->success,
                        $rv->success,
                        $ep->success
                    ]);
                    $this->outputLine([
                        'variant',
                        $v->channel_variant_code,
                        'delete',
                        $v->delete,
                        $rv->delete,
                        $ep->delete
                    ]);
                }
                foreach ($p->images as $ki => $img) {
                    $ri = $responses[$k]->images[$ki] ?? new vo\ChannelImage([]);
                    $ei = $mapExistingImages[$img->channel_image_code] ?? new vo\ChannelImage([]);
                    $this->outputLine([
                        'image',
                        $img->channel_image_code,
                        'channel_image_code',
                        $img->channel_image_code,
                        $ri->channel_image_code,
                        $ei->channel_image_code

                    ]);
                    $this->outputLine([
                        'image',
                        $img->channel_image_code,
                        'success',
                        $img->success,
                        $ri->success,
                        $ei->success
                    ]);
                    $this->outputLine([
                        'image',
                        $img->channel_image_code,
                        'delete',
                        $img->delete,
                        $ri->delete,
                        $ei->delete
                    ]);
                }
            }
        }
    }

}