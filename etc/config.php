<?php

return array(    
    'router' => array(
        'app' => 'Catalog',
        'controller' => 'Index',
        'action' => 'Index',        
        'suffix' => '.html',
        'showscriptname'=>false,
        'uri_protocol'=> 'PATH_INFO',  
        'rewrite'=>array(
            '(\d+)/(\d+)/(\d+)/(.*)' => 'index/index/year/{0}/m/{1}/day/{2}/title/{3}',
            'post/(\d+).html'=>'index/post/id/{0}'           
        )
    ),
    'alias' => array(
        'admin' => 'backend'
    ),
    'src' => 'Apps',
    'autoload' => array(
        'psr0' => array(),
        'psr4' => array(),
    ),
    // database settings
    'databases' => array(
        'db' => array(
            'driver' => 'mysqli',
            'host' => 'localhost',
            'database' => 'h',
            'username' => 'root',
            'password' => '',
            'prefix' => 'h_',
            'charset' => 'utf8',            
            'port' => '3306'
        ),
    ),
    'debug' => true,
    'view' => array(
        'theme' => 'default',
        'template' => 'Twig',
        'cache' => false
    ),
);
