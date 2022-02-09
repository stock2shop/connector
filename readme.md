# Stock2Shop - PHP Connectors

Stock2Shop connectors connect ERP accounting systems with sales channels, 
such as ecommerce shopping carts, market-places and our B2B trade store.

The purpose of this repository is to allow 3rd party developers to contribute 
connectors to our ecosystem.

This repository is a bare bones interface to guide you on what we require.
It includes an example channel connector, to illustrate data structures 
and our workflow.  

## Submission Guidelines

### PHP Version

We support PHP 7.1 at this stage.
You can read more about the features and changes that came with this version 
in the [PHP Manual](https://www.php.net/manual/en/migration71.new-features.php)

### Coding Style

This project conforms to the PSR-12 style recommendation. 
You can read more about PHP-FIG and PSRs [here](https://www.php-fig.org/psr/psr-12/).

If you use PhpStorm, you can configure your IDE to analyze your code and assist you in conforming to PSR-12:

1. Open your IDE Preferences (Mac OS ```CMD + ,```) or Settings (```CTRL + ALT + S```)
2. Navigate to ```Editor | Code Style | PHP```
3. Look for the ```Set from...``` link in the top-right hand side of the window.
4. Click it and select PSR12 from the options.
5. You can now clean-up files/reformat them using ```CMD + OPT + L``` (Mac OS).
6. It is recommended to 

### Architecture

See [architecture.md](architecture.md)
Read this before writing code.

### Github Workflow

You will be assigned a branch, commit code to your branch.
Once the E2E tests pass (see testing), create a pull request and code review.

### Setup

The recommended way of setting up the repository:

```bash
export S2S_PATH=/your/path/for/stock2shop
git clone https://github.com/stock2shop/connector.git ${S2S_PATH}/connector
```

Add your custom libraries through composer. 
Please check if the library has not already been added to the composer.json file and 
do not update the versions of libraries included in this libary.

### Testing

Tests use the [phpunit](https://phpunit.readthedocs.io/en/9.5/installation.html).
Ensure your tests are working with:

```bash
cd ${S2S_PATH}/connector/tests
./phpunit-4.8.phar ./
```

### New Channel Setup

Creating a new channel means adding a directory to:

`${S2S_PATH}/connector/www/v1/stock2shop/dal/channels`

Once you have added this directory, the E2E tests will automatically
call the appropriate methods with test data.

See [architecture](architecture.md) about the channel interface.
