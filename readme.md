# Stock2Shop - PHP Connectors

The purpose of this repository is to allow you, the 3rd party developer, to create connector code
which we can use to synchronize data between Stock2Shop and a channel.

## Overview

A channel is an online shop, a marketplace or a website that has shopping cart functionality, where
a business trades products and customers place orders.

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

An End-To-End or E2E test is included.
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
loadTestData() method in the test itself.

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

### Set Environment Variables

```bash
export S2S_PATH=/your/path/for/stock2shop
export CHANNEL_NAME=your_channel_name
```

### Clone the repo

```bash
git clone https://github.com/stock2shop/connector.git ${S2S_PATH}/connector
```

### Copy Example Channel

Creating a new connector means adding a directory to:

`${S2S_PATH}/connector/www/v1/stock2shop/dal/channels`

Once you have added this directory, the E2E tests will automatically call the appropriate methods with test data.
The following steps are

Copy the channel source files:

```bash
cp -r $S2S_PATH/connector/www/v1/stock2shop/dal/channels/example $S2S_PATH/connector/www/v1/stock2shop/dal/channels/$CHANNEL_NAME 
```

Substitute 'example' in the PHP classes in the example directory with your CHANNEL_NAME.

Replace 
```php
namespace stock2shop\dal\channels\example;
```

With your CHANNEL_NAME:
```php
namespace stock2shop\dal\channels\$CHANNEL_NAME;
```

### Copy example test data for new channel

```bash
cp -r $S2S_PATH/connector/tests/e2e/data/channels/example $S2S_PATH/connector/tests/e2e/data/channels/$CHANNEL_NAME 
```

### Run Tests For Channel

```bash
cd $S2S_PATH/connector/tests
php ./phpunit-8.phar ./
```

The tests should now run correctly for all channels and the new channel you've created.
You can now start by editing the channel Products.php file and coding your integration.

## Submission Guidelines

### PHP Version

PHP 7.4

### Coding Style

This project conforms to the PSR-12 style recommendation. 
You can read more about PHP-FIG and PSRs [here](https://www.php-fig.org/psr/psr-12/).

### Architecture
[See architecture.md](architecture.md). Please read this before writing code.

### Github

You will be assigned a branch, commit code to your branch.
Once the E2E tests pass (see testing), create a pull request and code review.

### Installation

The recommended way of setting up the repository:

```bash
export S2S_PATH=/your/path/for/stock2shop
git clone https://github.com/stock2shop/connector.git ${S2S_PATH}/connector
```

Add your custom libraries through composer.
Please check if the library has not already been added to the composer.json file and
do not update the versions of libraries included in this library.

## Frequently Asked Questions

TBC