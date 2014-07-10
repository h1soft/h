<?php

function startWith($str, $needle) {

    return strpos($str, $needle) === 0;
}

function endsWith($haystack,$needle,$case=true)
{
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
 * Get COOKIE input
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

    echo '<div class="alert alert-danger">';
    echo '<b>Fatal error</b>:  Uncaught exception \'' . get_class($exception) . '\' with message ';
    echo $exception->getMessage() . '<br>';
    echo 'Stack trace:<pre>' . $exception->getTraceAsString() . '</pre>';
    echo 'thrown in <b>' . $exception->getFile() . '</b> on line <b>' . $exception->getLine() . '</b><br>';
    echo '</div>';
}

function redirect($url, $httpCode = 302) {
    $url = url_to($url);
    if ($httpCode == 301) {
        header('HTTP/1.1 301 Moved Permanently');
    }
    header("Location: $url");
    die;
}

function url_to($_url, $_params = NULL, $_type = false) {
    $app = strtolower(H1Soft\H\Web\Application::app()->router()->getAppName());

//    print_r($alias);

    if ($app == H1Soft\H\Web\Application::app()->router()->getAppName()) {
        $app = '';
    } else {
        //Route Alias
        $alias = H1Soft\H\Web\Config::get('alias');
        $aliasName = array_search($app, $alias);
        if ($aliasName) {
            $app = $aliasName;
        } else {
            $app = H1Soft\H\Web\Application::app()->router()->getAppName();
        }
    }
    if (H1Soft\H\Web\Config::get("router.uri_protocol") == "PATH_INFO") {
        $_type = true;
    }
    $querystring = "";
    if (is_array($_params) && $_type == false) {
        $querystring = '?' . http_build_query($_params);
    } else if (is_array($_params) && $_type == true) {
        foreach ($_params as $key => $value) {
            $querystring .= '/' . $key . "/" . $value;
        }

        return sprintf("%s/%s/%s%s%s", H1Soft\H\Web\Application::basePath(), $app, $_url, $querystring, H1Soft\H\Web\Config::get('router.suffix'));
    }

    return sprintf("%s/%s/%s%s%s", H1Soft\H\Web\Application::basePath(), $app, $_url, H1Soft\H\Web\Config::get('router.suffix'), $querystring);
}

function memory_usage_start($memName = "execution_time") {
    return $GLOBALS['memoryCounter'][$memName] = memory_get_usage();
}

/**
 * Get the memory used
 */
function memory_usage($memName = "execution_time", $byte_format = true) {
    $totMem = memory_get_usage() - $GLOBALS['memoryCounter'][$memName];
    return $byte_format ? byte_format($totMem) : $totMem;
}

/**
 * Start the timer
 */
function timer_start($timeName = "execution_time") {
    $stimer = explode(' ', microtime());
    $GLOBALS['timeCounter'][$timeName] = $stimer[1] + $stimer[0];
}

/**
 * Get the time passed
 */
function timer($timeName = "execution_time", $precision = 6) {
    $etimer = explode(' ', microtime());
    $timeElapsed = $etimer[1] + $etimer[0] - $GLOBALS['timeCounter'][$timeName];
    return substr($timeElapsed, 0, $precision);
}

/**
 * Transform timestamp to readable time format
 *
 * @param int $time unix timestamp
 * @param string format of time (use the constant fdate_format or ftime_format)
 */
function time_format($time = null, $format = DATE_FORMAT) {
    return strftime($format, $time);
}

/**
 * Transform timestamp to readable time format as elapsed time e.g. 3 days ago, or 5 minutes ago to a maximum of a week ago
 *
 * @param int $time unix timestamp
 * @param string format of time (use the constant fdate_format or ftime_format)
 */
function time_elapsed($time = null, $format) {

    $diff = TIME - $time;
    if ($diff < MINUTE)
        return $diff . " " . get_msg('seconds_ago');
    elseif ($diff < HOUR)
        return ceil($diff / 60) . " " . get_msg('minutes_ago');
    elseif ($diff < 12 * HOUR)
        return ceil($diff / 3600) . " " . get_msg('hours_ago');
    elseif ($diff < DAY)
        return get_msg('today') . " " . strftime(TIME_FORMAT, $time);
    elseif ($diff < DAY * 2)
        return get_msg('yesterday') . " " . strftime(TIME_FORMAT, $time);
    elseif ($diff < WEEK)
        return ceil($diff / DAY) . " " . get_msg('days_ago') . " " . strftime(TIME_FORMAT, $time);
    else
        return strftime($format, $time);
}

