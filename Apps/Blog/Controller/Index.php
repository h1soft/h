<?php

namespace Apps\Blog\Controller;

class Index extends \Apps\Backend\Controller\AdminController {

    public function init() {
        if (!$this->isAdmin(true)) {
            echo json_encode(array(
                'state' => '没有登录'
            ));
        }
    }

    public function indexAction() {
        
    }

}
