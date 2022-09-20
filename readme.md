# Stock2Shop - PHP Connectors

Example Channel Connector

## Overview

A "channel" is an online shop, a marketplace or a website that has shopping cart functionality, where a business trades
products and customers place orders. A "connector" is code which makes it possible for synchronization of data between a
channel and Stock2Shop.

For more information please visit the "Integrations" section on our
website at [https://www.stock2shop.com](https://www.stock2shop.com) or our
[developer documentation](https://docs.stock2shop.com).

This channel connector writes data to the OS disk and is used as an example.

## Data Flow

Data is passed between applications using [DTOs](https://github.com/stock2shop/share).

The interface to send [ChannelProducts](https://github.com/stock2shop/share/blob/master/src/DTO/ChannelProducts.php) to 
a [Channel](https://github.com/stock2shop/share/blob/master/src/DTO/Channel.php) can be found 
[here](https://github.com/stock2shop/share/blob/master/src/Channel/ChannelProductsInterface.php).

For our system to verify that the products exist on the channel, there are two methods available,
[get()](https://github.com/stock2shop/share/blob/2ec36d6d4d60cff9ddea9df73786cfedef323fab/src/Channel/ChannelProductsInterface.php#L104) 
and [getByCode](https://github.com/stock2shop/share/blob/2ec36d6d4d60cff9ddea9df73786cfedef323fab/src/Channel/ChannelProductsInterface.php#L75) 

[Channel Products](https://github.com/stock2shop/share/blob/master/src/DTO/ChannelProducts.php) are sent in batches 
to the channel and the connector updates the channel as efficiently as possible.

## setup

```
git clone https://github.com/stock2shop/share.git
cd share
composer install
```

## running Tests
```
./vendor/bin/phpunit
```

## General

- PHP version 8.1
- use strict types `declare(strict_types=1);`