/**
 * Convert seconds to hh:ii:ss
 */
function sec_to_hms($sec) {
    $hours = intval(intval($sec) / 3600);
    $hms = str_pad($hours, 2, "0", STR_PAD_LEFT) . ':';
    $minutes = intval(($sec / 60) % 60);
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ':';
    $seconds = intval($sec % 60);
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
    return $hms;
}

/**
 * Convert seconds to string, eg. "2 minutes", "1 hour", "16 seconds"
 */
function sec_to_string($sec) {
    $str = null;
    if ($hours = intval(intval($sec) / 3600))
        $str .= $hours > 1 ? $hours . " " . get_msg('hours') : $hours . " " . get_msg('hour');
    if ($minutes = intval(($sec / 60) % 60))
        $str .= $minutes > 1 ? $minutes . " " . get_msg('minutes') : $minutes . " " . get_msg('minute');
    if ($seconds = intval($sec % 60))
        $str .= $seconds > 1 ? $seconds . " " . get_msg('seconds') : $seconds . " " . get_msg('second');
    return $str;
}

function is_email($email) {
    return preg_match('|^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$|i', $email);
}

/**
 * File extension
 *
 * @param string $file filename
 */
function file_ext($filename) {
    return substr(strrchr($filename, '.'), 1);
}

/**
 * Get the name without extension
 *
 * @param string $f filename
 */
function file_name($filename) {
    if (($filename = basename($filename) ) && ( $dot_pos = strrpos($filename, ".") ))
        return substr($filename, 0, $dot_pos);
}

/**
 * Upload one file selected with $file. Use it when you pass only one file with a form.
 * The file is saved into UPLOADS_DIR, the name created as "md5(time()) . file_extension"
 * it return the filename
 *
 * @return string uploaded filename
 */
function upload_file($file) {
    if ($_FILES[$file]["tmp_name"]) {
        $upload_filepath = UPLOADS_DIR . ( $filename = md5(time()) . "." . ( strtolower(file_ext($_FILES[$file]['name'])) ) );
        move_uploaded_file($_FILES[$file]["tmp_name"], $upload_filepath);
        return $filename;
    }
}

/**
 * Upload an image file and create a thumbnail
 *
 * @param string $file
 * @param string $UPLOADS_DIR
 * @param string $thumb_prefix Prefisso della thumbnail
 * @param int $max_width
 * @param int $max_height
 * @param bool $square
 * @return string Nome del file generato
 */
function upload_image($file, $thumb_prefix = null, $max_width = 128, $max_height = 128, $square = false) {
    if ($filename = upload_file($file)) {

        image_resize(UPLOADS_DIR . $filename, UPLOADS_DIR . $thumb_prefix . $filename, $max_width, $max_height, $square);
        return $filename;

        //try to create the thumbnail
        if ($thumb_prefix && !image_resize(UPLOADS_DIR . $filename, UPLOADS_DIR . $thumb_prefix . $filename, $max_width, $max_height, $square)) {
            unlink(UPLOADS_DIR . $filename);
            return false;
        }
        return $filename;
    }
}

function reduce_path($path) {
    $path = str_replace("://", "@not_replace@", $path);
    $path = preg_replace("#(/+)#", "/", $path);
    $path = preg_replace("#(/\./+)#", "/", $path);
    $path = str_replace("@not_replace@", "://", $path);

    while (preg_match('#\.\./#', $path)) {
        $path = preg_replace('#\w+/\.\./#', '', $path);
    }
    return $path;
}

//-------------------------------------------------------------
//
//					IMAGE FUNCTIONS
//
//-------------------------------------------------------------

/**
 * resize
 */
