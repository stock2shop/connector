<?php

namespace stock2shop\scripts\stock2shop;

//require_once
$loader = require __DIR__ . '/../../../../vendor/autoload.php';
$loader->add('stock2shop', __DIR__ . "/../../../../v1");

function main($className = "") {
    $voDir = __DIR__ . '/../../vo';
    $fileNames = array_diff(scandir($voDir, SCANDIR_SORT_ASCENDING), array('..', '.', 'README.md'));
    $output = new \stdClass();
    foreach ($fileNames as $fileName) {
        $vo = str_replace(".php", "", $fileName);
        if ($className === "" || $className === $vo) {
            $class = "\\stock2shop\\vo\\" . $vo;
            $output->{$vo} = new $class([]);
        }
    }
    echo json_encode($output, JSON_PRETTY_PRINT);
}

// get cli options and run
$options = getopt("", ["class:"]);
$class = (isset($options['class']))? $options['class']: "";
main($class);