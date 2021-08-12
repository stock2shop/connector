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

```bash
export S2S_PATH=/your/path/for/stock2shop
git clone https://github.com/stock2shop/connector.git ${S2S_PATH}/connector
```

### Running channel example test

```bash
php ${S2S_PATH}/connector/v1/test/channel.php --channel_type=example
```

You should see the output of the test results without any errors

e.g. 
```
----------------------------------------------------------------------------------------------------
-------------------------------------------Sync Products--------------------------------------------
----------------------------------------------------------------------------------------------------

product->channel_product_code                      = db11.json
product->success                                   = 1
product->synced                                    = 2021-08-11 12:02:44
product->variants[]->channel_variant_code          = db11~DB%2F9%2F0%2F11.json
product->variants[]->success                       = 1
product->channel_product_code                      = CD.json
product->success                                   = 1
product->synced                                    = 2021-08-11 12:02:44
product->variants[]->channel_variant_code          = CD~CDROM%2FPOR25.json
product->variants[]->success                       = 1
product->variants[]->channel_variant_code          = CD~CDROM%2FPOR5.json
product->variants[]->success                       = 1
```

### 3rd party / external libraries

You may require the use of a 3rd party library.
Load the library via composer and commit vendor files to your branch.

We have certain libraries already loaded. 

- GuzzleHTTP 6.2.3 Use this library for external API requests. 

Do not change the version of existing libraries.

#### installing composer

```bash
brew install composer # macOS / Linux
choco install composer # Windows
```