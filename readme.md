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
Notable features you may use include:

- Class constant visibility may be declared either public, protected or private.
- Iterable pseudo-types in parameters and return types.
- Multi-catch exception handling.
- Symmetric array destructuring.
- Void return type.
- Type declaration for function parameters may be marked as nullable. 
- Function return values may be marked as nullable.

### Coding Style

This project conforms to the PSR-12 style recommendation. 
You can read more about PHP-FIG and PSRs [here](https://www.php-fig.org/psr/psr-12/).

An overview of the PSR-12 style:

- All rules in PSR-1.
- Your PHP files must use the Unix LF (linefeed) only.
- Omit the closing brackets `?>` at the bottom of your PHP files.
- No hard limit on line-length.
- The soft-limit on line-length is 120 characters.
- No trailing whitespace at the end of lines.
- You may add blank lines for readability.
- Strictly one statement per line.
- Indentation must use 4 spaces only. Do not use tabs for indentation.
- All PHP reserved types, such as `string` or `int`, must always be in lower-case.
- Use the short form of types; `int` not `integer` and `bool` not `boolean`.
- Always include parenthesis after your class declarations `new Creator()` not `new Creator`.

If you use PhpStorm, you can configure it to analyze your code and assist you in conforming to PSR-12:

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

Add your custom libraries through composer (preferably). 
Please check if the library has not already been added to the composer.json file.

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

Once you have added this directory, the E2E tests will automatically
call the appropriate methods with test data.

See [architecture](architecture.md) about the channel interface.
