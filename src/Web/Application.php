<?php

namespace H1Soft\H\Web;

/**
 * 
 */
class Application extends \H1Soft\H\HApplication {

   /**
    *
    * @var  H1Soft\H\Web\Route
    */
    private static $_router;
    private static $request;
    private static $response;
    private static $appsPath;
    private static $etcPath;
    private static $themesPath;
    private static $_app;
    public static $basePath;
    private static $_psr_loader;
    private static $_session;
    protected $bootstrap;
    protected $bootstrapObj;

    public function __construct() {
        parent::__construct();
        self::$_app = $this;
    }
    
    /**
     * 
     * @param string $namespace
     * @return \H1Soft\H\Web\Application
     */
    public function bootstrap($namespace) {
        $this->bootstrap = $namespace;
        return $this;
    }
    
    private function startBootstrap() {
        if ($this->bootstrap &&
                class_exists($this->bootstrap)) {
            $className = $this->bootstrap;
            $this->bootstrapObj = new $className();
            $this->bootstrapObj->ApplicationStart();
        }
    }

    private function endBootstrap() {
        if ($this->bootstrap &&
                $this->bootstrapObj) {            
            $this->bootstrapObj->ApplicationEnd();
        }
    }

    public function run() {

        self::$basePath = dirname($_SERVER['SCRIPT_NAME']);

        self::$etcPath = \H1Soft\H\HApplication::$rootPath . 'etc/';

        self::$themesPath = \H1Soft\H\HApplication::$rootPath . 'themes/';

        $this->src = 'Apps';

        $this->_initConfig();

        self::$request = Request::getInstance();

        //init route
        self::$_router = new Router();

        self::$response = new Response();

        //register autoloader
        self::$_psr_loader = new \H1Soft\H\ClassLoader\Autoloader();

        $this->_autoLoader();

        self::$_psr_loader->register();

        self::$appsPath = \H1Soft\H\HApplication::$rootPath . $this->src . '/';

        set_error_handler("hmvc_error");
        set_exception_handler("hmvc_exceptionHandler");

        $this->startBootstrap();

        self::$_router->dispatch();
        
        $this->endBootstrap();
    }
    
    /**
     * 
     * @return \H1Soft\H\Web\Request
     */
    public static function request() {
        return self::$request;
    }
    
    /**
     * 
     * @return \H1Soft\H\Web\Response
     */
    public static function response() {
        return self::$response;
    }

    /**
     * 
     * @return \H1Soft\H\Web\Session
     */
    public static function session() {
        if (!self::$_session) {
            self::$_session = Session::getInstance();
        }
        return self::$_session;
    }
    
    /**
     * 
     * @return \H1Soft\H\Web\Router
     */
    public static function router() {
        return self::$_router;
    }

    public static function appsPath() {
        return self::$appsPath;
    }

    public static function etcPath() {
        return self::$etcPath;
    }

    public static function basePath() {
        return self::$basePath;
    }

    public static function app() {
        return self::$_app;
    }

    public static function db($_dbname = 'db') {
        return \H1Soft\H\Db\Db::getConnection($_dbname);
    }

    public static function themesPath() {
        return self::$themesPath;
    }

    private function _initConfig() {
        $_conf = require self::$etcPath . 'config.php';
        foreach ($_conf as $key => $value) {
            $this->$key = $value;
        }

//        if (!isset($this->themes) && !isset($this->themes['default'])) {
//            $this->themes = array('default' => 'default');
//        }
    }

    private function _autoLoader() {

        if (isset($this->src) && is_string($this->src) && !empty($this->src)) {

            self::$_psr_loader->addNameSpace(sprintf("\\%s\\", $this->src), $this->src);
        }

        // if(isset($this->autoload) && is_array($this->autoload)){
        // 	foreach ($this->autoload as $key => $value) {					
        // 			self::$_psr4_loader->addPrefix($key,$value);
        // 	}
        // }
    }

    public static function checkApp($_name) {
        if (is_dir(self::$appsPath . ucwords($_name))) {
            return true;
        }
        return false;
    }

    public static function checkController($_name) {
        self::$_router->strNameSpace = sprintf("\%s\%s\Controller\%s", Application::app()->src, self::$_router->getAppName(), $_name);
        $controller = self::$_router->getControllerNameSpace();
        if (is_file(self::$appsPath . sprintf("%s/Controller/%s.php", self::$_router->getAppName(), $_name)) && class_exists(self::$_router->strNameSpace)) {
            self::$_router->setController(new $controller());
            return true;
        }
        return false;
    }

    public static function checkAction($_name) {
        if (!self::$_router->getController()) {
            Application::checkController(\H1Soft\H\Web\Config::get('router.controller'), 'Index');
        }
        if (method_exists(self::$_router->getController(), $_name . 'Action')) {
            self::$_router->setAction($_name . 'Action');
            return true;
        }
        return false;
    }

}
