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
//    echo "<b>Error:</b> [$errno] $errstr<br />";
//    echo " Error on line $errline in $errfile<br />";
//    echo "Ending Script";
//    die();
    throw new ErrorException($message, 0, $code, $file, $line);
}

function hmvc_exceptionHandler($exception) {
    if (0 == error_reporting()) {
        return;
    }
    $version = HVERSION;
    echo <<<EOF
    <!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$version}</title>
     </head>
  <body>
   
EOF;
    echo '<div class="alert alert-danger">';
    echo '<h3>' . get_class($exception) . '</h3><p>';
    echo $exception->getMessage() . '<br></p>';
    echo 'Stack trace:<pre style="background-color:#ccc;">' . $exception->getTraceAsString() . '</pre>';
    echo 'thrown in <b>' . $exception->getFile() . '</b> on line <b>' . $exception->getLine() . '</b><br>';
    echo '</div>';
//    highlight_string(_getRows($exception->getFile(), $exception->getLine() - 3, $exception->getLine() + 3));
    echo '</body></html>';
}

function _getRows($filename, $start, $offset = 0) {
    $rows = file($filename);
    $rowsNum = count($rows);
    if ($offset == 0 || (($start + $offset) > $rowsNum)) {
        $offset = $rowsNum - $start;
    }
    $fileList = "<?php
/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */\n\n\t........\n\n";
    for ($i = $start; $max = $start + $offset, $i < $max; $i++) {
        $fileList .= $rows[$i];
    }
    return $fileList;
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

function arrayToObject($d) {
    if (is_array($d)) {
        /*
         * Return array converted to object
         * Using __FUNCTION__ (Magic constant)
         * for recursive call
         */
        return (object) array_map(__FUNCTION__, $d);
    } else {
        // Return object
        return $d;
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
        echo '<pre style="background-color:#EEE">' . print_r($value, TRUE) . "</pre><br/>";
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

function varPath() {
    return hmvc\HApplication::varPath();
}

/**
 * 打印日志
 * Log: ROOTPATH: var/logs/
 * @param type $message
 * @return type
 */
function hlog($message) {
    $path = varPath() . 'logs/' . date('Y-m-d') . '.log';
    return error_log(date('H:i:s ') . getenv('REMOTE_ADDR') . " $message\n", 3, $path);
}

function encode($string, $to = 'UTF-8', $from = 'UTF-8') {
    // ASCII is already valid UTF-8
    if ($to == 'UTF-8' AND is_ascii($string)) {
        return $string;
    }

    // Convert the string
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
 * Create a SEO friendly URL string from a valid UTF-8 string.
 *
 * @param string $string to filter
 * @return string
 */
function sanitize_url($string) {
    return urlencode(mb_strtolower(sanitize($string, FALSE)));
}

function to_xml($object, $root = 'data', $xml = NULL, $unknown = 'element', $doctype = "<?xml version = '1.0' encoding = 'utf-8'?>") {
    if (is_null($xml)) {
        $xml = simplexml_load_string("$doctype<$root/>");
    }

    foreach ((array) $object as $k => $v) {
        if (is_int($k)) {
            $k = $unknown;
        }

        if (is_scalar($v)) {
            $xml->addChild($k, h($v));
        } else {
            $v = (array) $v;
            $node = array_diff_key($v, array_keys(array_keys($v))) ? $xml->addChild($k) : $xml;
            self::from($v, $k, $node);
        }
    }

    return $xml;
}
