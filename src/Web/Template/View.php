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

class View extends \H1Soft\H\Web\AbstractTemplate {

    private $_charset = 'utf-8';
    private $_ext = ".php";

    public function __construct() {
        
    }

    public function render($filename = false, $data = false, $output = true) {
        if ($data) {
            if (!is_array($data)) {
                throw new RuntimeException("You must pass an array to data view.");
            }
            $this->_data = array_merge($this->_data, $data);
        }

        $filename = $this->_selectView($this->getViewPaths(), $filename);

        $rendered = "";

        ob_start();
        require($filename);
        $rendered = ob_get_contents();
        ob_end_clean();

        return $rendered;
    }

    public function assign($_valName, $_valValue) {
        
    }

    public function get($_valName) {
        
    }

    public function set($_valName, $_valValue) {
        
    }

}
