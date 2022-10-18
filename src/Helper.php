<?php

declare(strict_types=1);

namespace Stock2Shop\Connector;

use Stock2Shop\Share;

class Helper
{
    public const DATA_DIR = __DIR__ . '/data';

    public static function setDataDir()
    {
        if (!is_dir(self::DATA_DIR)) {
            mkdir(self::DATA_DIR);
        }
    }

    /**
     * Path for storing product
     */
    public static function getProductPath(Share\DTO\ChannelProduct $product): string
    {
        return sprintf('%s/%s.json', self::DATA_DIR, $product->id);
    }

    /**
     * Retrieves all .json files from data dir and parses them.
     */
    public static function getJSONFiles(): array
    {
        $files     = [];
        $fileNames = array_diff(scandir(self::DATA_DIR, SCANDIR_SORT_ASCENDING), array('..', '.'));
        sort($fileNames);
        foreach ($fileNames as $file) {
            if (str_ends_with($file, '.json')) {
                $contents     = file_get_contents(self::DATA_DIR . '/' . $file);
                $files[$file] = json_decode($contents, true);
            }
        }
        return $files;
    }
}
