<?php



use \stock2shop\dal\channels;
use \stock2shop\vo;



class TestUtils {

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
                "key"   => "separator",
                "value" => '~'
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
        $this->printHead("Sync Products");
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
            $this->printPad("product->channel_product_code", $product->channel_product_code);
            $this->printPad("product->success", $product->success);
            $this->printPad("product->synced", $product->synced);
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
                $this->printPad("product->variants[]->channel_variant_code", $variant->channel_variant_code);
                $this->printPad("product->variants[]->success", $variant->success);
            }
        }
    }

    function verifyGetProductsByCodeResponse($channelProducts, $fetchedProducts) {
        $this->printHead("Get Products By Code");
        if (count($channelProducts->channel_products) !== count($fetchedProducts->channel_products)) {
            throw new \Exception('failed to fetch');
        }

        /** @var vo\ChannelProduct $product */
        foreach ($fetchedProducts->channel_products as $key => $product) {
            if (!$product->channel_product_code || $product->channel_product_code == "") {
                throw new \Exception('failed to set product->channel_product_code');
            }
            $this->printPad("product->channel_product_code", $product->channel_product_code);
            if(count($channelProducts->channel_products[$key]->variants) !== (count($product->variants))) {
                throw new \Exception('incorrect variants');
            }
            foreach ($product->variants as $variant) {
                if (!$variant->channel_variant_code || $variant->channel_variant_code == "") {
                    throw new \Exception('failed to set variant->channel_variant_code');
                }
                $this->printPad("product->variants[]->channel_variant_code", $variant->channel_variant_code);
            }
        }
    }

    function verifyGetProductsResponse($fetchedProducts, $token, $limit) {
        $this->printHead("Get Products");
        if(count($fetchedProducts) > $limit) {
            throw new \Exception('too many products returned');
        }

        /** @var vo\ChannelProductGet $product */
        foreach ($fetchedProducts as $key => $product) {
            if(strcmp($token, $product->token) >= 0) {
                throw new \Exception('invalid token');
            }
            if(!$product instanceof vo\ChannelProduct ) {
                throw new \Exception('invalid ChannelProductGet returned');
            }
            if (!$product->channel_product_code || $product->channel_product_code == "") {
                throw new \Exception('failed to set product->channel_product_code');
            }
            $this->printPad("product->channel_product_code", $product->channel_product_code);
            if (!$product->token || $product->token == "") {
                throw new \Exception('failed to set product->token');
            }
            $this->printPad("product->token", $product->token);
            foreach ($product->variants as $variant) {
                if (!$variant->channel_variant_code || $variant->channel_variant_code == "") {
                    throw new \Exception('failed to set variant->channel_variant_code');
                }
                $this->printPad("product->variants[]->channel_variant_code", $variant->channel_variant_code);
            }
        }
    }

    function printPad($key, $value) {
        print str_pad($key, 50) . ' = ' . $value . PHP_EOL;
    }

    function printHead($heading) {
        print PHP_EOL;
        print str_pad("", 100, "-") . PHP_EOL;
        print str_pad($heading, 100, "-", STR_PAD_BOTH) . PHP_EOL;
        print str_pad("", 100, "-") . PHP_EOL;
        print PHP_EOL;
    }
}