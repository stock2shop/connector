# OS - CONNECTOR

## Overview

This folder contains the files which make up a connector library for a hypothetical system.

The three main classes of this connector each implement the Data Access Layer specifications defined in `stock2shop\dal\channel`.
Data structures are represented by objects from `stock2shop\vo`. 
For more information on what the specifications are please refer to [Value Objects](../../../vo) and the [Interfaces](../../channel).

This example scaffolds the minimum code required to turn a basic flat-file system into a Stock2Shop channel.
Data is read, written and removed from the flat-file system's [data](./data) directory.

## Storage

The system stores Product, Order and Fulfillment data on disk.
Objects are persisted in JSON format in `.json` files.
 
Please refer to the README files found in each one of the three subdirectories of `data` for a detailed explanation of 
the file naming conventions:

1. [Products Storage](data/products/readme.md)
2. [Orders Storage](data/orders/readme.md)
3. [Fulfillments Storage](data/fulfillments/readme.md)

## Classes

*Creator*

- This class is the concrete implementation of the [dal\channel\Creator](../../channel/Creator.php) abstract class.
- The Creator returns instances of [Products](../../channels/os/Products.php) class in the `createProducts()` 
- And [Orders](../../channels/os/Orders.php) class in the `createOrders()` method.
- [Fulfillments](../../channel/Fulfillments.php) is not implemented.

*Products* 

- This class defines the synchronization workflow for the 'os' connector between the channel and Stock2Shop.
- The `sync()` method uses channel meta data (for separators- and storage location) to configure the connector dynamically.
- The `getByCode()` method uses the [data/Helper](./data/Helper.php) class to get Products from the channel by code.
- The storage prefix is used to get the products which match the provided string.
- The `get()` method returns all products from a specific position by use of the '$token' variable. 
- Stock2Shop uses this mechanism to filter through records during synchronization.
- A value of an empty string ("") will return all products from the channel.

## Tests

Unit tests have been added to [tests/unit](../../../../../../tests/unit/www/v1/stock2shop/dal/channels/os) to evaluate 
the custom code in the [data/Helper](./data/Helper.php) class as well as the additional methods which do not come from
the Stock2Shop [DAL interfaces](../../channel).
