<?php

return array(    
    'router' => array(
        'app' => 'Frontend',
        'controller' => 'Index',
        'action' => 'Index',        
        'suffix' => '.html',
        'uri_protocol'=> 'PATH_INFO',        
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
