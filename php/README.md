# PHP connectors

## Submission Guidelines

### PHP version
7.1

### Code formatting
TODO

### Github workflow
You will be assigned a branch, commit code to your branch.
Once tests pass, create a pull request and code review.

### Setup

Clone repo

```bash
git clone https://github.com/stock2shop/connector.git
```

Set project path

```bash
export S2S_PHP_PATH=/your/path/to/stock2shop/connector/php/
```

Run example test
```bash
php ${S2S_PHP_PATH}v1/test/channel.php --channel_type=example
```
You should see the output of the test results without any errors

### 3rd party / external libraries

You may require the use of a 3rd party library.
Load the library via composer and commit vendor files to your branch.

We have certain libraries already loaded. 

- GuzzleHTTP 6.2.3 Use this library for external API requests. 

Do not change the version of existing libraries.

#### installing composer

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"

php composer.phar update

```