<?php

/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace hmvc\Web;

abstract class AbstractTemplate extends \hmvc\Collections\HArray {

    protected $_templateFileName;
    protected $_viewPath;
    protected $_theme;

    abstract public function render($filename = false, $data = false, $output = false);

    abstract public function assign($_valName, $_valValue);

    abstract public function get($_valName);

    public function setArray($_valArray) {
        if (is_array($_valArray) && !empty($_valArray)) {

            $this->_hdata = array_merge($this->_hdata, $_valArray);
        }
    }

    public function getArray() {
        return $this->_hdata;
    }

    abstract public function set($_valName, $_valValue);

    public function getTemplateEngine() {
        return get_called_class();
    }

    public function getTemplateFileName() {
        return $this->_templateFileName;
    }

    public function setTemplateFileName($_templateFileName) {
        return $this->_templateFileName = $_templateFileName;
    }

    public function getViewPath() {
        return $this->_viewPath;
    }

    public function setViewPath($_viewPath) {
        return $this->_viewPath = $_viewPath;
    }

    public function getTheme() {
        return $this->_theme;
    }

    public function setTheme($_theme) {
        return $this->_theme = $_theme;
    }

    public function isDefault() {
        return false;
    }

}
