<?php

namespace stock2shop\dal\channels\example\data;

class Helper
{
    const DATA_PATH = __DIR__;

    /**
     * Get JSON Files By Prefix
     *
     * This method returns all the JSON files with the specified
     * prefix.
     *
     * @param string $prefix
     * @param string $type
     * @return array
     */
    static function getJSONFilesByPrefix(string $prefix, string $type): array
    {
        $files = self::getJSONFiles($type);
        $items = [];
        foreach ($files as $fileName => $obj) {
            if (strpos($fileName, $prefix) === 0) {
                $items[$fileName] = $obj;
            }
        }
        return $items;
    }

    /**
     * Get JSON Files
     *
     * Internally uses file_get_contents() to get the data from each
     * file found in the data directory configured in the DATA_PATH
     * class constant, by connector $type name.
     *
     * @param $type Connector type name.
     * @return array
     */
    static function getJSONFiles($type): array
    {
        $path      = self::DATA_PATH . '/' . $type;
        $files     = [];
        $fileNames = array_diff(scandir($path, SCANDIR_SORT_ASCENDING), array('..', '.'));
        sort($fileNames);
        foreach ($fileNames as $file) {
            if (substr($file, -5) === '.json') {
                $contents     = file_get_contents($path . '/' . $file);
                $files[$file] = json_decode($contents);
            }
        }
        return $files;
    }


}
