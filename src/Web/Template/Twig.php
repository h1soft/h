<?php

/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace H1Soft\H\Web\Template;

use \H1Soft\H\Web\Application;

class Twig extends \H1Soft\H\Web\AbstractTemplate {

    private $_loader;
    private $_twigEnv;
    private $tplFullPath;
    private $cachePath = false;

    public function __construct() {

        $this->setViewPath(\H1Soft\H\Web\Application::themesPath());
        $this->setTheme(\H1Soft\H\Web\Config::get('view.theme'));

        $this->tplFullPath = $this->getViewPath() . \H1Soft\H\Web\Config::get('view.theme', 'default') . '/';

        if (\H1Soft\H\Web\Config::get('view.cache', false) === false) {
            $this->cachePath = false;
        } else {
            $this->cachePath = \H1Soft\H\Web\Application::varPath() . 'cache/';
        }

        if (!isset($this->_loader)) {
            \Twig_Autoloader::register();
            $this->_loader = new \Twig_Loader_Filesystem($this->tplFullPath);
            $this->_twigEnv = new \Twig_Environment($this->_loader, array(
                'cache' => $this->cachePath,
            ));
            $this->initFunctions();
        }
    }

    public function render($filename = false, $data = true, $output = true) {

        if (is_array($data)) {
            $data = array_merge($this->data, $data);
        }


        if (is_array($filename) && is_bool($data)) {

            if (!$data) {
                $output = false;
            }

            $data = array_merge($this->data, $filename);

            $action = strtolower(rtrim(Application::app()->router()->getActionName(), 'Action'));
            $filename = sprintf("%s/%s.html", strtolower(Application::app()->router()->getControllerName()), $action);
        } else {
            if (is_bool($data)) {
                $data = $this->data;
            }
            $filename = $filename . '.html';
        }

        if ($output) {
            echo $this->_twigEnv->render($filename, $data);
        } else {
            return $this->_twigEnv->render($filename, $data);
        }
    }

    public function disableCache() {
        $this->_twigEnv->disableDebug();
    }

    public function disableDebug() {
        $this->_twigEnv->setCache(false);
    }

    public function assign($_valName, $_valValue) {
        $this->$_valName = $_valValue;
    }

    public function get($_valName) {
        return isset($this->_valName) ? $this->_valName : NULL;
    }

    public function set($_valName, $_valValue) {
        $this->$_valName = $_valValue;
    }

    public function addLoaderPath($path, $namespace = false) {
        if ($namespace) {
            $this->_loader->addPath($path, $namespace);
        } else {
            $this->_loader->addPath($path);
        }
    }

    private function initFunctions() {
        $url_for = new \Twig_SimpleFunction('url_to', function ($_url, $_params = NULL) {
            return url_to($_url, $_params);
        });
        $this->_twigEnv->addFunction($url_for);
        $repeat = new \Twig_SimpleFunction('repeat', function ($_str, $_num = 0) {
            return str_repeat($_str, $_num);
        });
        $this->_twigEnv->addFunction($repeat);
        $url_ref = new \Twig_SimpleFunction('urlRef', function () {
            $rtn = Application::session()->get('hurlref');
            return $rtn ? $rtn : Application::app()->request()->curUrl();
        });
        $this->_twigEnv->addFunction($url_ref);
        $flash = new \Twig_SimpleFunction('flash', function ($_remove = true) {
            $flashmessage = Application::session()->get('hflash');
            if ($_remove) {
                Application::session()->remove('hflash');
            }
            return $flashmessage;
        });
        $this->_twigEnv->addFunction($flash);
        $flashCode = new \Twig_SimpleFunction('flashCode', function ($_remove = true) {
            $hcode = Application::session()->get('hcode');
            if ($_remove) {
                Application::session()->remove('hcode');
            }
            return $hcode;
        });
        $this->_twigEnv->addFunction($flashCode);
        $dateFormat = new \Twig_SimpleFunction('dateFormat', function ($_timestamp = NULL, $format = 'Y-m-d') {
            if ($_timestamp) {
                return date($format, $_timestamp);
            } else {
                return NULL;
            }
        });
        $this->_twigEnv->addFunction($dateFormat);
    }

}
