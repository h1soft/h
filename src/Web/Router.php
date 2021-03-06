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

class Router {

    protected $requestUri;
    protected $defaultController = "Index";
    protected $defaultAction = "Index";
    protected $defaultApp = "Catalog";
    protected $cInstance = NULL;
    protected $suffix = '';
    private $application;
    public $strNameSpace;
    public $errorController = "\\hmvc\\Web\\Exception\\Error";

    public function __construct() {
        $this->application = Application::app();
    }

    public function dispatch() {
        $this->initRequest();

        if (!$this->cInstance) {
            $this->_notfound();
        }

        $this->cInstance->init();

        $this->cInstance->before();

        if ($this->cInstance instanceof Controller && method_exists($this->cInstance, $this->getActionName())) {
            $reflectionMethod = new \ReflectionMethod($this->cInstance, $this->getActionName());
            if ($reflectionMethod->getNumberOfRequiredParameters() > 0) {
                $paramsWithid = $this->application->request()->getParamArrays();
                $paramsWithName = $this->application->request()->getParams();
                $ps = array();
                foreach ($reflectionMethod->getParameters() as $i => $param) {
                    $name = $param->getName();
                    if (isset($paramsWithName[$name])) {
                        if ($param->isArray())
                            $ps[] = is_array($paramsWithName[$name]) ? $paramsWithName[$name] : array($paramsWithName[$name]);
                        else if (!is_array($paramsWithName[$name]))
                            $ps[] = $paramsWithName[$name];
                    }else if (isset($paramsWithid[$i])) {
                        $ps[] = $paramsWithid[$i];
                    } else if ($param->isDefaultValueAvailable()) {
                        $ps[] = $param->getDefaultValue();
                    } else {
                        $ps[] = NULL;
                    }
                }
                $reflectionMethod->invokeArgs($this->cInstance, $ps);
//                call_user_func_array(array($this->cInstance,  $this->getActionName()), array());
            } else {
                $reflectionMethod->invoke($this->cInstance);
            }
        } else if ($this->cInstance instanceof RestController) {
            call_user_func_array(array($this->cInstance, 'dispatch'), array($this->getActionName(), $this->application->request()->getParamArrays()));
//            call_user_func_array(array($this->cInstance, $this->getActionName()), $this->application->request()->getParamArrays());
        } else {
            $this->_notfound();
        }

        $this->cInstance->after();
    }

    private function initRequest() {
        //init config
        $this->suffix = Config::get('router.suffix', '');

        $this->_rewrite();

        $this->_parseUrl();

        $this->defaultApp = Config::get('router.app', $this->defaultApp);
        $this->defaultController = isset($this->application->router['controller']) ? $this->application->router['controller'] : $this->defaultController;
        $this->defaultAction = isset($this->application->router['action']) ? $this->application->router['action'] . 'Action' : $this->defaultAction . 'Action';

        if (is_array($this->requestUri)) {
            $_param_len = count($this->requestUri);
            //alias
            $alias = \hmvc\Web\Config::get('alias');
            if (is_array($alias)) {
                $appname = $this->requestUri[0];
                if (isset($alias[$appname])) {
                    $this->defaultApp = $alias[$appname];
                    $this->requestUri[0] = $alias[$appname];
                }
            }
            switch ($_param_len) {
                case 1:
                    if (Application::checkApp($this->requestUri[0])) {
                        $this->defaultApp = ucwords($this->requestUri[0]);
                        $this->_defaultController();
                    } else if (Application::checkController(ucwords($this->requestUri[0]))) {
                        
                    } else if (Application::checkAction(strtolower($this->requestUri[0]))) {
                        
                    } else {
                        $this->_defaultController();
                        $this->application->request()->setParamArrays(array($this->requestUri[0]));
                    }

                    break;
                case 2:

                    if (Application::checkApp($this->requestUri[0])) {
                        $this->defaultApp = ucwords($this->requestUri[0]);

                        if (Application::checkController(ucwords($this->requestUri[1]))) {
                            $this->defaultController = ucwords($this->requestUri[1]);
                        } else if (Application::checkAction(ucwords($this->requestUri[1]))) {
                            $this->defaultAction = ucwords($this->requestUri[1]);
                        } else {
                            $this->_notfound();
                        }
                    } else if (Application::checkController(ucwords($this->requestUri[0]))) {
                        $this->defaultController = ucwords($this->requestUri[0]);

                        if (!Application::checkAction(strtolower($this->requestUri[1]))) {
                            $this->_notfound();
                        }
                    } else {
//                        $this->_notfound();
                        $this->_defaultController();
                        $this->application->request()->setParamArrays($this->requestUri);
                    }
                    break;
                case 3:
                    if (Application::checkApp($this->requestUri[0])) {
                        $this->defaultApp = ucwords($this->requestUri[0]);
                        if (Application::checkController(ucwords($this->requestUri[1]))) {
                            $this->defaultController = ucwords($this->requestUri[1]);
                        } else {
                            $this->_notfound();
                            return;
                        }
                        if (!Application::checkAction(ucwords($this->requestUri[2]))) {
                            $this->_notfound();
                            return;
                        }
                    } else if (Application::checkController(ucwords($this->requestUri[0]))) {
                        $this->defaultController = ucwords($this->requestUri[0]);
                        if (!Application::checkAction(ucwords($this->requestUri[1]))) {
                            $this->application->request()->setParamArrays(array($this->requestUri[1], $this->requestUri[2]));
                        } else {
                            $this->application->request()->setParamArrays(array($this->requestUri[2]));
                        }
                        return;
                    } else {
//                        $this->_notfound();
                        $this->_defaultController();
                        $this->application->request()->setParamArrays($this->requestUri);
                    }
                    break;
                case 4:
                case 5:
                case 6:
                case 7:
                case 8:
                case 9:
                case 10:
                case 11:
                case 12:
                case 13:
                case 14:
                case 15:
                case 16:
                case 17:
                case 18:
                case 19:
                case 20:
                case 21:
                case 22:
                case 23:
                case 24:
                case 25:
                case 26:
                case 27:
                case 28:
                case 29:
                case 30:
                case 31:
                case 32:
                case 33:
                case 34:
                case 35:
                case 36:
                case 37:
                case 38:
                case 39:
                case 40:
                    if (Application::checkApp($this->requestUri[0])) {
                        $this->defaultApp = ucwords($this->requestUri[0]);
                        if (Application::checkController(ucwords($this->requestUri[1]))) {
                            $this->defaultController = ucwords($this->requestUri[1]);
                        } else {
                            $this->_notfound();
                            return;
                        }
                        if (Application::checkAction(ucwords($this->requestUri[2]))) {
                            $this->prcParams(3, $_param_len);
                        } else {
                            $this->prcParams(2, $_param_len);
                        }
                    } else if (Application::checkController(ucwords($this->requestUri[0]))) {
                        $this->defaultController = ucwords($this->requestUri[0]);
                        if (!Application::checkAction(ucwords($this->requestUri[1]))) {
                            $this->prcParams(1, $_param_len);
                        } else {
                            $this->prcParams(2, $_param_len);
                        }
                    } else {
                        $this->_defaultController();
                        $this->prcParams(0, $_param_len);
//                        $this->application->request()->setParamArrays($this->requestUri);
                    }
                    break;
                default:
                    $this->_notfound();
                    break;
            }
        } else {
            $this->_defaultController();
        }
    }

