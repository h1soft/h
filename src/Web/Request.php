<?php

namespace H1Soft\H\Web;


class Request extends \H1Soft\H\Collections\HArray {

	private $_query_strings = array();

	private $_segments;


	
	public function __construct(){	
	
		foreach ($_SERVER as $key => $value) {
			$this->$key = $value;
		}		
	}


	public function ipAddress(){
		return $this->REMOTE_ADDR;
	}

	public function userAgent(){
		return $this->HTTP_HOST;
	}

	
	public function language(){
		return $this->HTTP_HOST;
	}

	public function requestUri(){
		return $this->REQUEST_URI;
	}

	public function query($_key){
		if(isset($this->_query_strings[$_key])) {
			return $this->_query_strings[$_key];
		}
		return NULL;
	}


	public function segment($_key){
		if(is_array($this->_segments) && isset($this->_segments[$_key])) {
			return $this->_segments[$_key];
		}
		return NULL;
	}

	public function setSegment($_segments){
		$this->_segments = $_segments;
	}







}
