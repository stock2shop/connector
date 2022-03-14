# Architecture

This document describes the high-level architecture of the connector code base.

## Project Structure

### /tests

This folder contains the source code for the [TestPrinter](tests/TestPrinter.php) class. There is
a [TestStreamFilter](tests/TestStreamFilter.php) class which is used by the printer to write the data to the
command-line when the e2e test is run.

The directory structure in [tests/www](tests/www) mirrors the main application to make absolute paths and autoloading
consistent.

### /tests/e2e

We have included E2E (end-to-end) tests directory. You do not have to write your own tests, the E2E tests should cover
our requirements.

#### /tests/e2e/data/channels

This folder contains default channel configuration data and meta data. Sample data used to populate Stock2Shop VO's is
included in JSON files.

#### /tests/e2e/data/channels/${CHANNEL_NAME}

There are certain tests that may require you to add data.

For example, an order webhook, which would require that you mock the webhook for local testing during development. Add
this to the [tests/e2e/data/channels](tests/e2e/data/channels/) directory in a file called `orderTransform.json`. If a
directory does not exist for your connector on this path then feel free to create one.

Please refer to the [tests readme](./tests/README.md) for more information.

#### /tests/www/v1/stock2shop/dal/channels/os

This folder contains a single unit test [ProductsTest](tests/www/v1/stock2shop/dal/channels/os/ProductsTest.php)
which illustrates the recommended coding convention for writing a unit test for a method. Please use the example as the
starting point for your own unit tests.

It is not necessary to write tests for any of the functions which are already evaluated in
the [ChannelTest](tests/e2e/ChannelTest.php) class.

### /www/vendor

Ensure you commit any libraries added. Do not change the version of existing libraries. Check first if there is a
library to perform the function you want before loading a new library.

Install Composer globally on your system:

```bash
brew install composer # macOS / Linux
choco install composer # Windows
```

### /www/v1/stock2shop/vo

VO Stands for [Value Object](https://martinfowler.com/bliki/ValueObject.html). The purpose of the Value Object classes
is to define the data we pass around.

The data classes in VO are not true Value Objects in the sense that you can modify the properties on these classes.

You must not edit anything in this directory. The classes in the [stock2shop\vo](www/v1/stock2shop/vo) are our Domain
Model.

### /www/v1/stock2shop/dal

DAL stands for "Data Access Layer". The purpose of this directory is to separate logic related to different systems. We
use the DAL to standardize the way we synchronize data between channels and Stock2shop.

### /www/v1/stock2shop/dal/channel

The `channel` directory contains the [Creator](www/v1/stock2shop/dal/channel/Creator.php) factory method class and the
contracts for Products, Orders and Fulfillments connector classes.

### /www/v1/stock2shop/dal/channels

A channel is usually an e-commerce shopping cart or a market-place. Think of channel as in "sales channel". If you have
been commissioned to integrate code into a specific shopping cart, then you will create a directory here.

Each channel must have these classes:-

`Creator.php` is used to create a new instance of the Factory class which dynamically instantiates the required channel
type from the following three channel types:

1. Products
2. Orders
3. Fulfillments

As a starting point, you may copy the Creator.php file found in `example` and adjust it accordingly. If the service you
are coding the connector integration for does not require any of the aforementioned channel types, then you must throw a
custom exception from our exceptions' namespace.

For example, if your shopping cart does not need to sync fulfillment data (i.e. shipping information/logistics) then add
the following to your Creator.php file in the `channels/[yourconnector]` directory:

```php
public function createFulfillments(): channel\Fulfillments
{
    throw new exceptions\NotImplemented();
}
```

### /www/v1/stock2shop/dal/channels/example

The 'example' directory contains an example of a channel connector. It is the bare minimum or boilerplate for starting a
connector project.

### /www/v1/stock2shop/dal/channels/os

The 'os' directory contains the code for a channel which is served from the local file system.

In a real-world scenario code in this connector would most likely save products, variants and images to the channel over
a REST-like HTTP interface.

### /www/v1/stock2shop/dal/channels/os/data

This connector writes to your local file system in the [data directory](www/v1/stock2shop/dal/channels/os/data).