<?php

/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * 判断开始字符串
 * @param type $str
 * @param type $needle
 * @return type
 */
function startWith($str, $needle) {

    return strpos($str, $needle) === 0;
}

/**
 * 判断结束字符串
 * @param type $haystack
 * @param type $needle
 * @param type $case
 * @return type
 */
function endsWith($haystack, $needle, $case = true) {
    $expectedPosition = strlen($haystack) - strlen($needle);

    if ($case) {
        return strrpos($haystack, $needle, 0) === $expectedPosition;
    }

    return strripos($haystack, $needle, 0) === $expectedPosition;
}

/**
 * Strip Image Tags
 *
 * @param	string	$str
 * @return	string
 */
function strip_image_tags($str) {
    return preg_replace(array('#<img[\s/]+.*?src\s*=\s*["\'](.+?)["\'].*?\>#', '#<img[\s/]+.*?src\s*=\s*(.+?).*?\>#'), '\\1', $str);
}

/**
 * 获取变量
 * @param type $param
 * @param type $default
 * @return type
 */
function get_default($param, $default = NULL) {
    if (isset($param)) {
        return $param;
    }
    return NULL;
}

/**
 * Get GET input
 */
function get($key = null, $filter = FILTER_SANITIZE_MAGIC_QUOTES) {
    if (!$key) {
        return $filter ? filter_input_array(INPUT_GET, $filter) : $_GET;
    }
    if (isset($_GET[$key]))
        return $filter ? filter_input(INPUT_GET, $key, $filter) : $_GET[$key];
}

/**
 * Get POST input
 */
function post($key = null, $filter = FILTER_SANITIZE_MAGIC_QUOTES) {
    if (!$key) {
        return $filter ? filter_input_array(INPUT_POST, $filter) : $_POST;
    }
    return $filter ? filter_input(INPUT_POST, $key, $filter) : $_POST[$key];
}

function get_post($key = null, $filter = FILTER_SANITIZE_MAGIC_QUOTES) {

    if (!isset($GLOBALS['_GET_POST']))
        $GLOBALS['_GET_POST'] = $_GET + $_POST;
    if (!$key)
        return $filter ? filter_input_array($GLOBALS['_GET_POST'], $filter) : $GLOBALS['_GET_POST'];

    if (isset($GLOBALS['_GET_POST'][$key]))
        return $filter ? filter_var($GLOBALS['_GET_POST'][$key], $filter) : $GLOBALS['_GET_POST'][$key];
}

/**
 * 获取COOKIE信息
 * @param type $key
 * @param type $filter
 * @return type
 */
function cookie($key = null, $filter = FILTER_SANITIZE_MAGIC_QUOTES) {
    if (isset($_COOKIE[$key]))
        return $filter ? filter_input(INPUT_COOKIE, $key, $filter) : $_COOKIE[$key];
}

function hmvc_error($code, $message, $file, $line) {
    if (0 == error_reporting()) {
        return;
    }
    if ($code) {
        \hmvc\System\StackTrace::systemError($message, false, true, false);
    }
}

function hmvc_exceptionHandler($exception) {
    if (0 == error_reporting()) {
        return;
    }
    \hmvc\System\StackTrace::exceptionError($exception);
}

/**
 * 页面跳转
 * @param type $url
 * @param type $_params
 * @param type $httpCode
 */
function redirect($url, $_params = NULL, $httpCode = 302) {
    $url = url_to($url, $_params);
    if ($httpCode == 301) {
        header('HTTP/1.1 301 Moved Permanently');
    }
    header("Location: $url");
    die;
}

function url_for($_url, $_params = NULL, $_type = false) {
    return url_to($_url, $_params, $_type);
}

