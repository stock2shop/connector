# EXAMPLE

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

## Folder Structure

The file and folder structure consists of:

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
    ├── LICENSE
    └── README

