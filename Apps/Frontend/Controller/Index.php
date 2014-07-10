<?php

namespace Apps\Frontend\Controller;

class Index extends \H1Soft\H\Web\Controller {

    public function indexAction() {        

     $this->render('index', array('test' => 'abcdefg'));
    }

    public function testAction() {

        echo SP;
    }


}
