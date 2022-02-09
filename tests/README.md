# Stock2Shop - PHP Connectors

## Overview

An End-To-End or E2E test is included. End-to-end testing is a technique which is used to test software from
beginning to end. It is an overall test which defines all dependencies in a system. We ask that you do not modify
our e2e test - although you may read through it to gain a better understanding of how your connector integration 
will fit into the Stock2Shop system.

The primary objective of the tests is to test whether your connector integration is working correctly.
Use it whilst you implement the Products, Orders and Fulfillments concrete classes. (see the main [README](../README.md)
file for more information).

## PhpUnit Version

Throughout this repository we have tried our best to make writing and testing your integration as easy and fimiliar 
for you as possible. The e2e test uses PhpUnit version 4.8. The .phar executable has been included for you to run
the test with on your local environment.

## Run The Tests 

To run the tests simply change to the tests directory in your terminal and run the following command:

```bash
./phpunit-4.8.phar ./
```

The test output will be displayed in your console. 
If there are errors - it indicates an issue with your connector integration.

## How It Works

There is one E2E test in the `e2e` directory in the root of this repository: `ChannelTest.php`.
The test has been designed to evaluate the connector implementations you have added in the `www/v1/dal/channels/` 
directory.

Please note that there is mock product, order, fulfillment and Stock2Shop channel "metadata" i9n the `tests/e2e/data`
directory which the test uses to simulate synchronization of data onto our system.

1. The test is bootstrapped in the setUp() class method which is executed first.
    - The channel types are set by scanning the www/v1/dal/channels/directory.
    - Each directory is considered a channel type.
    - Throughout the test, the channelTypes class property is used to run a number of test cases against your integration.
    
2. The test evaluates all methods from the [Products interface](../www/v1/stock2shop/dal/channel/Products.php). 
   - testProductsSync() method evaluates the sync() method you have implemented in your Products.php file. 
   - testProductsGet() method evaluates the get() method you have implemented in your Products.php file. 
   - testProductsGetByCode() method evaluates the getByCode() method you have implemented in your Products.php file.

## TODO

- Add complete workflow for each method. (overkill?)
- Add fulfillments and orders.
