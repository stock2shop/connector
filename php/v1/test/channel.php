<?php
chdir(__DIR__);

use \stock2shop\vo;
use \stock2shop\dal\channel;
use \stock2shop\dal\channels;

// Autoload using composer
$loader = require '../../vendor/autoload.php';
$loader->add('stock2shop', __DIR__ . "/../");

// check CLI input options
$options    = getopt("", ["channel_type:"]);
if (!isset($options["channel_type"])) {
    print("Runs tests against specific channel type");
    print("" . PHP_EOL);
    print("Usage:" . PHP_EOL);
    print("  php channel.php --channel_type=CHANNEL TYPE" . PHP_EOL);
    print("" . PHP_EOL);
    exit();
}

// load channel via factory
$class = "\\stock2shop\\dal\\channels\\" . $options["channel_type"] . "\\Creator";
$creator = new $class();
$channel = $creator->getChannel();

// Load test data
$json   = file_get_contents('data/syncChannelProducts.json');
$data   = json_decode($json, true);
$meta   = [
    [
        "key"   => "file.path",
        "value" => __DIR__
    ]
];
$params = new vo\SyncChannelProducts(
    [
        "meta"             => $meta,
        "channel_products" => $data,
        "flag_map"         => []
    ]
);

// sync products to channel
$syncedProducts = $channel->syncProducts($params);

// fetch products from channel
//$fetchedProducts = $connector->getProducts($params);
//$fetchedProductsByCode = $connector->getProductsByCode($params);


//// creates products
//$p1 = new vo\ChannelProduct($data);
//$syncedProducts = syncProducts(new channels\example\Creator(), [$p1], $metaItem);
//$fetchedProducts = getProducts(new channels\example\Creator(), 1, 10, $metaItem);
//$fetchedProductsByCode = getProductsByCode(new channels\example\Creator(), [$p1], $metaItem);

if (count($syncedProducts->channel_products) !== 2) {
    throw new \Exception('failed to create');
} else {
    print '2 products synced with channel' . PHP_EOL;
}
if (!$syncedProducts->channel_products[0]->success) {
    throw new \Exception('failed to set success flag');
} else {
    print 'Success flags set' . PHP_EOL;
}

if ($syncedProducts->channel_products[0]->channel_product_code === "") {
    throw new \Exception('failed to create');
}
if ($syncedProducts->channel_products[0]->synced === "") {
    throw new \Exception('failed to create');
}
//if(count($fetchedProducts) !== 1) {
//    throw new \Exception('failed to fetch');
//}
//if(count($fetchedProductsByCode) !== 1) {
//    throw new \Exception('failed to fetch by code');
//}

//// delete products
$params = new vo\SyncChannelProducts(
    [
        "meta"             => $meta,
        "channel_products" => $data,
        "flag_map"         => []
    ]
);
foreach ($params->channel_products as $product) {
    $product->delete = true;
}
$syncedProducts = $channel->syncProducts($params);
if (count($syncedProducts->channel_products) !== 2) {
    throw new \Exception('failed to delete');
}
foreach ($params->channel_products as $product) {
    if (!$product->success) {
        throw new \Exception('failed to create');
    }
}