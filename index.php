<?php


#error_reporting(E_ERROR | E_WARNING | E_PARSE);



require 'vendor/autoload.php';

// H1Soft\H\Collections\Config::load(include 'app/config/config.php');

//print_r(H1Soft\H\Collections\Config::get('db'));

//$app = new H1Soft\H\Application;
$app = new \H1Soft\H\Web\Application();

$app->run();


