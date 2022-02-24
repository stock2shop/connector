# Stock2Shop - PHP Connectors

Stock2Shop connectors connect ERP accounting systems with sales channels, 
such as ecommerce shopping carts, market-places and our B2B trade store.

The purpose of this repository is to allow 3rd party developers to contribute 
connectors to our ecosystem.

This repository is a bare bones interface to guide you on what we require.
It includes an example channel connector, to illustrate data structures 
and our workflow.  

## Submission Guidelines

### PHP Version

We support PHP 7.1 at this stage.
You can read more about the features and changes that came with this version 
in the [PHP Manual](https://www.php.net/manual/en/migration71.new-features.php)

### Coding Style

This project conforms to the PSR-12 style recommendation. 
You can read more about PHP-FIG and PSRs [here](https://www.php-fig.org/psr/psr-12/).

If you use PhpStorm, you can configure your IDE to analyze your code and assist you in conforming to PSR-12.

### Setup

The recommended way of setting up the repository:

```bash
export S2S_PATH=/your/path/for/stock2shop
git clone https://github.com/stock2shop/connector.git ${S2S_PATH}/connector
```

Add your custom libraries through composer.
Please check if the library has not already been added to the composer.json file and
do not update the versions of libraries included in this libary.

### Github

You will be assigned a branch, commit code to your branch.
Once the E2E tests pass (see testing), create a pull request and code review.

### New Connector Setup

Creating a new connector means adding a directory to:

`${S2S_PATH}/connector/www/v1/stock2shop/dal/channels`

Once you have added this directory, the E2E tests will automatically
call the appropriate methods with test data.

See [architecture](architecture.md) about the channel interface.

## Tests

An End-To-End or E2E test is included. End-to-end testing is a technique which is used to test software from
beginning to end. It is an overall test which defines all dependencies in a system. We ask that you do not modify
our e2e test - although you may read through it to gain a better understanding of how your connector integration
will fit into the Stock2Shop system.

There is one E2E test in the `e2e` directory in the root of this repository: `ChannelTest.php`.
The test has been designed to evaluate the connector implementations you have added in the `www/v1/dal/channels/`
directory.

The primary objective of the tests is to test whether your connector integration is working correctly.
Use it whilst you implement the Products, Orders and Fulfillments concrete classes. Please note that there is mock 
product, order, fulfillment and Stock2Shop channel "metadata" in the `tests/e2e/data` directory which the test uses to 
simulate synchronization of data onto our system.

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
