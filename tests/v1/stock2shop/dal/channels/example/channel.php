<?php
chdir(__DIR__);
$loader = require '../../vendor/autoload.php';
$loader->add('stock2shop', __DIR__ . "/../");
require_once "TestUtils.php";

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

// Get channel instance
$test    = new TestUtils();
$channel = $test->getChannel('example');

// Sync products to channel
$channelProducts = $test->loadChannelProducts();
$syncedProducts  = $channel->syncProducts($channelProducts);
$test->verifySyncProductsResponse($channelProducts, $syncedProducts);

// Get products by codes
$channelProducts = $test->loadChannelProducts();
$allProducts     = $channel->getProductsByCode($channelProducts);
$test->verifyGetProductsByCodeResponse($channelProducts, $allProducts);

// Get products
$channelProducts = $test->loadChannelProducts();
$fetchedProducts = $channel->getProducts("", count($allProducts->channel_products), $channelProducts->meta);
$test->verifyGetProductsResponse($fetchedProducts, "", count($allProducts->channel_products));
if (count($fetchedProducts) !== count($allProducts->channel_products)) {
    throw new \Exception('products not fetched');
}

// Get products using paging (one at a time)
$token           = "";
$cnt             = 0;
$fetchedProducts = [];
for ($i = 0; $i < count($allProducts->channel_products); $i++) {
    $products = $channel->getProducts($token, 1, $channelProducts->meta);
    $test->verifyGetProductsResponse($products, $token, 1);
    $fetchedProducts[] = $products[0];
    $cnt               += count($products);
    $token             = $products[0]->token;
}
if ($cnt !== count($allProducts->channel_products)) {
    throw new \Exception('product paging incorrect');
}

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
$fetchedProducts = $channel->getProducts("", count($channelProducts->channel_products), $channelProducts->meta);
if (count($fetchedProducts) !== 0) {
    throw new \Exception('products not removed');
}


// Run order transform
$webhook = $test->loadOrder();
$order   = $channel->transformOrder((object)$webhook, $test->loadMeta());
$test->verifyTransformOrderResponse($order);