function url_to($_url, $_params = NULL, $_type = false) {
    $app = strtolower(hmvc\Web\Application::app()->router()->getAppName());
    $basePath = hmvc\Web\Application::basePath();
    if (is_array($_url)) {
        if (isset($_url[1])) {
            $app = $_url[1];
        }
        $_url = $_url[0];
        $basePath = hmvc\Web\Application::request()->baseUrl();
    }

    if ($app == hmvc\Web\Application::app()->router()->getAppName() || hmvc\Web\Application::app()->router()->getAppName() == \hmvc\Web\Config::get('router.app')) {
        $app = '';
    } else {
        //Route Alias
        $alias = hmvc\Web\Config::get('alias');
        $aliasName = array_search($app, $alias);
        if ($aliasName) {
            $app = $aliasName;
        } else {
//            $app = strtolower(h\Web\Application::app()->router()->getAppName());
        }
        $app = '/' . $app;
    }

    if (startWith($_url, '/')) {
        $_url = ltrim($_url, '/');
        $app = '';
    }
    if (hmvc\Web\Config::get("router.uri_protocol") == "PATH_INFO") {
        $_type = true;
    }
    //index.php
    $showscriptname = hmvc\Web\Config::get('router.showscriptname', 'index.php');
    //Query String
    $querystring = "";

    if (is_array($_params) && $_type == false) {
        if ($showscriptname) {
            $querystring = http_build_query($_params);
            return sprintf("%s/%s?r=%s/%s%s&%s", $basePath, $showscriptname, $app, $_url, hmvc\Web\Config::get('router.suffix'), $querystring);
        } else {
            $querystring = '?' . http_build_query($_params);
        }
    } else if (is_array($_params) && $_type == true) {

        foreach ($_params as $key => $value) {
            $querystring .= '/' . $key . "/" . $value;
        }
        if ($showscriptname) {
            return sprintf("%s/%s%s/%s%s%s", $basePath, $showscriptname, $app, $_url, $querystring, hmvc\Web\Config::get('router.suffix'));
        } else {

            return sprintf("%s%s/%s%s%s", $basePath, $app, $_url, $querystring, hmvc\Web\Config::get('router.suffix'));
        }
    } else {
        if ($showscriptname && $_type == false) {
            $showscriptname = '/' . $showscriptname . '?r=';
        } else if ($showscriptname && $_type == true) {
            $showscriptname = '/' . $showscriptname;
        }
        return sprintf("%s%s%s/%s%s%s", $basePath, $showscriptname, $app, $_url, hmvc\Web\Config::get('router.suffix'), $querystring);
    }

    return sprintf("%s%s/%s%s%s", $basePath, $app, $_url, hmvc\Web\Config::get('router.suffix'), $querystring);
}

/**
 * 数组转成对象
 * @param type $data
 * @return object
 */
function arrayToObject($data) {
    if (is_array($data)) {
        return (object) array_map(__FUNCTION__, $d);
    } else {
        return $data;
    }
}

/**
 * 对象转换成数组
 * @param type $d
 * @return type
 */
function objectToArray($d) {
    if (is_object($d)) {
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        return array_map(__FUNCTION__, $d);
    } else {
        return $d;
    }
}

/**
 * 读取配置文件
 * @param string $_key
 * @param mixed $_default
 * @return null or value
 */
function config($_key, $_default = NULL) {
    return \hmvc\Web\Config::get($_key, $_default);
}

/**
 * 获取SESSION
 * @param type $key
 * @param type $default
 * @return type
 */
function session($key, $default = NULL) {
    return isset($_SESSION[$k]) ? $_SESSION[$k] : $default;
}

function p() {
    foreach (func_get_args() as $value) {
        dump($value);
    }
    die;
}

/**
 * 获取根目录
 * @return type
 */
function rootPath() {
    return hmvc\HApplication::rootPath();
}

/**
 * VAR目录
 * @return type
 */
function varPath() {
    return hmvc\HApplication::varPath();
}

function encode($string, $to = 'UTF-8', $from = 'UTF-8') {
    if ($to == 'UTF-8' AND is_ascii($string)) {
        return $string;
    }

    return @iconv($from, $to . '//TRANSLIT//IGNORE', $string);
}

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'utf-8');
}

/**
 * Filter a valid UTF-8 string so that it contains only words, numbers,
 * dashes, underscores, periods, and spaces - all of which are safe
 * characters to use in file names, URI, XML, JSON, and (X)HTML.
 *
 * @param string $string to clean
 * @param bool $spaces TRUE to allow spaces
 * @return string
 */
