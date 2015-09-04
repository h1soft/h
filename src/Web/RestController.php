<?php

/*
 * Copyright (C) 2014 Allen Niu <h@h1soft.net>

 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.



 * This file is part of the hmvc package.
 * (w) http://www.hmvc.cn
 * (c) Allen Niu <h@h1soft.net>

 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.


 */

namespace hmvc\Web;

/**
 * 默认规则
 * 如果Action不存在调用默认函数
 * 
 * GET      user/       index
 * GET      user/1      show
 * DELETE   user/1      destory
 * POST     user/       create
 * GET      user/new    create
 * PUT      user/       edit
 */

/**
 * Restful Controller
 *
 * @author allen <i@w4u.cn>
 */
class RestController {

    /**
     * 当前请求的方法
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var array 允许的方法
     */
    protected $allowMethod = array('POST', 'GET', 'DELETE', 'PUT', 'HEAD');

    /**
     *
     * @var array  支持响应的格式
     */
    protected $allowType = array('xml', 'json', 'html');
    protected $formatType = 'json';

    /**
     *
     * @var type 
     */
    protected $contentType = array(
        'xml' => 'application/xml,text/xml,application/x-xml',
        'json' => 'application/json,text/x-json,application/jsonrequest,text/json',
        'js' => 'text/javascript,application/javascript,application/x-javascript',
        'css' => 'text/css',
        'rss' => 'application/rss+xml',
        'yaml' => 'application/x-yaml,text/yaml',
        'atom' => 'application/atom+xml',
        'pdf' => 'application/pdf',
        'text' => 'text/plain',
        'png' => 'image/png',
        'jpg' => 'image/jpg,image/jpeg,image/pjpeg',
        'gif' => 'image/gif',
        'csv' => 'text/csv',
        'html' => 'text/html,application/xhtml+xml,*/*'
    );

    /**
     *
     * @var array
     */
    protected $_viewScripts = array();

    /**
     *
     * @var array 
     */
    protected $_viewCss = array();

    /**
     *
     * @var type 模版引擎
     */
    protected $_engine;

    /**
     *
     * @var Request 当前请求 
     */
    public $request;

    /**
     *
     * @var string 网站根目录
     */
    public $rootPath;

    /**
     *
     * @var string 网站URL 
     */
    public $basePath;

    /**
     *
     * @var array 模版变量 
     */
    protected $_tplVars = array();

    public function __construct() {
        $this->request = Application::request();
        $this->rootPath = Application::$rootPath;
        $this->basePath = Application::$basePath;
        $this->method = $this->request->getMethod();
        $this->formatType = $this->request->getAcceptType();
    }

    public function init() {
        
    }

    public function before() {
        
    }

    public function after() {
        
    }

    public function dispatch($method, $arguments) {
        $callMethod = $method . '_';
        $defaultMethod = 'index';
        switch ($this->method) {
            case 'GET':
                $defaultMethod = 'index';
                break;
            case 'POST':
                $defaultMethod = 'create';
                break;
            case 'PUT':
                $defaultMethod = 'update';
                break;
            case 'DELETE':
                $defaultMethod = 'destroy';
                break;
            default:
                break;
        }
        if ($method == 'new' || (isset($arguments[0]) && $arguments[0] == 'new')) {
            $defaultMethod = 'create';
        }
        $callMethod = $callMethod . $defaultMethod;
        if (method_exists($this, $callMethod)) {
            call_user_func_array(array(Application::router()->getController(), $callMethod), $arguments);
        } else if (method_exists($this, $defaultMethod)) {
            array_unshift($arguments, $method);
            call_user_func_array(array(Application::router()->getController(), $defaultMethod), $arguments);
        } else {
            array_unshift($arguments, $method);
            call_user_func_array(array(Application::router()->getController(), "default"), $arguments);
        }
    }

    public function render($tplFileName = false, $data = true, $output = true) {
        $this->_initTemplateEngine();
        $this->_engine->setArray($this->_tplVars);
        $this->_engine->set('HVERSION', HVERSION);
        $this->_engine->set('BASEPATH', $this->basePath);
        $this->_engine->set('cssFiles', $this->_viewCss);
        $this->_engine->set('jsFiles', $this->_viewScripts);
        if ($this->_engine->isDefault()) {
            $this->TplFullPath = $this->_engine->render($tplFileName, $data, $output);
            extract($this->_engine->getArray());
            ob_start();
            include($this->TplFullPath);
            $rendered = ob_get_contents();
            ob_end_clean();
            if (!$output) {
                return $rendered;
            }
            echo $rendered;
            return false;
        }
        return $this->_engine->render($tplFileName, $data, $output);
    }

    public function response($data, $format = NULL) {
        if ($format == NULL) {
            $format = $this->formatType;
        }
        if (empty($data)) {
            $data = array();
        }
        switch ($format) {
            case 'json':
                header('Content-Type: application/json');
                echo json_encode($data);
                break;
            case 'xml':
                header('Content-Type: application/xml');
                echo xml_encode($data);
                break;

            default:
                break;
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
        if (\hmvc\Web\Config::get('view.template')) {
            $engine = sprintf("\\hmvc\\Web\\Template\\%s", \hmvc\Web\Config::get('view.template', 'View'));

            if (!class_exists($engine)) {
                throw new \Exception("模版引擎不存在");
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

    public function getCss() {
        return $this->_viewCss;
    }

    public function getJs() {
        return $this->_viewScripts;
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
        return \hmvc\Db\Db::getConnection($_dbname);
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
        $auth = \hmvc\Web\Auth::getInstance();
        if (!$auth->isAllowed($_method)) {
            //redirect
            $this->redirect('index/noauth');
        }
    }

    public function isAdmin($_return = false) {
        $auth = \hmvc\Web\Auth::getInstance();
        if ($_return) {
            return $auth->isAdmin();
        }
        if (!$auth->isAdmin()) {
            //redirect
            $this->redirect('auth/invalid');
        }
    }

    public function isSuperAdmin($_return = false) {
        $auth = \hmvc\Web\Auth::getInstance();
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
