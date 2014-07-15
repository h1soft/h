<?php


#error_reporting(E_ERROR | E_WARNING | E_PARSE);



require 'vendor/autoload.php';

date_default_timezone_set('Asia/Shanghai');
$app = new \H1Soft\H\Web\Application();

$app->run();


