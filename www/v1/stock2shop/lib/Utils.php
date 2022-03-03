<?php

namespace stock2shop\lib;

/**
 * Utils
 * @package stock2shop\lib
 */
class Utils
{

    /**
     * Get MySQL Date Microseconds
     *
     * Returns microseconds-accurate timestamp used when setting the value
     * to the 'synced' property of a `vo\ChannelProduct` object which has
     * been successfully synchronized.
     *
     * @param string $mt php microtime() string
     * @return string
     */
    public static function getMySqlDateMicroseconds(string $mt = ''): string
    {
        date_default_timezone_set('UTC');
        $t = ($mt !== '') ? $mt : microtime();
        $mt = explode(' ', $t);
        $date = date('Y-m-d H:i:s', $mt[1]);
        return $date . substr($mt[0], 1, 7);
    }

}