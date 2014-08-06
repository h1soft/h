HFramework
===================================

php 5.3 mvc framework
-----------------------------------


## A Simple Example

New File: composer.json
```json
{    
    "require": {
        "h1soft/h": "1.2.3"
    }
}
```

New File : index.php
```php
require 'vendor/autoload.php';

$app = new \H1Soft\H\Web\Application();
$app->bootstrap('\Module\Bootstrap')->run();
```


[![image]](https://travis-ci.org/h1soft/h/)
[image]: https://api.travis-ci.org/h1soft/h.svg?branch=master "hframework"
