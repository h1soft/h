<?php


namespace Apps\Backend\Controller;


class Page extends \H1Soft\H\Web\Controller {
    public function indexAction() {
        print_r($this->req()->getParams());
//        echo url_to('2014/5/6/test');
//        print_r($_SERVER);
    }
    
    public function addAction() {
        
    }
    
    public function editAction() {
        
    }
}
