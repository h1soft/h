<?php

namespace H1Soft\H\Web;


abstract class Controller extends \H1Soft\H\Collections\HArray {	

    private $_viewScripts = array();

    private $_viewCss = array();

    //template engine
    private $_engine;

 	public function __construct() {}

    public function init(){}

    public function before(){}

    public function indexAction(){}


    public function after(){ }


    public function render($tplFileName=false,$data=true,$output=true){
        try {
            $this->_initTemplateEngine();
            return $this->_engine->render($tplFileName,$data,$output);    
        } catch (Twig_Error_Loader $e) {
            print_r($e);
        }        
    }

    public function addJs($filename){
        array_push($this->_viewScripts, $filename);
        
    }

    public function addCss($filename){
        array_push($this->_viewCss, $filename);
        
    }


    private function _initTemplateEngine(){                
        if(\H1Soft\H\Web\Config::get('view.template')){
            $engine = sprintf("\\H1Soft\\H\\Web\\Template\\%s",\H1Soft\H\Web\Config::get('view.template'));

            if(!class_exists($engine)){
                $engine = "\\H1Soft\\H\\Web\\Template\\View";
            }
            $this->_engine = new $engine();
           
        }

    }

    public function getRender() {
        return $this->_engine;
    }

    public function setRender($_engine) {
        return $this->_engine = $_engine;
    }


}
