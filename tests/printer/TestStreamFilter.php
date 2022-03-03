<?php

namespace tests\printer;

/**
 * Test Stream Filter
 * This is a custom stream filter to get the output
 * from PhpUnit.
 */
class TestStreamFilter extends \php_user_filter
{
    public static $cache = '';
    public function filter($in, $out, &$consumed, $closing) {
        while ($bucket = stream_bucket_make_writeable($in)) {
            self::$cache .= $bucket->data;
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }
}