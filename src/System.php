<?php

namespace H1Soft\H;


class System extends Singleton {

	//Windows
	public static function isWin(){
		
		if (isset($_SERVER['WINDIR'])){
			return true;
		}

		return false;
	}

}
