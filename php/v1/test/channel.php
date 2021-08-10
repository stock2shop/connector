<?php
chdir(__DIR__);

use \stock2shop\vo;
use \stock2shop\dal\channels;

// Autoload using composer
$loader = require '../../vendor/autoload.php';
$loader->add('stock2shop', __DIR__ . "/../");

class Test {

    function getChannel($type) {
        $class   = "\\stock2shop\\dal\\channels\\" . $type . "\\Creator";
        $creator = new $class();
        return $creator->getChannel();
    }

    function loadChannelProducts() {
        $json   = file_get_contents('data/syncChannelProducts.json');
        $data   = json_decode($json, true);
        $meta   = [
            [
                "key"   => "file.path",
                "value" => __DIR__
            ]
        ];
        return new vo\SyncChannelProducts(
            [
                "meta"             => $meta,
                "channel_products" => $data,
                "flag_map"         => []
            ]
        );
    }

    function verifySyncProductsResponse($channelProducts, $syncedProducts) {
        print PHP_EOL;
        if (count($channelProducts->channel_products) !== count($syncedProducts->channel_products)) {
            throw new \Exception('failed to create');
        }

        /** @var vo\ChannelProduct $product */
        foreach ($syncedProducts->channel_products as $key => $product) {
            if (!$product->success) {
                throw new \Exception('failed to set product->success');
            }
            if (!$product->synced) {
                throw new \Exception('failed to set product->synced');
            }
            if (!vo\ChannelProduct::isValidSynced($product->synced)) {
                throw new \Exception('Invalid product->synced date');
            }
            if (!$product->channel_product_code || $product->channel_product_code == "") {
                throw new \Exception('failed to set product->channel_product_code');
            }
            print '-- product->channel_product_code ' . $product->channel_product_code  .PHP_EOL;
            print '-- product->success ' . $product->success  .PHP_EOL;
            print '-- product->synced ' . $product->synced  .PHP_EOL;
            if(count($channelProducts->channel_products[$key]->variants) !== (count($product->variants))) {
                throw new \Exception('incorrect variants');
            }
            foreach ($product->variants as $variant) {
                if (!$variant->success) {
                    throw new \Exception('failed to set variant->success');
                }
                if (!$variant->channel_variant_code || $variant->channel_variant_code == "") {
                    throw new \Exception('failed to set variant->channel_variant_code');
                }
                print '---- variant->channel_variant_code ' . $variant->channel_variant_code  .PHP_EOL;
                print '---- variant->success ' . $variant->success  .PHP_EOL;
            }
        }
        print PHP_EOL . '--------------------------------------- '. PHP_EOL;
    }

    function verifyGetProductsByCodeResponse($channelProducts, $fetchedProducts) {
        print PHP_EOL;
        if (count($channelProducts->channel_products) !== count($fetchedProducts->channel_products)) {
            throw new \Exception('failed to fetch');
        }

        /** @var vo\ChannelProduct $product */
        foreach ($fetchedProducts->channel_products as $key => $product) {
            if (!$product->channel_product_code || $product->channel_product_code == "") {
                throw new \Exception('failed to set product->channel_product_code');
            }
            print '-- product->channel_product_code ' . $product->channel_product_code  .PHP_EOL;
            if(count($channelProducts->channel_products[$key]->variants) !== (count($product->variants))) {
                throw new \Exception('incorrect variants');
            }
            foreach ($product->variants as $variant) {
                if (!$variant->channel_variant_code || $variant->channel_variant_code == "") {
                    throw new \Exception('failed to set variant->channel_variant_code');
                }
                print '---- variant->channel_variant_code ' . $variant->channel_variant_code  .PHP_EOL;
            }
        }
        print PHP_EOL . '--------------------------------------- '. PHP_EOL;
    }
}

// Run tests
// Check CLI input options
$options = getopt("", ["channel_type:"]);
if (!isset($options["channel_type"])) {
    print("Runs tests against specific channel type");
    print("" . PHP_EOL);
    print("Usage:" . PHP_EOL);
    print("  php channel.php --channel_type=CHANNEL TYPE" . PHP_EOL);
    print("" . PHP_EOL);
    exit();
}

// Run tests
$test = new Test();
$channel = $test->getChannel('example');

// SyncProducts to channel
$channelProducts = $test->loadChannelProducts();
$syncedProducts = $channel->syncProducts($channelProducts);
$test->verifySyncProductsResponse($channelProducts, $syncedProducts);

// fetch products
$channelProducts = $test->loadChannelProducts();
$fetchedProducts = $channel->getProductsByCode($channelProducts);
$test->verifyGetProductsByCodeResponse($channelProducts, $fetchedProducts);

// Change variant sku's to ensure old variants are removed and new ones created.
$channelProducts = $test->loadChannelProducts();
foreach ($channelProducts->channel_products as $product) {
    foreach ($product->variants as $variant) {
        $variant->sku .= "-1";
    }
}
$syncedProducts = $channel->syncProducts($channelProducts);
$test->verifySyncProductsResponse($channelProducts, $syncedProducts);

// Delete test products
$channelProducts = $test->loadChannelProducts();
foreach ($channelProducts->channel_products as $product) {
    $product->delete = true;
}
$syncedProducts = $channel->syncProducts($channelProducts);
$test->verifySyncProductsResponse($channelProducts, $syncedProducts);