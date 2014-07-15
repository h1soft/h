<?php

namespace H1Soft\H;

abstract class Singleton {

    private static $instance = array();

    private function __construct() {
        
    }
    
    protected function init() {
        
    }

    public static function getInstance() {
        $classname = get_called_class();   
        if (isset(self::$instance[$classname])) {
            return self::$instance[$classname];
        }
             
        self::$instance[$classname] = new $classname();
        self::$instance[$classname]->init();
        return self::$instance[$classname];
    }

}
