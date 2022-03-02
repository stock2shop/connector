# Stock2Shop - PHP Connectors

Stock2Shop connectors connect ERP accounting systems with sales channels, 
such as ecommerce shopping carts and market-places.

The purpose of this repository is to allow you, the 3rd party developer, to create 
sales channels we can use.

## Data flow for a sales channel connector

[Channel Product data](www/v1/stock2shop/vo/ChannelProduct.php) is sent in batches to your
connector. The connector then sends this data to your channel and marks each product if successfully or not.

To verify the product has been updated to the channel we have methods to fetch data from your connector
and your code needs to return a [Channel Product](www/v1/stock2shop/vo/ChannelProduct.php) 
with the `channel_product_code` set, if it exists on the channel.

Orders are sent from the channel to your connector, your connector needs to transform
the order into a [Stock2Shop order](www/v1/stock2shop/vo/Order.php).

Fulfillments (logistic information) is sent in batches to your connector.
The connector then sends this data to your channel, much the same as products above.

## Setup

This setup assumes you already have an environment created to run PHP.

### Set Environment Variables

```bash
export S2S_PATH=/your/path/for/stock2shop
export CHANNEL_NAME=your_channel_name
```

### Clone the repo

```bash
git clone https://github.com/stock2shop/connector.git ${S2S_PATH}/connector
```

### Copy example channel

```bash
cp -r $S2S_PATH/connector/www/v1/dal/channels/example $S2S_PATH/connector/www/v1/dal/channels/$CHANNEL_NAME 
```

### Run tests for channel

```bash
cd $S2S_PATH/tests
php ./phpunit-4.8.phar ./
```

## Submission Guidelines

### PHP Version

PHP 7.3

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
do not update the versions of libraries included in this libary.

### New Connector Setup

Creating a new connector means adding a directory to:

`${S2S_PATH}/connector/www/v1/stock2shop/dal/channels`

Once you have added this directory, the E2E tests will automatically
call the appropriate methods with test data.

See [architecture](architecture.md) about the channel interface.

## Tests

An End-To-End or E2E test is included. End-to-end testing is a technique which is used to test software from
beginning to end.

### Unit Tests

Your implementation will most likely require additional methods, transforms and other helper classes such as an API 
Client to access the data. Please see the example we have provided for unit testing of helper classes in 
[tests/unit/www/v1/stock2shop/dal/channels/example](tests/unit/www/v1/stock2shop/dal/channels/example/HelperTest.php) 
for an example of a unit test which evaluates the methods of the Helper class in the example connector that is 
provided.

Your unit tests must extend the [TestCase](tests/TestCase.php) class.
There is no need to add tests for the methods which are already evaluated in the E2E test (see below).

### E2E Test

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

### Run Tests

Tests use the [phpunit](https://phpunit.readthedocs.io/en/9.5/installation.html).
Ensure your tests are working with:

```bash
cd ${S2S_PATH}/connector/tests
./phpunit-4.8.phar ./
```

Throughout this repository we have tried our best to make writing and testing your integration as easy and fimiliar
for you as possible. The e2e test uses PhpUnit version 4.8. The .phar executable has been included for you to run
the test on your local environment.

The test output will be displayed in your console.
If there are errors - it indicates an issue with your connector integration.

## Folder Structure

The following serves to give you an overview of the folder structure for the connector you will be adding to the 
[channels directory](www/v1/stock2shop/dal/channels/). The example below is the structure of the 'example' directory:

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
    ├── LICENSE                 # Any license information which may be relevant to the code.
    └── README                  # An overview of the implementation and any notes.

## Frequently Asked Questions

TBC