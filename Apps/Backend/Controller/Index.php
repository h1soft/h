<?php

namespace Apps\Backend\Controller;

class Index extends \H1Soft\H\Web\Controller {

    function indexAction() {  
        $this->isAdmin();
        //php info
        $this->assign('PHP_VERSION', PHP_VERSION);
        $this->assign('UPLOAD_MAX_FILESIZE', ini_get('upload_max_filesize'));
        
        //version
        $this->assign('HVERSION', HVERSION);
        
        //check db        
        $this->assign('MYSQLI', function_exists('mysqli_connect'));
        
        //gd info
        $this->assign('GD_INFO', gd_info());        
        $this->assign('GD_IMGTYPE', $this->getSupportedImageTypes());
        
        $this->render('admin/index');
    }

    function logoutAction() {
        \H1Soft\H\Web\Auth::getInstance()->logout();
        $this->redirect('index/login');
    }

    function loginAction() {
        $auth = \H1Soft\H\Web\Auth::getInstance();

        if ($this->isPost()) {
            $username = post('username');
            $password = post('password');

            if ($auth->login($username, $password)) {
                $this->setFlashMessage("登录成功");
                $this->assign('lflag', 0);

                $this->redirect('index/index');
            } else {
                $this->setFlashMessage("登录失败");
                $this->assign('lflag', 1);
            }
        }
        $this->render('admin/login');
    }

    function getSupportedImageTypes() {
        $aSupportedTypes = array();

        $aPossibleImageTypeBits = array(
            IMG_GIF => 'GIF',
            IMG_JPG => 'JPG',
            IMG_PNG => 'PNG',
            IMG_WBMP => 'WBMP'
        );

        foreach ($aPossibleImageTypeBits as $iImageTypeBits => $sImageTypeString) {
            if (imagetypes() & $iImageTypeBits) {
                $aSupportedTypes[] = $sImageTypeString;
            }
        }

        return $aSupportedTypes;
    }

}
