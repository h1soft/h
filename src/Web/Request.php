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

class Request extends \hmvc\Singleton {

    private $_segments = array();
    private $_params = array();

    public function init() {
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            if (isset($_GET)) {
                $_GET = Security::stripSlashes($_GET);
            }
            if (isset($_POST)) {
                $_POST = Security::stripSlashes($_POST);
            }
            if (isset($_REQUEST)) {
                $_REQUEST = Security::stripSlashes($_REQUEST);
            }
            if (isset($_COOKIE)) {
                $_COOKIE = Security::stripSlashes($_COOKIE);
            }
        }

        foreach ($_SERVER as $key => $value) {
            $this->$key = $value;
        }

        foreach ($_ENV as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * 获取客户端IP地址
     * @return string 当前IP地址
     */
    public function ipAddress() {
        return $this->REMOTE_ADDR ? $this->REMOTE_ADDR : $this->HTTP_X_FORWARDED_FOR;
    }

    public function userAgent() {
        return $this->HTTP_USER_AGENT;
    }
    
    public function language() {
        return $this->HTTP_ACCEPT_LANGUAGE;
    }

    public function requestUri() {
        return $this->REQUEST_URI;
    }
    
    public function baseUrl() {
        return Application::basePath();
    }

    public function curUrl() {
        $pageURL = 'http://';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL = "https://";
        }
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
     * 
     * @param string $_key 
     * @param NULL $defaultValue
     * @return $_GET or NULL
     */
    public function get($_key, $defaultValue = null) {
        if (isset($_GET[$_key])) {
            return $_GET[$_key];
        } else if (isset($this->_params[$_key])) {
            return $this->_params[$_key];
        }
        return $defaultValue;
    }

    public function post($_key, $defaultValue = null) {
        if (isset($_POST[$_key])) {
            return $_POST[$_key];
        }
        return $defaultValue;
    }

    public function get_post($_key, $defaultValue = null) {
        if (isset($this->_params[$_key])) {
            return $this->_params[$_key];
        }
        if (isset($_GET[$_key])) {
            return $_GET[$_key];
        }
        if (isset($_POST[$_key])) {
            return $_POST[$_key];
        }
        return $defaultValue;
    }

    public function post_get($_key, $defaultValue = null) {
        if (isset($_POST[$_key])) {
            return $_POST[$_key];
        }
        if (isset($this->_params[$_key])) {
            return $this->_params[$_key];
        }
        if (isset($_GET[$_key])) {
            return $_GET[$_key];
        }
        return $defaultValue;
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

    public function param($_key, $defaultValue = null) {
        if (isset($this->_params[$_key])) {
            return $this->_params[$_key];
        }
        return $defaultValue;
    }

    public function setParams($_params) {
        $this->_params = $_params;
    }

    public function getParams() {
        return $this->_params;
    }

    public function getParam($name, $defaultValue = null) {
        return isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
    }

    public function getQuery($name, $defaultValue = null) {
        return isset($_GET[$name]) ? $_GET[$name] : $defaultValue;
    }

    public function getPost($name, $defaultValue = null) {
        return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
    }

    public function getQueryString() {
        return isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    }

    public function isHttps() {
        return isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';
    }

    public function getRequestType() {
        if (isset($_POST['_method'])) {
            return strtoupper($_POST['_method']);
        }

        return strtoupper(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET');
    }

    public function getUrlReferrer() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }

    public function getUserAgent() {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }

    public function getUserHostAddress() {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }

    public function getAcceptTypes() {
        return isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : null;
    }

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
        if ($this->REQUEST_METHOD == "POST") {
            return true;
        }
        return false;
    }

    public function isGet() {
        if ($this->REQUEST_METHOD == "GET") {
            return true;
        }
        return false;
    }

    public function isPut() {
        if ($this->REQUEST_METHOD == "PUT" || $this->_METHOD || $this->_METHOD == "PUT") {
            return true;
        }
        return false;
    }

    public function isDelete() {
        if ($this->REQUEST_METHOD == "DELETE" || $this->_METHOD || $this->_METHOD == "DELETE") {
            return true;
        }
        return false;
    }

    public function isAjax() {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
            return true;
        }
        return false;
    }

    function xssClean($data, $htmlentities = 0) {
        $htmlentities && $data = htmlentities($data, ENT_QUOTES, 'utf-8');
        // Fix &entity\n;
        $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"\\\\]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"\\\\]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"\\\\]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"\\\\]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"\\\\]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"\\\\]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);


        return $this->filter_remote_img_type($data, FALSE);
    }

    function filter_remote_img_type($text, $bbcode = TRUE) {
        $pattern = $bbcode ? "/\[img[^\]]*\]\s*(.*?)+\s*\[\/img\]/is" : "/<img[^>]+src=[\'|\"]([^\'|\"]+)[\'|\"][^>]*[\/]?>/is";
        preg_match_all($pattern, $text, $matches);
        foreach ($matches[1] as $k => $src) {
            $data = get_headers($src);
            $header_str = implode('', $data);
            if (FALSE === strpos($header_str, 'Content-Type: image') || FALSE !== strpos($header_str, 'HTTP/1.1 401') || FALSE !== strpos($header_str, 'HTTP/1.1 404')) {
                $text = str_replace($matches[0][$k], '', $text);
            }
        }
        return $text;
    }

}
