<?php


namespace H1Soft\H\Utils;

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
