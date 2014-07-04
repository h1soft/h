<?php

namespace Apps\Frontend\Controller;

class Index extends \H1Soft\H\Web\Controller {

    public function indexAction() {
        echo \H1Soft\H\Web\Application::app()->request()->segment(0);


        // $this->render(array('test'=>'abcdefg'));
    }

    public function testAction() {



        $this->render('index/index', array('test' => 'abcdefg'));
    }

    public function tsetAction() {



        // $this->render('index/index',array('test'=>'abcdefg'));
    }

}
