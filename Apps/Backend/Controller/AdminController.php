<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Apps\Backend\Controller;

/**
 * Description of AdminController
 *
 * @author Administrator
 */
class AdminController extends \H1Soft\H\Web\Controller {
    public function init(){
        parent::init();
        $this->isAdmin();
        \H1Soft\H\Web\Config::set('view.theme','default');
    }
}
