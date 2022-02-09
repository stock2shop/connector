# Architecture

This document describes the high-level architecture of the connector code base.

## Project Structure

### /tests

We have included E2E (end-to-end) tests directory.
You do not have to write your own tests, the E2E tests should cover our requirements.

There are certain tests that may require you to add data.
For example, an order webhook, which would require example data sent to our connector.
This can be added to the `tests/data/` directory.

Please refer to the [tests readme](./tests/README.md) for more information.

### /www/vendor

Ensure you commit any libraries added.
Do not change the version of existing libraries.
Check first if there is a library to perform the function
you want before loading a new library.

Install Composer globally on your system:

```bash
brew install composer # macOS / Linux
choco install composer # Windows
```

If you need to add new libraries, ensure these are added 
by composer and that you have not committed the vendor files to the repository.
It is sufficient to commit the `composer.json` file.

Examples:

```bash
composer require automattic/woocommerce
composer install
```

### /www/v1/stock2shop/vo

VO Stands for [Value Object](https://martinfowler.com/bliki/ValueObject.html).
The purpose of the Value Object classes is to define the data we pass around.

You must not edit anything in this directory. The classes in the `stock2shop\vo` namespace are 
our Domain Model and you are required to transform the data coming into your connector implementation 
as required to match our specifications.

Use the following helper class to generate JSON documents for each Value Object:

Run the following from your command line:

```bash
php ${S2S_PATH}/connector/www/v1/stock2shop/scripts/stock2shop/voJson.php
```

If you want to view a specific VO, use this.

```bash
php ${S2S_PATH}/connector/www/v1/stock2shop/scripts/stock2shop/voJson.php --class=Variant
```

This is useful for quickly viewing classes which may extend multiple parents.
For more information read the [README.md](www/v1/stock2shop/vo/README.md) file in the `/www/v1/stock2shop/vo` directory.

### /www/v1/stock2shop/dal

DAL stands for "Data Access Layer".
The purpose of this directory is to separate logic related 
to different systems.

The `channel` directory contains the interfaces which describe our system and must not be modified.

### /www/v1/stock2shop/dal/channels

A channel is usually a e-commerce shopping cart or a market place.
Think of channel as in "sales channel".
If you have been commissioned to integrate code into a specific
shopping cart, then you will create a directory here.

Each channel must have these classes:-

`Creator.php` is used to create a new instance of the Factory class which dynamically instantiates the required channel type from the following three options:

1. Products
2. Orders
3. Fulfillments

As a starting point, you may copy the Creator.php file found in `example` and adjust it accordingly. 
If the service you are coding the connector integration for does not require any of the aforementioned channel types, then you must throw 
a custom exception from our exceptions namespace.

For example, if your shopping cart does not need to sync fulfillment data (i.e. shipping information/logistics) then add the following to your Creator.php file in the `channels/[yourconnector]` directory: 

```php
public function createFulfillments(): channel\Fulfillments
{
    throw new exceptions\NotImplemented();
}
```

`connector.php` implements the `channel/Connector.php` interface.
It must therefore include all methods and have the same method signatures.

## How To Use Value Objects

Refer to the [readme file](./www/v1/stock2shop/vo/README.md) in the `www/v1/stock2shop/vo` directory for specific use of
each of the Value Object classes.