    private function _parseUrl() {
        //path_info
        if (isset($_SERVER['PATH_INFO'])) {
            $_GET['r'] = $_SERVER['PATH_INFO'];
        }
        if (isset($_GET['r'])) {
            $pathinfo = pathinfo($_GET['r']);
            $acceptType = config('router.accepttype', '.html|.xml|.json');
            $this->application->request()->setAcceptType($pathinfo['extension']);
            $r = preg_replace("/{$acceptType}$/", '', $_GET['r']);
            if (function_exists('filter_var')) {
                $this->requestUri = explode('/', filter_var(trim($r, '/'), FILTER_SANITIZE_STRING));
            } else {
                $this->requestUri = explode('/', $this->xssClean(trim($r, '/')));
            }
            $this->application->request()->setSegment($this->requestUri);
        } else {
            $this->requestUri = "";
        }
    }

    private function _rewrite() {
        //读取伪静态规则
        $requestUri = $_SERVER['REQUEST_URI'];
        $rewrites = Config::get('router.rewrite');
//        $requestUri = str_replace($this->suffix, '', $requestUri);

        foreach ($rewrites as $rewrite => $value) {
            $rewrite = str_replace('/', '\/', $rewrite);

            $requestUri = str_replace(Application::basePath(), '', $requestUri);
//            echo $rewrite;
            if (preg_match("/$rewrite/i", $requestUri, $paramValues)) {
//                print_r($paramValues);
//                die;
                $paramNum = 0;
                if (preg_match_all("/{[0-9]}/", $value, $paramNumRs)) {
                    if (isset($paramNumRs[0])) {
                        $paramNum = count($paramNumRs[0]);
                    }
                }

                if (($paramNum + 1) == count($paramValues)) {
                    $paramValues = array_slice($paramValues, 1);
                } else {
                    continue;
                }

                $patterns = array();
                $replacements = array();
                foreach ($paramValues as $key => $val) {
                    $patterns[] = "/\{$key\}/";
                    $replacements[] = $val;
                }

                $_GET['r'] = preg_replace($patterns, $replacements, $value);
            }
        }
    }

    public function getControllerName() {
        return $this->defaultController;
    }

    public function getController() {
        return $this->cInstance;
    }

    /**
     * 
     * @return string ActionName
     */
    public function getActionName() {
        return $this->defaultAction;
    }

    /**
     * 
     * @return string AppName
     */
    public function getAppName() {
        return $this->defaultApp;
    }

    /**
     * 404 Page
     */
    private function _notfound() {
        $this->cInstance = new $this->errorController();
        $this->cInstance->appName = $this->defaultApp;
        $this->cInstance->controllerName = $this->defaultController;
        $this->cInstance->actionName = $this->defaultAction;
        $this->defaultController = "Error";
        $this->defaultAction = 'notfoundAction';
    }

    private function _defaultController() {
        Application::checkController(\hmvc\Web\Config::get('router.controller'), 'Index');
        Application::checkAction(\hmvc\Web\Config::get('router.action'), 'Index');
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

    public function setController($cInstance) {
        $this->cInstance = $cInstance;
    }

    /**
     * 当前Action
     * @param type $name
     */
    public function setAction($name) {
        $this->defaultAction = $name;
    }

    public function getControllerNameSpace() {
        return $this->strNameSpace;
    }

    /**
     * 处理PATH_INFO模式 参数
     * @param type $_start_index
     * @param type $_params_len
     */
    public function prcParams($_start_index, $_params_len) {

        if ($_start_index < $_params_len) {
            $ps = array_slice($this->requestUri, $_start_index);
            $params = array();
            for ($i = 0; $i < $_params_len; $i+=2) {
                $key = isset($ps[$i]) ? $ps[$i] : NULL;
                $value = isset($ps[$i + 1]) ? $ps[$i + 1] : NULL;
                if (!$key) {
                    continue;
                }
                $params[$key] = $value;
            }
            $this->application->request()->setParamArrays($ps);
            $this->application->request()->setParams($params);
            unset($ps);
        }
    }

}
