<?php
/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace hmvc\Web;

class Auth extends \hmvc\Singleton {

    protected $id;
    protected $name;
    protected $session;

  
    protected function init() {
        $this->session = \hmvc\Web\Session::getInstance();
        
    }

    public function isAllowed($_method) {
        
    }

    public function setId($id) {
        $this->session->set('auth.id',$id);
    }

    public function getId() {
        return $this->session->get('auth.id');
    }

    public function setName($name) {
        $this->session->set('auth.name',$name);
    }

    public function getName() {
        return $this->session->get('auth.name');
    }

    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return false;
        }
        //check db
        $db = \hmvc\Db\Db::getConnection();
        $password = \hmvc\Utils\Crypt::password($password);
        
        $user = $db->getOne("admin","username='{$username}' and password='{$password}'");
        if ($user) {
            $this->setId($user['id']);
            $this->setName($user['username']);
            $db->update('admin',array('last_login'=>time(),'login_ip'=>  Application::app()->request()->ipAddress()),"id='{$user['id']}'");
//            $this->session->set('auth.id', $this->getId());
//            $this->session->set('auth.name', $this->getName());
            return true;
        }
        return false;
    }

    /**
     * 是否登录
     */
    public function isLogin() {       
        if ($this->session->get('auth.id')) {
            return true;
        }
        return false;
    }

    /**
     * 退出
     */
    public function logout() {
        $this->session->remove('auth.id');
        $this->session->remove('auth.name');
    }
    
     
    public function isAdmin() {
        if(!$this->isLogin()){
            redirect('index/login');
        }
        Session::getInstance()->set('redirect', Application::app()->request()->curUrl());        
        return true;
    }
    
    /**
     * 最小的ID 是Admin
     */
    public function isSuperAdmin() {
        if(!$this->isLogin()){
            redirect('index/login');
        }
        Session::getInstance()->set('redirect', Application::app()->request()->curUrl());
        $db = \hmvc\Db\Db::getConnection();      
        $user = $db->getRow("SELECT MIN( id ) as id FROM  `h_admin`");        
        if ($user['id'] == $this->session->get('auth.id')) {            
            return true;
        }
        return false;
    }

}
