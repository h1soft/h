<?php
/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace hmvc\Utils;

class PHPDoc {
    public static function findName($_name,$_doc) {
        $result = NULL;
        preg_match("/@$_name (.*) */", $_doc, $result);        
        if(isset($result[1])){
            return $result[1];
        }
        return NULL;
    }
    
    public static function bool($_name,$_doc) {
        $result = NULL;
        preg_match("/@$_name*/", $_doc, $result);        
        if($result){
            return true;
        }
        return false;
    }
}
