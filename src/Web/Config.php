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

class Config {

    private function __construct() {
        
    }

    static public function get($_key, $_default = "") {
        if (empty($_key)) {
            return $_default;
        }
        $subkeys = explode('.', $_key);

        $_len = count($subkeys);
        if ($_len == 1) {
            return Application::app()->$subkeys[0];
        } else if ($_len <= 2) {
            $_tobj = Application::app()->$subkeys[0];
            if (is_array($_tobj) && isset($_tobj[$subkeys[1]])) {
                return $_tobj[$subkeys[1]];
            }
        } else {

            $_tobj = Application::app()->$subkeys[0];
            array_shift($subkeys);

            foreach ($subkeys as $value) {

                if (is_string($_tobj)) {
                    return $_default;
                } else if (is_array($_tobj) && isset($_tobj[$value])) {
                    $_tobj = $_tobj[$value];
                }
            }
            return $_tobj;
        }



        return $_default;
    }

    static public function set($_key, $_default = "") {
        if (empty($_key)) {
            return $_default;
        }
        $subkeys = explode('.', $_key);

        $_len = count($subkeys);
        if ($_len == 1) {
            Application::app()->$subkeys[0] = $_default;
        } else if ($_len <= 2) {
            $_tobj = Application::app()->$subkeys[0];
            if (is_array($_tobj) && isset($_tobj[$subkeys[1]])) {
                $_tobj[$subkeys[1]] = $_default;                
                Application::app()->$subkeys[0] = $_tobj;
            }            
        }
    }

}
