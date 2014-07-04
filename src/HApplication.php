<?php

namespace H1Soft\H;


date_default_timezone_set('UTC');

iconv_set_encoding("internal_encoding", "UTF-8");

mb_internal_encoding('UTF-8');

// System Start Time
define('START_TIME', microtime(true));

// System Start Memory
define('START_MEMORY_USAGE', memory_get_usage());

define('DS',DIRECTORY_SEPARATOR);

// Absolute path to the system folder
define('SP', realpath(__DIR__). DS);


//Common Functions
require 'Common.php';


class HApplication extends \H1Soft\H\Collections\HArray {

	//站点根目录
	public static $rootPath;	

	public static $varPath;

	public function __construct(){
		self::$rootPath = getcwd() . DS;
		self::$varPath = getcwd() . DS . 'var/';
		
	}

	public static function rootPath(){
		return self::$rootPath;
	}

	public static function varPath(){
		return self::$varPath;
	}

	

}
