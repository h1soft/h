<?php

namespace H1Soft\H\Web\Template;

use \H1Soft\H\Web\Application;

class Twig extends \H1Soft\H\Web\AbstractTemplate {

	private $_loader;

	private $_twigEnv;

	private $tplFullPath;

	private $cachePath = false;

	public function __construct(){

		$this->setViewPath(\H1Soft\H\Web\Application::themesPath());
		$this->setTheme(\H1Soft\H\Web\Config::get('view.theme'));
		
		$this->tplFullPath = $this->getViewPath().\H1Soft\H\Web\Config::get('view.theme','default') . '/';

		if(\H1Soft\H\Web\Config::get('view.cache',false) === false) {
			$this->cachePath = false;	
		}else{
			$this->cachePath = \H1Soft\H\Web\Application::varPath() . 'cache/';	
		}
		
		if(!isset($this->_loader)){
			\Twig_Autoloader::register();
			$this->_loader = new \Twig_Loader_Filesystem($this->tplFullPath);		
			$this->_twigEnv = new \Twig_Environment($this->_loader, array(				
			    'cache' => $this->cachePath ,
			));
		
		}
	}

	public function render($filename = false, $data = true, $output = true) {

		if(is_array($data)) {
			$data = array_merge($this->data,$data);
		}
		
		//省略模版文件名
		if(is_array($filename) && is_bool($data)){
			
			if(!$data){$output = false; }
			
			$data = array_merge($this->data,$filename);
				
			$action = strtolower(rtrim(Application::app()->router()->getActionName(),'Action'));
			$filename = sprintf("%s/%s.html",strtolower(Application::app()->router()->getControllerName()),$action);
			
		}else{			
			$filename = $filename . '.html';

		}

		if ($output){				
			echo $this->_twigEnv->render($filename, $data);	
		}else{
			return $this->_twigEnv->render($filename, $data);	
			 
		}
		
	}

	public function disableCache(){
		$this->_twigEnv->disableDebug();
	}

	public function disableDebug(){
		$this->_twigEnv->setCache(false);
	}

	public function assign($_valName,$_valValue) {
		$this->$_valName = $_valValue;
	}

	public function get($_valName) {
		return isset($this->_valName) ? $this->_valName : '';
	}

	public function set($_valName,$_valValue) {
		$this->$_valName = $_valValue;
	}

}