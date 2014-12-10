<?php
/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace hmvc;

iconv_set_encoding("internal_encoding", "UTF-8");

mb_internal_encoding('UTF-8');

// System Start Time
define('START_TIME', microtime(true));

// System Start Memory
define('START_MEMORY_USAGE', memory_get_usage());

define('DS', DIRECTORY_SEPARATOR);

define('HVERSION', 'HMVC v1.2.3');

define('H_ERROR', 'danger');

define('H_WARNING', 'warning');

define('H_INFO', 'info');

define('H_SUCCESS', 'success');

// Absolute path to the system folder
define('SP', realpath(__DIR__) . DS);


//Common Functions
require 'Common.php';

class HApplication extends \hmvc\Collections\HArray {

    
    public static $rootPath;
    public static $varPath;

    public function __construct() {
        self::$rootPath = getcwd() . DS;
        self::$varPath = getcwd() . DS . 'var/';
    }

    public static function rootPath() {
        return self::$rootPath;
    }

    public static function varPath() {
        return self::$varPath;
    }

}