function image_resize($source, $dest, $new_width, $new_height, $quality) {

    if ($memory_limit = get_setting('memory_limit')) {
        $old_memory_limit = ini_get('memory_limit');
        ini_set('memory_limit', $memory_limit);
    }


    // increase the memory limit for resizing the image
    switch ($ext = file_ext($source)) {
        case 'jpg':
        case 'jpeg': $source_img = imagecreatefromjpeg($source);
            break;
        case 'png': $source_img = imagecreatefrompng($source);
            break;
        case 'gif': $source_img = imagecreatefromgif($source);
            break;
        default:
            return false;
    }

    list($width, $height) = getimagesize($source);

    // create a new true color image
    $dest_img = imagecreatetruecolor($new_width, $new_height);

    imagealphablending($dest_img, false);

    $origin_x = $origin_y = 0;

    $dest_canvas_color = 'ffffff';
    $dest_img_color_R = hexdec(substr($dest_canvas_color, 0, 2));
    $dest_img_color_G = hexdec(substr($dest_canvas_color, 2, 2));
    $dest_img_color_B = hexdec(substr($dest_canvas_color, 2, 2));

    // Create a new transparent color for image
    $color = imagecolorallocatealpha($dest_img, $dest_img_color_R, $dest_img_color_G, $dest_img_color_B, 127);

    // Completely fill the background of the new image with allocated color.
    imagefill($dest_img, 0, 0, $color);

    // Restore transparency blending
    imagesavealpha($dest_img, true);

    $src_x = $src_y = 0;
    $src_w = $width;
    $src_h = $height;


    $cmp_x = $width / $new_width;
    $cmp_y = $height / $new_height;

    // calculate x or y coordinate and width or height of source
    if ($cmp_x > $cmp_y) {
        $src_w = round($width / $cmp_x * $cmp_y);
        $src_x = round(($width - ($width / $cmp_x * $cmp_y)) / 2);
    } else if ($cmp_y > $cmp_x) {
        $src_h = round($height / $cmp_y * $cmp_x);
        $src_y = round(($height - ($height / $cmp_y * $cmp_x)) / 2);
    }

    imagecopyresampled($dest_img, $source_img, $origin_x, $origin_y, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h);

    switch ($ext) {
        case 'png': imagepng($dest_img, $dest, ceil($quality / 10));
            break;
        case 'gif': imagegif($dest_img, $dest, $quality);
            break;
        default: imagejpeg($dest_img, $dest, $quality);
    }

    imagedestroy($source_img);
    imagedestroy($dest_img);

    if (!$memory_limit)
        ini_set('memory_limit', $old_memory_limit);

    return true;
}

//add style sheet
function add_style($file, $dir = CSS_DIR, $url = null) {
    
}

//add javascript file
function add_script($file, $dir = JAVASCRIPT_DIR, $url = null) {
    
}

//add javascript code
function add_javascript($javascript, $onload = false) {
    
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

function objectToArray($d) {
    if (is_object($d)) {
        // Gets the properties of the given object
        // with get_object_vars function
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        /*
         * Return array converted to object
         * Using __FUNCTION__ (Magic constant)
         * for recursive call
         */
        return array_map(__FUNCTION__, $d);
    } else {
        // Return array
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
    return \H1Soft\H\Web\Config::get($_key, $_default);
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

function dump() {
    $string = '';
    foreach (func_get_args() as $value) {
        $string .= '<pre>' . h($value === NULL ? 'NULL' : (is_scalar($value) ? $value : print_r($value, TRUE))) . "</pre>\n";
    }
    return $string;
}

/**
 * 获取根目录
 * @return type
 */
function rootPath() {
    return H1Soft\H\HApplication::rootPath();
}

function varPath() {
    return H1Soft\H\HApplication::varPath();
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

function is_ascii($string) {
    return !preg_match('/[^\x00-\x7F]/S', $string);
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

/**
 * Filter a valid UTF-8 string to be file name safe.
 *
 * @param string $string to filter
 * @return string
 */
function sanitize_filename($string) {
    return sanitize($string, FALSE);
}

function dir_is_writable($dir, $chmod = 0755) {
    // If it doesn't exist, and can't be made
    if (!is_dir($dir) AND ! mkdir($dir, $chmod, TRUE))
        return FALSE;

    // If it isn't writable, and can't be made writable
    if (!is_writable($dir) AND ! chmod($dir, $chmod))
        return FALSE;

    return TRUE;
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

function setFlashMessage($name, $value) {
    $_SESSION[$name] = $value;
}

function getFlashMessage($name) {
    $message = $_SESSION[$name];
    unset($_SESSION[$name]);
    return $message;
}
