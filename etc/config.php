<?php

return array(
    //router
    'router' => array(
        'controller'=>'Index',
        'action'=>'Index',
        'app'=>'Frontend',
        'suffix'=>'.html'
    ),

    'alias' => array(
        'backend'=>'admin'
    ),
    'src'=>'Apps',
    'autoload'=> array(
        'psr0'=>array(),
        'psr4'=>array(),
        ),
	// database settings
    'db' => array(        
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'mvc',
        'username' => 'root',
        'password' => '',
        'prefix' => 'apcmf_',
        'charset' => 'uft8',
        'schema' => 'public'
    ),    
    'debug'=>true,
    'view'=>array(
        'theme'=>'default',
        'template'=>'Twig',
        'cache'=>false
    ),
    

);
