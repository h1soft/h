<?php
/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace hmvc\ClassLoader;

class Autoloader {

    private $psr4;
    private $psr0;

    public function __construct() {
        $this->psr4 = new \hmvc\ClassLoader\Psr4ClassLoader();
    }

    public function addNameSpace($_namespace, $_path) {
        $this->psr4->addPrefix($_namespace, $_path);
    }

    public function register() {
        $this->psr4->register();
    }

}
