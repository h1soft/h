<?php

namespace Apps\UEditor\Controller;

class Index extends \Apps\Backend\Controller\AdminController {

    public function init() {
        if (!$this->isAdmin(true)) {
            echo json_encode(array(
                'state' => '没有登录'
            ));
        }
    }

    public function indexAction() {
        date_default_timezone_set("Asia/Shanghai");
        error_reporting(E_ERROR);
        header("Content-Type: text/html; charset=utf-8");
        $CONFIG = objectToArray(new \Apps\UEditor\Helper\Config());
        if (is_file(\H1Soft\H\Web\Application::etcPath() . 'ueditor.php')) {
            $etcConfig = include \H1Soft\H\Web\Application::etcPath() . 'ueditor.php';
            if (is_array($etcConfig)) {
                $CONFIG = array_merge($CONFIG, $etcConfig);
            }
        }
        $action = $this->get('action');
        $curPath = dirname(dirname(__FILE__));
        switch ($action) {
            case 'config':
                $result = json_encode($CONFIG);
                break;

            /* 上传图片 */
            case 'uploadimage':
            /* 上传涂鸦 */
            case 'uploadscrawl':
            /* 上传视频 */
            case 'uploadvideo':
            /* 上传文件 */
            case 'uploadfile':
                $result = include("$curPath/Classes/action_upload.php");
                break;

            /* 列出图片 */
            case 'listimage':
                $result = include("$curPath/Classes/action_list.php");
                break;
            /* 列出文件 */
            case 'listfile':
                $result = include("$curPath/Classes/action_list.php");
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $result = include("$curPath/Classes/action_crawler.php");
                break;

            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state' => 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
    }

}
