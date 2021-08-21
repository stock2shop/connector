# PHP connectors

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

Test use phpunit.
Ensure your tests work with:

```bash
cd ${S2S_PATH}/connector/tests
./phpunit-4.8.phar ./
```

### New channel setup

Creating a new channel means adding a directory to:

`${S2S_PATH}/connector/www/v1/stock2shop/dal/channels`

Once you hav added this directory, the E2E tests will automatically
call the appropriate methods with test data.

