<?php

/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace hmvc\Web\Template;

use hmvc\Web\Application;
use hmvc\Web\Config;

class View extends \hmvc\Web\AbstractTemplate {

    private $tplRealPath;

    public function __construct() {
        $this->setTheme(Config::get('view.theme'));
        $this->setViewPath(rootPath() . 'themes/' . $this->getTheme() . '/');
    }

    public function isDefault() {
        return true;
    }

    public function render($filename = false, $data = false, $output = true) {
        $actionName = strtolower(rtrim(Application::app()->router()->getActionName(), 'Action'));;
        if (is_array($data)) {
            $this->_hdata = array_merge($this->_hdata, $data);
        }
        if (empty($filename)) {
            $filename = $actionName;
        }
        if (is_array($filename) && is_bool($data)) {
            if (!$data) {
                $output = false;
            }
            $data = array_merge($this->_hdata, $filename);
            $filename = sprintf("%s/%s.php", strtolower(Application::app()->router()->getControllerName()), $actionName);
        }

        $this->tplRealPath = sprintf("%s%s.php", $this->getViewPath(), $filename);
        $this->setTemplateFileName($filename);

        if (!is_file($this->tplRealPath)) {
            throw new \Exception("模版不存在 {$this->tplRealPath}");
        }

        return $this->tplRealPath;
    }

    public function assign($_valName, $_valValue) {
        $this->$_valName = $_valValue;
    }

    public function get($_valName) {
        return isset($this->$_valName) ? $this->$_valName : NULL;
    }

    public function set($_valName, $_valValue) {
        $this->$_valName = $_valValue;
    }

}
