# EXAMPLE - CONNECTOR

## Overview

This is an example channel. 
Use this as a boilerplate for creating new channels.

## Classes

*Creator*

Concrete class implementation of the [dal\channel\Creator](../../channel/Creator.php) class.
The `createOrders()` and `createFulfillments()` methods throw [NotImplemented](../../../exceptions/NotImplemented.php) exceptions
as the connector only implements the [dal\channel\Products](../../../dal/channel/Products.php) interface.

*Products*

This class defines the workflow for synchronization between the channel and Stock2Shop's system.
It is a very simple and uncomplicated example which illustrates the minimum requirements for 
a channel connector which syncs products.

## Usage

Use this example as the starting point for your implementation.
See the main [readme](../../../../../../readme.md) file for the setup steps.