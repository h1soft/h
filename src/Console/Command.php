<?php
namespace hmvc\Console;
/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Command
 *
 * @author allen <i@w4u.cn>
 */
class Command extends \hmvc\HApplication {

    private static $appsPath;
    private static $etcPath;
    private static $themesPath;
    private static $_app;    
    private static $_psr_loader;
    private static $_session;
    
    private static $config;
    
    private static $instance;
    
    private static $webApp;



    public function __construct() {
        parent::__construct();
        self::$_app = $this;        
        self::$webApp = new \hmvc\Web\Application();
        \hmvc\HApplication::$rootPath = dirname(\hmvc\HApplication::$rootPath) . '/';
        \hmvc\HApplication::$varPath = dirname(\hmvc\HApplication::$rootPath) . DS . 'var/';
    }


    public function run() {
        
        self::$etcPath = \hmvc\HApplication::$rootPath . 'etc/';

        self::$themesPath = \hmvc\HApplication::$rootPath . 'themes/';

        $this->src = 'Apps';

        $this->_initConfig();
        
        //register autoloader
        self::$_psr_loader = new \hmvc\ClassLoader\Autoloader();

        $this->_autoLoader();

        self::$_psr_loader->register();

        self::$appsPath = \hmvc\HApplication::$rootPath . $this->src . '/';

        #set_error_handler("hmvc_error");
        #set_exception_handler("hmvc_exceptionHandler");
    }

    /**
     * 
     * @return \hmvc\Web\Session
     */
    public static function session() {
        if (!self::$_session) {
            self::$_session = Session::getInstance();
        }
        return self::$_session;
    }

    function colorize($text, $color, $bold = FALSE) {
        $colors = array_flip(array(30 => 'gray', 'red', 'green', 'yellow', 'blue', 'purple', 'cyan', 'white', 'black'));

        return"\033[" . ($bold ? '1' : '0') . ';' . $colors[$color] . "m$text\033[0m";
    }
    
    

    public function appsPath() {
        return self::$appsPath;
    }

    public function etcPath() {
        return self::$etcPath;
    }
    
    public function app() {
        return self::$_app;
    }
    
    public function db($_dbname = 'db') {
        return \hmvc\Db\Db::getConnection($_dbname);
    }
    
    private function _initConfig() {
        self::$config = require self::$etcPath . 'config.php';
        foreach (self::$config as $key => $value) {
            self::$webApp->$key = $value;
        }
    }
    
    private function _autoLoader() {

        if (isset($this->src) && is_string($this->src)) {
            self::$_psr_loader->addNameSpace("\\{$this->src}\\", $this->src);
        }

        if (isset($this->autoload['psr4']) && is_array($this->autoload['psr4'])) {
            foreach ($this->autoload['psr4'] as $key => $value) {
                self::$_psr_loader->addNameSpace($key, $value);
            }
        }
    }
    
    public static function getInstance() {
        $classname = get_called_class();
        if (isset(self::$instance)) {
            return self::$instance;
        }

        self::$instance = new $classname();     
        self::$instance->run();
        return self::$instance;
    }

}
