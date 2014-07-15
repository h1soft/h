<?php

namespace Apps\Catalog\Controller;

class Index extends \H1Soft\H\Web\Controller {
    public function init() {
        $this->assign('Blog', \Apps\Blog\Model\Blog::getInstance());
    }

    public function indexAction() {
        

        $this->render('index');
    }
    
    public function aboutAction() {
        

        $this->render('index');
    }

    public function postAction() {
        $this->assign('post_id', $this->get('id'));
        $this->render('post');
    }

}
