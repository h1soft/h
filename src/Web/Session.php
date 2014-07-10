<?php

namespace H1Soft\H\Web;

class Session extends \H1Soft\H\Singleton {
    
    protected function init() {        
        \session_start();
    }
    
    public function set($_name,$_value) {
        $_SESSION[$_name] = $_value;
    }
    
    public function get($_name) {
        if(isset($_SESSION[$_name])){
            return $_SESSION[$_name];
        }else{
            return NULL;
        }
    }
    
    public function remove($_name) {
        unset($_SESSION[$_name]);
    }
    
}
