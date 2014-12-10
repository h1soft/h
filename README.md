HFramework
===================================
## Build Status
[![Build Status](https://travis-ci.org/h1soft/h.svg?branch=master)](https://travis-ci.org/h1soft/h)

php 5.3 mvc framework
-----------------------------------


## A Simple Example

New File: composer.json
```json
{    
    "require": {
        "h1soft/h": "dev-master"
    }
}

composer install
```

New File : index.php
```php
require 'vendor/autoload.php';

$app = new \hmvc\Web\Application();
$app->bootstrap('\Module\Bootstrap')->run();
```

```
project
       |__vendor
              |___ModuleA
                       |_____Controller
                                   |_____Index.php
              |___ModuleB
                       |_____Controller
                                   |_____Index.php
       |__etc
            |__config.php
            |__db.php
            |__rewrite.php

```

