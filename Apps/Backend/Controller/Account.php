<?php

namespace Apps\Backend\Controller;

/**
 * 账户管理
 */
class Account extends \H1Soft\H\Web\Controller {

    public function indexAction() {
        
    }

    public function settingAction() {
        $this->isAdmin();
        $this->assign('menu_setting', 1);
//        \H1Soft\H\Web\Config::set('view.theme','hello');
//        echo \H1Soft\H\Web\Config::get('view.theme');
        $adminHelper = new \Apps\Backend\Helper\Account();
        $auth = \H1Soft\H\Web\Auth::getInstance();
        $admin = $adminHelper->getAccount($auth->getId());
        if ($this->isPost()) {
            $username = $this->post('username');
            $oldpasswd = $this->post('oldpasswd');
            $newpasswd = $this->post('newpasswd');
            $renewpasswd = $this->post('renewpasswd');
            if ($oldpasswd && \H1Soft\H\Utils\Crypt::password($oldpasswd) != $admin['password']) {
                $this->showFlashMessage("旧密码错误");
            } else if (strlen($newpasswd) < 6) {
                $this->showFlashMessage("密码不能小于6位");
            } else if ($newpasswd != $renewpasswd) {
                $this->showFlashMessage("两次输入的密码不一样");
            }else{
                $admin['password'] = \H1Soft\H\Utils\Crypt::password($newpasswd);
            }

            

            if ($auth->getName() != $username) {
                //check user
                if ($adminHelper->checkUsername($username)) {
                    $this->showFlashMessage("用户名被占用");
                } else if (strlen($username) < 3) {
                    $this->showFlashMessage("用户名不能小于3个字符");
                } else {
                    $admin['username'] = $username;
                }
            }
            
            //更新
            $this->db()->update('admin',$admin,"id='{$auth->getId()}'");
             $this->showFlashMessage("修改成功",H_SUCCESS);
        }

        $this->render('admin/account_setting', array('item' => $admin));
    }

}
