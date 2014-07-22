<?php

/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    public static function model() {
        return self::getInstance();
    }

}
