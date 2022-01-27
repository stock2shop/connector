# SCRIPTS

## vojson.php

Run this PHP script to output the Value Object/VO classes in JSON format. The script serializes the class properties outputs it to stdout.

The syntax is:

1. Output all objects:
```bash
php ${S2S_PATH}/connector/www/v1/stock2shop/scripts/stock2shop/VOJSON.php
```

2. Output single objects by name:
```bash
php ${S2S_PATH}/connector/www/v1/stock2shop/scripts/stock2shop/VOJSON.php --class=Variant
php ${S2S_PATH}/connector/www/v1/stock2shop/scripts/stock2shop/VOJSON.php --class=SystemOrder
```