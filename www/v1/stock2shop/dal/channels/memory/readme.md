# MEMORY - CONNECTOR

## Overview

This is an example channel which attempts to mimic the behavior of a RESTful API.

## Storage

The `memory` connector persists data to an in-memory storage class [ChannelState](./ChannelState.php)
where products and images are created, updated or removed from the channel's state during synchronization.

## Data Models

This channel models product and image data differently to the way that the Stock2Shop system does.
A [memory\ProductMapper](./ProductMapper.php) class has been provided to
transform [vo\ChannelProduct](../../../vo/ChannelProduct.php)
and [vo\ChannelVariant](../../../vo/ChannelImage.php) into [MemoryProduct](./memoryProduct.php) objects - which has a
flat structure and does not support "variants".

Images are mapped by the [memory\ImageMapper](./ImageMapper.php) from [vo\ChannelImage](../../../vo/ChannelImage)
objects into [memory\MemoryImage](./memoryImage.php) objects.

## Mapping Configuration

Custom channel configuration has been added to the [data](../../../../../../tests/e2e/data/channels/memory) directory of
the e2e test which includes the `mustache_template` JSON meta template used to map the product data onto the data
classes discussed in the previous section.

The JSON template makes the product and variant properties which are synchronized to "MemoryProduct"s on the channel
configurable. Due to the limitations of the channel (i.e. the flattened representation of product/variants), the
"channel_product_code" property is hard-coded in the [memory\ProductMapper](./ProductMapper.php) class and is not
included in the JSON config.

The following properties are configurable on channel-level:

- Product title
- Product price
- Product quantity
