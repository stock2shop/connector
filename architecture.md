# Architecture

This document describes the high-level architecture of the connector code base.

## Code Map

This section talks about important directories and data structures. 

### /tests

PHP unit test framework.
We have included E2E (end-to-end) tests directory.

You do not have to write your own tests, the E2E tests should cover our requirements.

There are certain tests that may require you to add data.
For example, an order webhook, which would require example data sent to our connector.
This can be added to the `tests/data/` directory.

### /www/vendor

composer libraries

Ensure you commit any libraries added.
Do not change the version of existing libraries.
Check first if there is a library to perform the function
you want before loading a new library.

Use / learn composer

```bash
brew install composer # macOS / Linux
choco install composer # Windows
```

If you need to add new libraries, ensure these are added 
by composer and that the appropriate vendor files are committed.

### /www/v1/stock2shop/vo

VO Stands for [Value Object](https://martinfowler.com/bliki/ValueObject.html).
The purpose of the Value Object classes is to define the data we pass around.

You cannot edit anything in this directory.

To output all the data structures in a JSON format via the command line, run this:

```bash
php ${S2S_PATH}/connector/www/v1/stock2shop/scripts/stock2shop/VOJSON.php
```

If you want to view a specific VO, use this.

```bash
php ${S2S_PATH}/connector/www/v1/stock2shop/scripts/stock2shop/VOJSON.php --class=Variant
```

This is useful for quickly viewing classes which may extend multiple parents.

### /www/v1/stock2shop/dal

DAL stands for "Data Access Layer".
The purpose of this directory is to separate logic related 
to different systems.

### /www/v1/stock2shop/dal/channels

A channel is usually a e-commerce shopping cart or a market place.
Think of channel as in "sales channel".
If you have been commissioned to integrate code into a specific
shopping cart, then you will create a directory here.

Each channel must have these classes:-

- Connector.php
- Creator.php

These are called by our application using the factory method.

`creator.php` is used to create a new instance of your channel.
You can copy the Creator found in `example/Creator.php`.

`connector.php` implements the `channel/Connector.php` interface.
It must therefore include all methods and have the same method signatures.

## General

This sections talks about the things which are everywhere and nowhere in particular.

### TODO 