# Stock2Shop - Demo Channel Connector

## Overview

A "Channel" is an online shop, a marketplace or a website that has shopping cart functionality.
e.g. Shopify, Magento or WooCommerce.

A "Connector" makes it possible to sync data between a Channel and Stock2Shop.

This repository has a Demo API which can be run locally.
It mimics how a "real" Channel might behave.

## Setup

To set up the Demo API and tests, run the below in sequence.  

```
git clone https://github.com/stock2shop/connector.git
cd connector
composer install
```

Set your environment.

```
cp env.sample .env
```

Edit the `.env` accordingly. 

Start the Demo API locally.
The API uses the file system to store information, it has no database.

> Binaries for Mac, linux and windows have been included in demo_store directory
> The demo API is built in Go, instructions to build are below but are not required 
> to run the project.

```
./demo_store/bin/mac /path/to/your/data/dir
```

Run your tests

```
vendor/bin/phpunit
```

Your tests should pass.
You can view the products saved on the Demo API by looking in the data dir.
You can also view the logs depending on where you configured this.

## Data Flow

Data is passed between applications using Data Transfer Objects [DTOs](https://github.com/stock2shop/share).

The interface to send [ChannelProducts](https://github.com/stock2shop/share/blob/master/src/DTO/ChannelProducts.php) to
a [Channel](https://github.com/stock2shop/share/blob/master/src/DTO/Channel.php) can be found
[here](https://github.com/stock2shop/share/blob/master/src/Channel/ChannelProductsInterface.php).

For our system to verify that the products exist on the channel, there are two methods available,
[get](https://github.com/stock2shop/share/blob/2ec36d6d4d60cff9ddea9df73786cfedef323fab/src/Channel/ChannelProductsInterface.php#L104)
and [getByCode](https://github.com/stock2shop/share/blob/2ec36d6d4d60cff9ddea9df73786cfedef323fab/src/Channel/ChannelProductsInterface.php#L75)

[Channel Products](https://github.com/stock2shop/share/blob/master/src/DTO/ChannelProducts.php) are sent in batches
to the channel and the connector updates the channel as efficiently as possible.

## General

- PHP version 8.1
- Use strict types `declare(strict_types=1);`
- Use PSR12 standards
- Setup php-cs-fixer in your IDE to enforce coding standards

***
## Demo API Build

Using go modules for dependencies, the following commands should be run in the `connector/demo_store` directory.

List all modules
```bash
go list -m all
```

Add missing and remove unused modules
```bash
go mod tidy
```

Copy dependencies to vendor dir
```bash
go mod vendor
```

## Run

In the `demo_store` first build the program 
```bash
    go build -o build/<EXECUTIBLE_NAME>
```