function sanitize($string, $spaces = TRUE) {
    $search = array(
        '/[^\w\-\. ]+/u', // Remove non safe characters
        '/\s\s+/', // Remove extra whitespace
        '/\.\.+/', '/--+/', '/__+/' // Remove duplicate symbols
    );

    $string = preg_replace($search, array(' ', ' ', '.', '-', '_'), $string);

    if (!$spaces) {
        $string = preg_replace('/--+/', '-', str_replace(' ', '-', $string));
    }

    return trim($string, '-._ ');
}

/**
 * 中文URL转码
 *
 * @param string $string to filter
 * @return string
 */
function sanitize_url($string) {
    return urlencode(mb_strtolower(sanitize($string, FALSE)));
}

/**
 * 对象转成XML
 * @param mixed $mixed
 * @param DOMDocument $domElement
 * @param DOMDocument $DOMDocument
 */
function xml_encode($mixed, $domElement = null, $DOMDocument = null) {
    if (is_null($DOMDocument)) {
        $DOMDocument = new DOMDocument;
        $DOMDocument->formatOutput = true;
        xml_encode($mixed, $DOMDocument, $DOMDocument);
        echo $DOMDocument->saveXML();
    } else {
        if (is_array($mixed)) {
            foreach ($mixed as $index => $mixedElement) {
                if (is_int($index)) {
                    if ($index === 0) {
                        $node = $domElement;
                    } else {
                        $node = $DOMDocument->createElement($domElement->tagName);
                        $domElement->parentNode->appendChild($node);
                    }
                } else {
                    $plural = $DOMDocument->createElement($index);
                    $domElement->appendChild($plural);
                    $node = $plural;
                    if (!(rtrim($index, 's') === $index)) {
                        $singular = $DOMDocument->createElement(rtrim($index, 's'));
                        $plural->appendChild($singular);
                        $node = $singular;
                    }
                }

                xml_encode($mixedElement, $node, $DOMDocument);
            }
        } else {
            $domElement->appendChild($DOMDocument->createTextNode($mixed));
        }
    }
}

/**
 * 打印日志
 * @param type $message
 * @return boolean
 */
function log_message($message) {
    if (empty($message)) {
        return false;
    }
    $log_path = config('logs.path', 'var/logs/');
    if ($log_path == 'var/logs/') {
        $path = rootPath() . $log_path . date('Y-m-d') . '.log';
    } else {
        $path = $log_path . date('Y-m-d') . '.log';
    }

    return error_log(date('H:i:s ') . getenv('REMOTE_ADDR') . " $message\r\n", 3, $path);
}

/**
 * 判断是否是ASCII
 * @param type $string
 * @return type
 */
function is_ascii($string) {
    return !preg_match('/[^\x00-\x7F]/S', $string);
}

function directory($dir, $recursive = TRUE) {
    $i = new \RecursiveDirectoryIterator($dir);
    if (!$recursive)
        return $i;
    return new \RecursiveIteratorIterator($i, \RecursiveIteratorIterator::SELF_FIRST);
}

function dir_is_writable($dir, $chmod = 0755) {
    if (!is_dir($dir) AND ! mkdir($dir, $chmod, TRUE))
        return FALSE;
    if (!is_writable($dir) AND ! chmod($dir, $chmod))
        return FALSE;
    return TRUE;
}

/**
 * 打印对象
 */
function dump() {
    $string = '';
    $index = 0;
    echo '<p><h2>Dumper</h2></p>';
    foreach (func_get_args() as $value) {
        $string .= '<p><h3>Object #' . $index . '</h3></p>
            <pre style="background: #FFFFCC repeat scroll 0 0;
border: 0px solid #aaaaaa;
border-radius: 10px 10px 10px 10px;
color: #000000;
font-size: 11pt;
line-height: 160%;
margin-bottom: 1em;
padding: 1em;">' . h($value === NULL ? 'NULL' : (is_scalar($value) ? $value : print_r($value, TRUE))) . "</pre>\n";
        $index++;
    }
    echo($string);
}
