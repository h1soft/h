<?php

namespace H1Soft\H\Web;

use \H1Soft\H\Web\Application;

abstract class Controller extends \H1Soft\H\Collections\HArray {

    private $_viewScripts = array();
    private $_viewCss = array();
    //template engine
    private $_engine;
    public $request;
    public $rootPath;
    public $basePath;
    private $_tplVars = array();

    public function __construct() {
        $this->request = Application::request();
        $this->rootPath = Application::$rootPath;
        $this->basePath = Application::$basePath;
    }

    public function init() {
        
    }

    public function before() {
        
    }

    public function indexAction() {
        
    }

    public function after() {
        
    }

    public function render($tplFileName = false, $data = true, $output = true) {
        try {
            $this->_initTemplateEngine();
            $this->_engine->setArray($this->_tplVars);
            $this->_engine->set('HVERSION', HVERSION);
            return $this->_engine->render($tplFileName, $data, $output);
        } catch (Twig_Error_Loader $e) {
            print_r($e);
        }
    }

    public function addJs($filename) {
        array_push($this->_viewScripts, $filename);
    }

    public function addCss($filename) {
        array_push($this->_viewCss, $filename);
    }

    private function _initTemplateEngine() {
        if (\H1Soft\H\Web\Config::get('view.template')) {
            $engine = sprintf("\\H1Soft\\H\\Web\\Template\\%s", \H1Soft\H\Web\Config::get('view.template'));

            if (!class_exists($engine)) {
                $engine = "\\H1Soft\\H\\Web\\Template\\View";
            }
            $this->_engine = new $engine();
        }
    }

    public function getRender() {
        return $this->_engine;
    }

    public function assign($_valName, $_valValue) {
        $this->_tplVars[$_valName] = $_valValue;
    }

    public function setRender($_engine) {
        return $this->_engine = $_engine;
    }

    public function request() {
        return Application::request();
    }

    public function get($_key) {
        return Application::request()->get($_key);
    }

    public function post($_key) {
        return Application::request()->post($_key);
    }

    public function query($_key, $_rev = true) {
        if ($_rev) {
            return Application::request()->get_post($_key);
        }
        return Application::request()->post_get($_key);
    }
    
    public function param($_key) {      
        return Application::request()->param($_key);
    }

    public function db($_dbname = 'db') {
        return \H1Soft\H\Db\Db::getConnection($_dbname);
    }

    public function redirect($url, $httpCode = 302) {
        $url = url_to($url);
        if ($httpCode == 301) {
            header('HTTP/1.1 301 Moved Permanently');
        }
        header("Location: $url");
        die;
    }

    public function isAllowed($_method, $_redirect = true) {
        $auth = \H1Soft\H\Web\Auth::getInstance();
        if (!$auth->isAllowed($_method)) {
            //redirect
            $this->redirect('index/noauth');
        }
    }

    public function isAdmin() {
        $auth = \H1Soft\H\Web\Auth::getInstance();

        if (!$auth->isAdmin()) {
            //redirect
            $this->redirect('auth/invalid');
        }
    }
    
    public function isSuperAdmin() {
        $auth = \H1Soft\H\Web\Auth::getInstance();

        if (!$auth->isSuperAdmin()) {
            //redirect
            $this->redirect('auth/invalid');
        }
    }

    protected function isPost() {
        return $this->req()->isPost();
    }

    protected function isPut() {
        return $this->req()->isPut();
    }

    protected function isGet() {
        return $this->req()->isGet();
    }

    protected function isDelete() {
        return $this->req()->isDelete();
    }

    /**
     * 
     * @return Request 
     */
    protected function req() {
        return Application::request();
    }

    protected function res() {
        return Application::response();
    }

    protected function session() {
        return Application::session();
    }

    public function setFlashMessage($message) {
        Application::session()->set('hflash', $message);
    }

    public function getFlashMessage($default = "") {
        $message = Application::session()->get('hflash');
        Application::session()->remove('hflash');
        return $message ? $message : $default;
    }

}
