<?php

/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace H1Soft\H\Web;

use \H1Soft\H\Web\Application;

abstract class Controller extends \H1Soft\H\Collections\HArray {

    protected $_viewScripts = array();
    protected $_viewCss = array();
    //template engine
    protected $_engine;
    public $request;
    public $rootPath;
    public $basePath;
    protected $_tplVars = array();

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
            $this->_engine->set('BASEPATH', $this->basePath);
            $this->_engine->set('cssFiles', $this->_viewCss);
            $this->_engine->set('jsFiles', $this->_viewScripts);
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
        if ($this->_engine) {
            return $this->_engine;
        }
        if (\H1Soft\H\Web\Config::get('view.template')) {
            $engine = sprintf("\\H1Soft\\H\\Web\\Template\\%s", \H1Soft\H\Web\Config::get('view.template'));

            if (!class_exists($engine)) {
                $engine = "\\H1Soft\\H\\Web\\Template\\View";
            }
            $this->_engine = new $engine();
        }
        return $this->_engine;
    }

    public function getRender() {
        if (isset($this->_engine)) {
            return $this->_engine;
        }
        return $this->_initTemplateEngine();
    }

    public function assign($_valName, $_valValue) {
        $this->_tplVars[$_valName] = $_valValue;
    }
    
    public function getTplVars() {
        return $this->_tplVars;
    }

    public function setRender($_engine) {
        return $this->_engine = $_engine;
    }

    public function request() {
        return Application::request();
    }

    public function get($_key, $defaultValue = NULL) {
        return Application::request()->get($_key, $defaultValue);
    }

    public function post($_key, $defaultValue = NULL) {
        return Application::request()->getPost($_key, $defaultValue);
    }

    public function query($_key, $_rev = true) {
        if ($_rev) {
            return Application::request()->get_post($_key);
        }
        return Application::request()->post_get($_key);
    }

    public function param($_key) {
        return Application::request()->getParam($_key);
    }

    public function db($_dbname = 'db') {
        return \H1Soft\H\Db\Db::getConnection($_dbname);
    }

    public function redirect($url, $httpCode = 302) {
        if (startWith($url, 'http://') || startWith($url, 'https://')) {
            
        } else {
            $url = url_to($url);
        }

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

    public function isAdmin($_return = false) {
        $auth = \H1Soft\H\Web\Auth::getInstance();
        if ($_return) {
            return $auth->isAdmin();
        }
        if (!$auth->isAdmin()) {
            //redirect
            $this->redirect('auth/invalid');
        }
    }

    public function isSuperAdmin($_return = false) {
        $auth = \H1Soft\H\Web\Auth::getInstance();
        if ($_return) {
            return $auth->isAdmin();
        }
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

    protected function isAjax() {
        return $this->req()->isAjax();
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

    public function saveUrlRef($url = NULL) {
        if ($url) {
            Application::session()->set('hurlref', $url);
        } else {
            Application::session()->set('hurlref', $this->req()->curUrl());
        }
        return Application::session()->get('hurlref');
    }

    public function urlRef() {
        $rtn = Application::session()->get('hurlref');
        return $rtn ? $rtn : Application::app()->request()->curUrl();
    }

    public function showFlashMessage($message, $code = H_ERROR, $url = NULL) {
        if (!$url) {
            $url = $this->req()->curUrl();
        }
        Application::session()->set('hflash', $message);
        Application::session()->set('hcode', $code);
        $this->redirect($url);
    }

    public function setFlashMessage($message, $code = H_ERROR) {
        Application::session()->set('hflash', $message);
        Application::session()->set('hcode', $code);
    }

    public function getFlashMessage($default = "") {
        $message = Application::session()->get('hflash');
        Application::session()->remove('hflash');
        Application::session()->remove('hcode');
        return $message ? $message : $default;
    }

}
