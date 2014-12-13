HMVC
===================================

## Build Status
[![Build Status](https://travis-ci.org/h1soft/h.svg?branch=master)](https://travis-ci.org/h1soft/h)

php 5.3 mvc framework
-----------------------------------


## 快速开始

新建文件 : composer.json
```json
{    
    "require": {
        "h1soft/h": "dev-master"
    }
}
```

## 使用Composer工具
```
composer install
```

新建入口文件 : index.php
```php
require 'vendor/autoload.php';

$app = new \hmvc\Web\Application();
$app->run();
```



目录结构

```yaml
project
       |__companyName
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


## 系统最低要求

php5.3或者以上


## 支持
自定义路由
MySQLi
