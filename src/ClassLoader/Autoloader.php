<?php

namespace H1Soft\H\ClassLoader;

class Autoloader {

    private $psr4;
    private $psr0;

    public function __construct() {
        $this->psr4 = new \H1Soft\H\ClassLoader\Psr4ClassLoader();
    }

    public function addNameSpace($_namespace, $_path) {
        $this->psr4->addPrefix($_namespace, $_path);
    }

    public function register() {
        $this->psr4->register();
    }

}
