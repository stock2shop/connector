# Stock2Shop - PHP Connectors

The purpose of this repository is to allow you, the 3rd party developer, to create connector code
which we can use to synchronize data between Stock2Shop and a channel.

## Overview

A "channel" is an online shop, a marketplace or a website that has shopping cart functionality, where
a business trades products and customers place orders. A "connector" is code which makes it possible
for synchronization of data between a channel and Stock2Shop.

For more information on the aforementioned concepts and Stock2Shop, please visit the "Integrations" section 
on our website at [https://www.stock2shop.com](https://www.stock2shop.com) or our 
[developer documentation](https://docs.stock2shop.com).

### Data Flow

[Channel Product data](www/v1/stock2shop/vo/ChannelProduct.php) is sent in batches to your
connector. The connector then sends this data to your channel and marks each product if successful or not.

To verify the product has been updated to the channel we have methods to fetch data from your connector
and your code needs to return a [Channel Product](www/v1/stock2shop/vo/ChannelProduct.php) 
with the `channel_product_code` set, if it exists on the channel.

By the use of webhooks, Orders are sent from the channel to your connector, your connector needs to transform 
the order into [Stock2Shop order](www/v1/stock2shop/vo/SystemOrder.php).

Fulfillments (logistic information) is sent in batches to your connector.
The connector then sends this data to your channel, much the same as products above.

### Tests

An end-to-end test is included. Do not modify this.
Tests use [phpunit](https://devdocs.io/phpunit~8/) version 8.

#### Unit Tests

Your implementation will most likely require additional methods, transforms and other helper classes such as an API
Client to access the data. Please see the example we have provided for unit testing of helper classes in
[tests/unit/www/v1/stock2shop/dal/channels/os](tests/unit/www/v1/stock2shop/dal/channels/os/HelperTest.php)
for an example of a unit test which evaluates the methods of the Helper class in the example connector that is
provided.

Your unit tests must extend the [TestCase](tests/TestCase.php) class.
There is no need to add tests for the methods which are already evaluated in the E2E test (see below).

#### E2E Test

This is an overall test which defines all dependencies in our system. We ask that you do not modify our e2e test -
although you may read through it to gain a better understanding of how your connector integration
will fit into the Stock2Shop system when it is completed.

The primary objective of the tests is to test whether your connector integration is working correctly.
Use it whilst you implement the Products, Orders and Fulfillments concrete classes. Please note that there is mock
product, order, fulfillment and Stock2Shop channel "metadata" in the `tests/e2e/data` directory which the test uses to
simulate synchronization of data onto our system.

Please mock the channel data and order transform (if implemented) for your target channel by adding JSON files to the
[tests/e2e/data/channels](tests/e2e/data/channels/) directory. Have a look at the example provided for guidance and the
`loadTestData()` method in the [ChannelTest](./tests/e2e/ChannelTest.php) class.

### Folder Structure

The following serves to give you an overview of the folder structure for the connector you will be adding to the
[channels directory](www/v1/stock2shop/dal/channels/). The example below is the structure of the 'os' directory:

    .
    ├── data                    # The connector writes and reads data from this directory. 
    │   │                       # NB: This is a flat-file connector.
    │   ├── products            # products storage
    │   │   ..
    │   ├── orders              # orders storage
    │   │   ..
    │   ├── fulfillments        # fulfillments storage
    │   │   ..
    │   ├── Helper.php          # Provides methods which are used to access the data from disk.
    │
    ├── Creator.php             # Concrete factory class of the abstract Creator class.
    ├── Products.php            # Products channel type class.
    ├── Orders.php              # Orders channel type class.
    ├── Fulfillments.php        # Fulfillments channel type class.
    └── README                  # An overview of the implementation and any notes.

The folder structure above is for a channel connector which implements all of the interfaces in [dal\channel](www/v1/stock2shop/dal/channel).
Your code might not implement all of these, but you may refer to the code in the [os](www/v1/stock2shop/dal/channels/os) directory
for an example.

The [example](www/v1/stock2shop/dal/channels/example) folder structure omits the data directory and the Orders and Fulfillments
classes. Please refer to the [readme](www/v1/stock2shop/dal/channels/example/readme.md) in the example folder for more
information.

## Creating A Channel Connector

This setup assumes you already have an environment which is able to run PHP applications.
See the section on "Submission Guidelines" in this readme file for specific information regarding your 
environment.

1. Set Environment Variables

```bash
export S2S_PATH=/your/path/for/stock2shop
export CHANNEL_NAME=your_channel_name
```

The S2S_PATH variable must be the absolute path to the directory where this repository is located. 
For example:

On Mac OSX:
/Users/yourUsername/stock2shop
On Ubuntu: 
/home/yourUsername/stock2shop

The `CHANNEL_NAME` variable must be a lowercase string with no spaces.

2. Clone this repository.

```bash
git clone https://github.com/stock2shop/connector.git ${S2S_PATH}/connector
```

3. Copy Example Channel

Copy the channel source files:

```bash
cp -r $S2S_PATH/connector/www/v1/stock2shop/dal/channels/example $S2S_PATH/connector/www/v1/stock2shop/dal/channels/$CHANNEL_NAME 
```

4. Substitute Namespaces. 

Substitute 'example' in the PHP classes in the directory with your `CHANNEL_NAME`.

Replace 
```php
namespace stock2shop\dal\channels\example;
```

With your CHANNEL_NAME:
```php
namespace stock2shop\dal\channels\$CHANNEL_NAME;
```

5. Copy Sample Channel Data.

You will most likely want to make your connector configurable per channel. 
How you do this is up to the constraints of the system you are coding the integration for.

To make your channel connector code as extensible as possible, start by copying the 
[channelData.json](tests/e2e/data/channels/os/channelData.json) file in the [tests/e2e/data/channels/os](./tests/e2e/data/channels/os)
directory into a new directory in the [tests/e2e/data/channels](./tests/e2e/data/channels) directory. 
You must name this directory the same as your `CHANNEL_NAME` variable. 

Your implementation in `www/v1/stock2shop/dal/channels/${CHANNEL_NAME}/Products` may make use of the sample meta data
configured in the `channelData.json`. Please refer to the `os` [connector](./www/v1/stock2shop/dal/channels/os) for an 
example of using channel meta data for storage path separators. 

If your channel uses the HTTP protocol, then you might want to use channel meta data to configure your connector with 
your channel's API credentials (secret, client_id), endpoint URLs, etc.

7. Run Tests for your `CHANNEL_NAME`.

```bash
cd $S2S_PATH/connector/tests
php ./phpunit-8.phar ./
```

The tests should now run correctly for all channels and the new channel you've created.
You can now start by editing the `Products.php` file integration.

## Submission Guidelines

### General

- PHP version 7.4.
- PHPUnit 8.0.0.
- This project conforms to the PSR-12 style recommendation. 
- You can read more about it [here](https://www.php-fig.org/psr/psr-12/).
- Please read the [architecture.md](./architecture.md) file before starting.

### Github

- You will be assigned a branch.
- Commit code to your branch.
- Make sure your code passes the E2E test.
- Add detailed notes on your implementation.
- Any relevant configuration instructions must be included in the readme in your `CHANNEL_NAME` directory.
- When you are ready, create a pull request and request a code review.

### 3rd Party Libraries

- Add your custom libraries through composer.
- Please check if the library has not already been added to the composer.json file.
- Do not update the versions of libraries included in this repository. 
- We have included Guzzle for HTTP requests which is locked at version 6.2.3.

## Frequently Asked Questions

TBC