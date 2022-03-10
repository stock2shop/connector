# MEMORY - CONNECTOR

## Overview

This is an example channel. 
Use this as a boilerplate for creating new channels.

## Storage

The `memory` connector persists data to an in-memory storage class [ChannelState](./ChannelState.php)
where products and images are created, updated or removed from the channel's state during synchronization. 

## Data Models

This channel models product and image data differently to the way that the Stock2Shop system does. 
A [memory\ProductMapper](./ProductMapper.php) class has been provided to transform [vo\ChannelProduct](../../../vo/ChannelProduct.php) 
and [vo\ChannelVariant](../../../vo/ChannelImage.php) into [MemoryProduct](./memoryProduct.php) objects - 
which has a flat structure and does not support "variants".

Images are mapped by the [memory\ImageMapper](./ImageMapper.php) from [vo\ChannelImage](../../../vo/ChannelImage) objects
into [memory\MemoryImage](./memoryImage.php) objects.

## Meta Configuration

Custom channel configuration has been added to the [data](../../../../../../tests/e2e/data/channels/memory) directory
of the e2e test which includes the `mustache_template` meta template used to map the product data onto the data classes
discussed in the previous section.