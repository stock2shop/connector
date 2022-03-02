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

### /www/v1/stock2shop/vo

VO Stands for [Value Object](https://martinfowler.com/bliki/ValueObject.html).
The purpose of the Value Object classes is to define the data we pass around.

The data classes in VO are not true Value Objects in the sense that you 
can modify the properties on these classes.

You must not edit anything in this directory. The classes in the [stock2shop\vo](www/v1/stock2shop/vo) are 
our Domain Model.

### /www/v1/stock2shop/dal

DAL stands for "Data Access Layer". The purpose of this directory is to separate logic related to different systems.

### /www/v1/stock2shop/dal/channel

The `channel` directory contains our factory classes for channel creation.

### /www/v1/stock2shop/dal/channels

A channel is usually an e-commerce shopping cart or a market-place.
Think of channel as in "sales channel".
If you have been commissioned to integrate code into a specific
shopping cart, then you will create a directory here.

Each channel must have these classes:-

`Creator.php` is used to create a new instance of the Factory class which dynamically instantiates the required channel 
type from the following three channel types:

1. Products
2. Orders
3. Fulfillments

As a starting point, you may copy the Creator.php file found in `example` and adjust it accordingly. 
If the service you are coding the connector integration for does not require any of the aforementioned channel types, 
then you must throw a custom exception from our exceptions' namespace.

For example, if your shopping cart does not need to sync fulfillment data (i.e. shipping information/logistics) then 
add the following to your Creator.php file in the `channels/[yourconnector]` directory: 

```php
public function createFulfillments(): channel\Fulfillments
{
    throw new exceptions\NotImplemented();
}
```

### /www/v1/stock2shop/dal/channels/example

The 'example' directory contains an example of a channel connector.
It is the bare minimum or boilerplate for starting a connector project.

### /www/v1/stock2shop/dal/channels/os

The 'os' directory contains the code for a channel which is served from the local file system.

In a real-world scenario code in this connector would most likely save products, variants and 
images to the channel over a REST-like HTTP interface.

### /www/v1/stock2shop/dal/channels/os/data

This connector writes to your local file system in the [data directory](www/v1/stock2shop/dal/channels/os/data).