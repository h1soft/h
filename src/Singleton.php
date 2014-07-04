<?php

namespace H1Soft\H;


abstract class Singleton {

	private static $instance = NULL;

	private function __construct(){}

    public static function getInstance()
    {
        $classname = get_called_class();

        if (self::$instance != NULL) return self::$instance;
        self::$instance = new $classname();
        return self::$instance;
    }

}
