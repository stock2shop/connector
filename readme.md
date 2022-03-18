# Stock2Shop - PHP Connectors

The purpose of this repository is to allow you, the 3rd party developer, to create connector code which we can use to
synchronize data between Stock2Shop and a channel.

## Overview

A "channel" is an online shop, a marketplace or a website that has shopping cart functionality, where a business trades
products and customers place orders. A "connector" is code which makes it possible for synchronization of data between a
channel and Stock2Shop.

For more information on the aforementioned concepts and Stock2Shop, please visit the "Integrations" section on our
website at [https://www.stock2shop.com](https://www.stock2shop.com) or our
[developer documentation](https://docs.stock2shop.com).

## Data Flow

[Channel Product data](www/v1/stock2shop/vo/ChannelProduct.php) is sent in batches to your connector. The connector then
sends this data to your channel and marks each product if successful or not.

To verify the product has been updated to the channel we have methods to fetch data from your connector and your code
needs to return a [Channel Product](www/v1/stock2shop/vo/ChannelProduct.php)
with the `channel_product_code` set, if it exists on the channel.

By the use of webhooks, Orders are sent from the channel to your connector, your connector needs to transform the order
into [Stock2Shop order](www/v1/stock2shop/vo/SystemOrder.php).

Fulfillments (logistic information) is sent in batches to your connector. The connector then sends this data to your
channel, much the same as products above.

## Getting Started

Follow these set up instruction to run the application.

This setup assumes you already have an environment which is able to run PHP applications. See the section on "Submission
Guidelines" in this readme file for specific information regarding your environment.

### Create absolute path to your project.

```bash
export S2S_PATH=/your/path/for/the/project
```

### Clone Repository

```bash
git clone https://github.com/stock2shop/connector.git ${S2S_PATH}/connector
```

### Run Tests

```bash
cd $S2S_PATH/connector/tests
export=S2S_CHANNEL_NAME= && ./phpunit-4.8.phar ./
```

You should have successful results from your test.
If not, raise an issue with your error.
For more information on testing see the [test section](#tests)
The above command ran tests for all channels (note we left the S2S_CHANNEL_NAME equal to nothing)

### Creating Your Connector

The `S2S_CHANNEL_NAME` variable is the name of the channel connector you will be creating in this repository. The
variable must be a lowercase string with no spaces.

```bash
export S2S_CHANNEL_NAME=your_channel_name
```

Create boilerplate classes for your channel

```bash
cp -r $S2S_PATH/connector/www/v1/stock2shop/dal/channels/boilerplate $S2S_PATH/connector/www/v1/stock2shop/dal/channels/$S2S_CHANNEL_NAME 
```

Substitute 'boilerplate' in the namespace for the following PHP classes in the `S2S_CHANNEL_NAME` directory with your `S2S_CHANNEL_NAME`:

```bash
sed -i "s/boilerplate/${S2S_CHANNEL_NAME}/g" $S2S_PATH/connector/www/v1/stock2shop/dal/channels/$S2S_CHANNEL_NAME/Creator.php
sed -i "s/boilerplate/${S2S_CHANNEL_NAME}/g" $S2S_PATH/connector/www/v1/stock2shop/dal/channels/$S2S_CHANNEL_NAME/Products.php  
```
 
### Run rests Again

```bash
cd $S2S_PATH/connector/tests
export=S2S_CHANNEL_NAME=your_channel_name && ./phpunit-4.8.phar ./
```

You will notice that the tests for your channel now fail.
You can now start by editing the `Products.php` class with your integration and make the tests pass.

## Tests

An end-to-end test is included. Do not modify this. Tests use [phpunit](https://devdocs.io/phpunit~8/). PHAR
executables have been included for version 8.0.0 and 4.8.

A test report is printed to the command-line if you set the `S2S_TEST_DEBUG` environment variable to 'true':

```shell
export S2S_TEST_DEBUG=true && ${S2S_PATH}/phpunit-8.phar ./
```

The report gives a detailed summary of object's and which properties were synced to the channel which is useful for
identifying mistakes in your code.

### Unit Tests

Your implementation will most likely require additional methods, transforms and other helper classes such as an API
Client to access the data. Please see the example we have provided for unit testing of helper classes in
[tests/unit/www/v1/stock2shop/dal/channels/os](tests/unit/www/v1/stock2shop/dal/channels/os/HelperTest.php)
for an example of a unit test which evaluates the methods of the Helper class in the example connector that is provided.

Your unit tests must extend the [TestCase](tests/TestCase.php) class. There is no need to add tests for the methods
which are already evaluated in the E2E test (see below).

### E2E Test

This is an overall test which defines all dependencies in our system. We ask that you do not modify our e2e test -
although you may read through it to gain a better understanding of how your connector integration will fit into the
Stock2Shop system when it is completed.

The primary objective of the tests is to test whether your connector integration is working correctly. Use it whilst you
implement the Products, Orders and Fulfillments concrete classes. Please note that there is mock product, order,
fulfillment and Stock2Shop channel "metadata" in the `tests/e2e/data` directory which the test uses to simulate
synchronization of data onto our system.

Please mock the channel data and order transform (if implemented) for your target channel by adding JSON files to the
[tests/e2e/data/channels](tests/e2e/data/channels/) directory. Have a look at the example provided for guidance and the
`loadTestData()` method in the [ChannelTest](./tests/e2e/ChannelTest.php) class.

## Submission Guidelines

### General

- PHP version 7.4.
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

## Getting Help

Create an issue on Github.