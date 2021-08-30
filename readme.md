# PHP connectors

Stock2Shop connectors connect ERP accounting systems with sales channels, 
such as ecommerce shopping carts, market places and our B2B trade store.

The purpose of this repository is to allow 3rd party developers to contribute 
connectors to our ecosystem.

This repository is a bare bones interface to guide you on what we require.
It includes an example channel connector, to illustrate data structures 
and our workflow.  

## Submission Guidelines

### PHP version
7.1

### Architecture

See [architecture.md](architecture.md)
Read this before writing code.

### Github workflow

You will be assigned a branch, commit code to your branch.
Once the E2E tests pass (see testing), create a pull request and code review.

### Setup

```bash
export S2S_PATH=/your/path/for/stock2shop
git clone https://github.com/stock2shop/connector.git ${S2S_PATH}/connector
```

### Testing

Tests use the [phpunit](https://phpunit.readthedocs.io/en/9.5/installation.html).
Ensure your tests are working with:

```bash
cd ${S2S_PATH}/connector/tests
./phpunit-4.8.phar ./
```

### New channel setup

Creating a new channel means adding a directory to:

`${S2S_PATH}/connector/www/v1/stock2shop/dal/channels`

Once you hav added this directory, the E2E tests will automatically
call the appropriate methods with test data.

See [architecture](architecture.md) about the channel interface.