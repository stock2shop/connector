# OS - CONNECTOR

## Overview

This folder contains the files which make up a connector for a hypothetical system
which writes and reads data to the OS.

This example connector, instead of reading to some 3rd party platform via an API it uses the
file system to store the data. It is here purely as a guide for you.

## Storage

JSON data created by this channel is stored in the [data directory](data)

## Classes

## Custom Channel Data

You will most likely want to make your connector configurable per channel.
How you do this is up to the constraints of the system you are coding the integration for.

Start by copying the [channelData.json](tests/e2e/data/channels/os/channelData.json) file in the [tests/e2e/data/channels/os](./tests/e2e/data/channels/os)
directory into a new directory in the [tests/e2e/data/channels](./tests/e2e/data/channels) directory.
You must name this directory the same as your `CHANNEL_NAME` variable. Your implementation in 
`www/v1/stock2shop/dal/channels/${CHANNEL_NAME}/Products` may make use of the sample meta data
configured in the `channelData.json`. The workflow in the [Products](./Products.php) class of this sample connector 
provides a working example of using channel meta data for storage path separators.

Another potential use is if your channel uses the HTTP protocol, then you might want to use channel meta data to 
configure your API client with your channel's API credentials (secret, client_id), endpoint URLs, etc.

## Tests

Unit tests have been added to [tests/unit](../../../../../../tests/unit/www/v1/stock2shop/dal/channels/os) to evaluate 
the custom code in the [data/Helper](./data/Helper.php) class as well as the additional methods which do not come from
the Stock2Shop [DAL interfaces](../../channel).
