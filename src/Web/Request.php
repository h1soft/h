<?php

namespace H1Soft\H\Web;

class Request {

    private $_gets = array();
    private $_posts = array();
    private $_segments = array();
    private $_params = array();
    
    private $_cookies;

    public function __construct() {
        $_server = filter_input_array(INPUT_SERVER,FILTER_SANITIZE_MAGIC_QUOTES);
        foreach ($_server as $key => $value) {
            $this->$key = $value;
        }
        
        $_env = filter_input_array(INPUT_ENV,FILTER_SANITIZE_MAGIC_QUOTES);
        foreach ($_env as $key => $value) {
            $this->$key = $value;
        }
        
        $_post = filter_input_array(INPUT_POST,FILTER_SANITIZE_MAGIC_QUOTES);
        if($_post) {
            foreach ($_post as $key => $value) {
                $this->_posts[$key] = $value;
            }
        }
        $_get = filter_input_array(INPUT_GET,FILTER_SANITIZE_MAGIC_QUOTES);
        if($_get){
            foreach ($_get as $key => $value) {
                $this->_gets[$key] = $value;
            }
        }
        
        $_cookie = filter_input_array(INPUT_COOKIE,FILTER_SANITIZE_MAGIC_QUOTES);
        if($_cookie){
            foreach ($_cookie as $key => $value) {
                $this->_cookies[$key] = $value;
            }
        }
//        unset($_server);
    }

    public function ipAddress() {
        return $this->REMOTE_ADDR;
    }

    public function userAgent() {
        return $this->HTTP_HOST;
    }

    public function language() {
        return $this->HTTP_HOST;
    }

    public function requestUri() {
        return $this->REQUEST_URI;
    }
    
    public function curUrl() {
        $pageURL = 'http://';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL = "https://";
        }
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }        
        return $pageURL;
    }

    public function get($_key) {
        if (isset($this->_gets[$_key])) {
            return $this->_gets[$_key];
        }else if (isset($this->_params[$_key])) {
            return $this->_params[$_key];
        }
        return NULL;
    }
    
    public function post($_key) {
        if (isset($this->_posts[$_key])) {
            return $this->_posts[$_key];
        }
        return NULL;
    }
    
    
    public function get_post($_key) {
        if (isset($this->_params[$_key])) {
            return $this->_params[$_key];
        }
        if (isset($this->_gets[$_key])) {
            return $this->_gets[$_key];
        }
        if (isset($this->_posts[$_key])) {
            return $this->_posts[$_key];
        }
        return NULL;
    }
    
    public function post_get($_key) {
        if (isset($this->_posts[$_key])) {
            return $this->_posts[$_key];
        }
        if (isset($this->_params[$_key])) {
            return $this->_params[$_key];
        }
        if (isset($this->_gets[$_key])) {
            return $this->_gets[$_key];
        }        
        return NULL;
    }

    public function segment($_key) {
        if (is_array($this->_segments) && isset($this->_segments[$_key])) {
            return $this->_segments[$_key];
        }
        return NULL;
    }

    public function setSegment($_segments) {
        $this->_segments = $_segments;
    }
    
    public function param($_key) {
        if (isset($this->_params[$_key])) {
            return $this->_params[$_key];
        }
        return NULL;
    }
    
    public function setParams($_params) {
        $this->_params = $_params;
    }
    
    public function getParams() {
        $this->_params;
    }
    
    //Utils

    function get_ip() {
        if (!defined("IP")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR") ? getenv("HTTP_X_FORWARDED_FOR") : getenv("REMOTE_ADDR");
            if (!preg_match("^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}^", $ip)) {
                $ip = null;
            }
            define("IP", $ip);
        }
        return IP;
    }

    /**
     * Return true if $ip is a valid ip
     */
    function is_ip($ip) {
        return preg_match("^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}^", $ip);
    }

    /**
     * Return the browser information of the logged user
     */
    function get_browser_info() {

        if (!isset($GLOBALS['rain_browser_info'])) {
            $known = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape', 'konqueror', 'gecko');
            preg_match('#(' . join('|', $known) . ')[/ ]+([0-9]+(?:\.[0-9]+)?)#', strtolower($_SERVER['HTTP_USER_AGENT']), $br);
            preg_match_all('#\((.*?);#', $_SERVER['HTTP_USER_AGENT'], $os);

            global $rain_browser_info;
            $rain_browser_info['lang_id'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $rain_browser_info['browser'] = isset($br[1][1]) ? $br[1][1] : null;
            $rain_browser_info['version'] = isset($br[2][1]) ? $br[2][1] : null;
            $rain_browser_info['os'] = $od[1][0];
        }
        return $GLOBALS['rain_browser_info'];
    }
    
    public function isPost() {
        if($this->REQUEST_METHOD =="POST"){
            return true;
        }
        return false;
    }
    
    public function isGet() {
        if($this->REQUEST_METHOD =="GET"){
            return true;
        }
        return false;
    }
    
    public function isPut() {
        if($this->REQUEST_METHOD =="PUT" || $this->_METHOD || $this->_METHOD == "PUT"){
            return true;
        }
        return false;
    }
    public function isDelete() {
        if($this->REQUEST_METHOD =="DELETE" || $this->_METHOD || $this->_METHOD == "DELETE"){
            return true;
        }
        return false;
    }

}
