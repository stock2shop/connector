# SCRIPTS

## Overview

This directory contains helper scripts for use during development.
Feel free to add your own here.

## vojson.php

Run this PHP script to output the Value Object/VO classes in JSON format.

The syntax is:

1. Output all objects:
```bash
php ${S2S_PATH}/connector/www/v1/stock2shop/scripts/stock2shop/voJson.php
```

2. Output single objects by name:
```bash
php ${S2S_PATH}/connector/www/v1/stock2shop/scripts/stock2shop/voJson.php --class=Variant
php ${S2S_PATH}/connector/www/v1/stock2shop/scripts/stock2shop/voJson.php --class=SystemOrder
```
