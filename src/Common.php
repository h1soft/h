<?php


/**
	 * Strip Image Tags
	 *
	 * @param	string	$str
	 * @return	string
	 */
	function strip_image_tags($str)
	{
		return preg_replace(array('#<img[\s/]+.*?src\s*=\s*["\'](.+?)["\'].*?\>#', '#<img[\s/]+.*?src\s*=\s*(.+?).*?\>#'), '\\1', $str);
	}
	/**
	 * Get GET input
	 */
	function get( $key = null, $filter = FILTER_SANITIZE_MAGIC_QUOTES ){
		if( !$key ) {
			return $filter ? filter_input_array( INPUT_GET, $filter ) : $_GET;
		}
		if( isset($_GET[$key]) ) 
		return $filter ? filter_input(INPUT_GET, $key, $filter ) : $_GET[$key];
	}


	/**
	 * Get POST input
	 */	
	function post( $key = null, $filter = FILTER_SANITIZE_MAGIC_QUOTES ){
		if( !$key )
			return $filter ? filter_input_array( INPUT_POST, $filter ) : $_POST;
		if( isset($_POST[$key]) )
			return $filter ? filter_input(INPUT_POST, $key, $filter ) : $_POST[$key];
	}



	/**
	 * Get GET_POST input
	 */
	function get_post( $key = null, $filter = FILTER_SANITIZE_MAGIC_QUOTES ){

		if( !isset($GLOBALS['_GET_POST'] ) )
			$GLOBALS['_GET_POST'] = $_GET + $_POST;
		if( !$key )
			return $filter ? filter_input_array( $GLOBALS['_GET_POST'], $filter ) : $GLOBALS['_GET_POST'];

		if( isset($GLOBALS['_GET_POST'][$key] ) )
			return $filter ? filter_var($GLOBALS['_GET_POST'][$key], $filter ) : $GLOBALS['_GET_POST'][$key];
	}



	/**
	 * Get COOKIE input
	 */
	function cookie( $key = null, $filter = FILTER_SANITIZE_MAGIC_QUOTES ){
		if( isset($_COOKIE[$key]) )
			return $filter ? filter_input(INPUT_COOKIE, $key, $filter ) : $_COOKIE[$key];
	}

	function customErrorHandle($errno, $errstr, $errfile, $errline)
	 { 
	 	if (0 == error_reporting()) 
	    { 
	    	echo 0;
	        return; 
	    } 
		 echo "<b>Error:</b> [$errno] $errstr<br />";
		 echo " Error on line $errline in $errfile<br />";
		 echo "Ending Script";
		 die();
	 }


function exceptionHandler($exception) {
	if (0 == error_reporting()) 
    { 
        return; 
    } 
    // these are our templates
    $traceline = "#%s %s(%s): %s(%s)";
    $msg = "PHP Fatal error:  Uncaught exception '%s' with message '%s' in %s:%s\nStack trace:\n%s\n  thrown in %s on line %s";

    // alter your trace as you please, here
    $trace = $exception->getTrace();

    foreach ($trace as $key => $stackPoint) {
        // I'm converting arguments to their type
        // (prevents passwords from ever getting logged as anything other than 'string')
        $trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
    }

    // build your tracelines
    $result = array();
    foreach ($trace as $key => $stackPoint) {
        $result[] = sprintf(
            $traceline,
            $key,
            $stackPoint['file'],
            $stackPoint['line'],
            $stackPoint['function'],
            implode(', ', $stackPoint['args'])
        );
    }
    // trace always ends with {main}
    $result[] = '#' . ++$key . ' {main}';

    // write tracelines into main template
    $msg = sprintf(
        $msg,
        get_class($exception),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        implode("\n", $result),
        $exception->getFile(),
        $exception->getLine()
    );

    // log or echo as you please
    error_log($msg);
}
//-------------------------------------------------------------
//
//	BENCHMARK/DEBUG FUNCTIONS
//
//-------------------------------------------------------------






	/**
	 * Save the memory used at this point
	 */
	function memory_usage_start( $memName = "execution_time" ){
		return $GLOBALS['memoryCounter'][$memName] = memory_get_usage();
	}



	/**
	 * Get the memory used
	 */
	function memory_usage( $memName = "execution_time", $byte_format = true ){
		$totMem = memory_get_usage() - $GLOBALS['memoryCounter'][ $memName ];
		return $byte_format ? byte_format($totMem) : $totMem;
	}


//-------------------------------------------------------------
//
//					 TIME FUNCTIONS
//
//-------------------------------------------------------------

	/**
	 * Start the timer
	 */
	function timer_start( $timeName = "execution_time" ){
		$stimer = explode( ' ', microtime( ) );
		$GLOBALS['timeCounter'][$timeName] = $stimer[ 1 ] + $stimer[ 0 ];
	}

	/**
	 * Get the time passed
	 */
	function timer( $timeName = "execution_time", $precision = 6 ){
	   $etimer = explode( ' ', microtime( ) );
	   $timeElapsed = $etimer[ 1 ] + $etimer[ 0 ] - $GLOBALS['timeCounter'][ $timeName ];
	   return substr( $timeElapsed, 0, $precision );
	}

	/**
	 * Transform timestamp to readable time format
	 *
	 * @param int $time unix timestamp
	 * @param string format of time (use the constant fdate_format or ftime_format)
	 */
	function time_format( $time=null, $format=DATE_FORMAT ){
		return strftime( $format, $time );
	}


	/**
	 * Transform timestamp to readable time format as elapsed time e.g. 3 days ago, or 5 minutes ago to a maximum of a week ago
	 *
	 * @param int $time unix timestamp
	 * @param string format of time (use the constant fdate_format or ftime_format)
	 */
	function time_elapsed( $time = null, $format ){

		$diff = TIME - $time;
		if( $diff < MINUTE )
			return $diff . " " . get_msg('seconds_ago');
		elseif( $diff < HOUR )
			return ceil($diff/60) . " " . get_msg('minutes_ago');
		elseif( $diff < 12*HOUR )
			return ceil($diff/3600) . " " . get_msg('hours_ago');
		elseif( $diff < DAY )
			return get_msg('today') . " " . strftime( TIME_FORMAT, $time );
		elseif( $diff < DAY*2 )
			return get_msg('yesterday') . " " . strftime( TIME_FORMAT, $time );
		elseif( $diff < WEEK )
			return ceil($diff/DAY) . " " . get_msg('days_ago') . " " . strftime( TIME_FORMAT, $time );
		else
			return strftime( $format, $time );

	}


	/**
	 * Convert seconds to hh:ii:ss
	 */
	function sec_to_hms($sec) {
		$hours = intval(intval($sec) / 3600);
		$hms  = str_pad($hours, 2, "0", STR_PAD_LEFT). ':';
		$minutes = intval(($sec / 60) % 60);
		$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
		$seconds = intval($sec % 60);
		$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
		return $hms;
	}



	/**
	 * Convert seconds to string, eg. "2 minutes", "1 hour", "16 seconds"
	 */
	function sec_to_string($sec) {
		$str = null;
		if( $hours = intval(intval($sec) / 3600) )
			$str .= $hours > 1 ? $hours . " " . get_msg('hours') : $hours . " " . get_msg('hour');
		if( $minutes = intval(($sec / 60) % 60) )
			$str .= $minutes > 1 ? $minutes . " " . get_msg('minutes') : $minutes . " " . get_msg('minute');
		if( $seconds = intval($sec % 60) )
			$str .= $seconds > 1 ? $seconds . " " . get_msg('seconds') : $seconds . " " . get_msg('second');
		return $str;
	}


//-------------------------------------------------------------
//
//					 STRING FUNCTIONS
//
//-------------------------------------------------------------

	/**
	 * Cut html
	 * text, length, ending, tag allowed, $remove_image true / false, $exact true=the ending words are not cutted
	 * Note: I get this functions from web but I don't remember the source. It should be from cakePHP.
	 */
	function cut_html( $text, $length = 100, $ending = '...', $allowed_tags = '<b><i>', $remove_image = true, $exact = false ) {

		if( !$remove_image )
			$allowed_tags .= '<img>';

		$text = strip_tags($text, $allowed_tags );
		if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length)
			return $text;

		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
		$total_length = strlen($ending);
		$open_tags = array();
		$truncate = '';
		foreach ($lines as $line_matchings) {
			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1])) {
				// if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					// do nothing
				// if tag is a closing tag (f.e. </b>)
				} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false) {
						unset($open_tags[$pos]);
					}
				// if tag is an opening tag (f.e. <b>)
				} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, strtolower($tag_matchings[1]));
				}
				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}
			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if ($total_length+$content_length> $length) {
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1]+1-$entities_length <= $left) {
							$left--;
							$entities_length += strlen($entity[0]);
						} else {
							// no more characters left
							break;
						}
					}
				}
				$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			} else {
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}
			// if the maximum length is reached, get off the loop
			if($total_length>= $length)
				break;
		}

		// don't cut the last words
		if (!$exact && $spacepos = strrpos($truncate, ' ') )
			$truncate = substr($truncate, 0, $spacepos);

		$truncate .= $ending;
		foreach ($open_tags as $tag)
			$truncate .= '</' . $tag . '>';

		return $truncate;
	}



	/**
	 * Cut string and add ... at the end
	 * useful to cut noHTML text, for example to cut the title of an article
	 */
	function cut( $string, $length, $ending = "..." ){
		if( strlen( $string ) > $length )
			return $string = substr( $string, 0, $length ) . $ending;
		else
			return $string = substr( $string, 0, $length );
	}




	/**
	 * Return a random string
	 */
	function rand_str($length = 5, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890'){
		$chars_length = (strlen($chars) - 1);
		$string = $chars{rand(0, $chars_length)};
		for ($i = 1; $i < $length; $i = strlen($string)){
			$r = $chars{rand(0, $chars_length)};
			if ($r != $string{$i - 1}) $string .=  $r;
		}
		return $string;
	}



//-------------------------------------------------------------
//
//					NUMBER FUNCTIONS
//
//-------------------------------------------------------------

	/**
	 * Convert byte to more readable format, like "1 KB" instead of "1024".
	 * cut_zero, remove the 0 after comma ex:  10,00 => 10	  14,30 => 14,3
	 */
	function byte_format( $size ){
		if( $size > 0 ){
			$unim = array("B","KB","MB","GB","TB","PB");
			for( $i=0; $size >= 1024; $i++ )
				$size = $size / 1024;
			return number_format($size,$i?2:0, DEC_POINT, THOUSANDS_SEP )." ".$unim[$i];
		}
	}


	/**
	 * Format the money in the current format. If add_currency is true the function add the currency configured into the language
	 */
	function format_money( $number, $add_currency = false ){
		return ( $add_currency && CURRENCY_SIDE == 0 ? CURRENCY . " " : "" ) . number_format($number,2,DEC_POINT,THOUSANDS_SEP) . ( $add_currency && CURRENCY_SIDE == 1 ? " " . CURRENCY : "" );
	}




//-------------------------------------------------------------
//
//					EMAIL FUNCTIONS
//
//-------------------------------------------------------------


	/**
	 * Return true if the email is valid else false
	 */
	function is_email( $email ){
		return preg_match('|^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$|i', $email );
	}

	/**
	 * Send an email
	 * @param $to
	 */
	function email_send( $to, $subject, $body, $from = null, $from_name = null, $attachment = null, $embed_images = false ){

			// TO DO: use the email class

	}


	/**
	 * Send an email with selected template
	 */
	function email_tpl_send( $template = "generic/email", $to, $subject, $body, $from = null, $from_name = null, $attachment = null){
		$tpl = new TPL();
		$tpl->assign("body", $body );
		$body = $tpl->draw( $template, true );
		return emailSend( $to, $subject, $body, $from, $from_name, $attachment );
	}



//-------------------------------------------------------------
//
//					FILE FUNCTIONS
//
//-------------------------------------------------------------

	/**
	 * Return list of dir and files without . ..
	 *
	 * @param string $d directory
	 */
	function dir_scan($dir){
		if( is_dir($dir) && $dh = opendir($dir) ){ $f=array(); while ($fn = readdir($dh)) { if($fn!='.'&&$fn!='..') $f[] = $fn; } return $f; }
	}

	/**
	 * Get the list of files filtered by extension ($ext)
	 *
	 * @param string $d directory
	 * @param string $ext extension filter, example ".jpg"
	 */
	function file_list($dir,$ext=null){
		if( $dl=dir_scan($dir) ){ $l=array(); foreach( $dl as $f ) if( is_file($dir.'/'.$f) && ($ext?preg_match('/\.'.$ext.'$/',$f):1) ) $l[]=$f; return $l; }
	}



	/**
	 * Get the list of directory
	 *
	 * @param string $dir directory
	 */
	function dir_list($dir){
		if( $dl=dir_scan($dir) ){ $l=array(); foreach($dl as $f)if(is_dir($dir.'/'.$f))$l[]=$f; return $l; }
	}


	/**
	 * File extension
	 *
	 * @param string $file filename
	 */
	function file_ext($filename){
		return substr(strrchr($filename, '.'),1);
	}



	/**
	 * Get the name without extension
	 *
	 * @param string $f filename
	 */
	function file_name($filename){
		if( ($filename = basename($filename) ) && ( $dot_pos = strrpos( $filename , "." ) ) )
			return substr( $filename, 0, $dot_pos );
	}



	/**
	 * Delete dir and contents
	 *
	 * @param string $dir directory
	 */
	function dir_del($dir) {
		if( $l=dir_scan($dir) ){ foreach($l as $f) if (is_dir($dir."/".$f)) dir_del($dir.'/'.$f);	else unlink($dir."/".$f);	return rmdir($dir); }
	}

	/**
	 * Copy all the content of a directory
	 *
	 * @param string $s source directory
	 * @param string $d destination directory
	 */
	function dir_copy( $source, $dest) {
		if (is_file($source)){
			copy($source, $dest);
			chmod($dest, fileperms($source) );
		}
		else{
			mkdir( $dest, 0777 );
			if( $l=dir_scan($source) ){ foreach( $l as $f ) dir_copy("$source/$f", "$dest/$f"); }
		}
	}



	/**
	 * Upload one file selected with $file. Use it when you pass only one file with a form.
	 * The file is saved into UPLOADS_DIR, the name created as "md5(time()) . file_extension"
	 * it return the filename
	 *
	 * @return string uploaded filename
	 */
	function upload_file($file){
		if( $_FILES[$file]["tmp_name"] ){
			$upload_filepath = UPLOADS_DIR . ( $filename = md5(time()).".".( strtolower( file_ext($_FILES[$file]['name'] ) ) ) );
			move_uploaded_file( $_FILES[$file]["tmp_name"], $upload_filepath );
			return $filename;
		}
	}


	/**
	 * Upload an image file and create a thumbnail
	 *
	 * @param string $file
	 * @param string $UPLOADS_DIR
	 * @param string $thumb_prefix Prefisso della thumbnail
	 * @param int $max_width
	 * @param int $max_height
	 * @param bool $square
	 * @return string Nome del file generato
	 */
	function upload_image( $file, $thumb_prefix = null, $max_width = 128, $max_height = 128, $square = false ){
		if( $filename = upload_file( $file ) ){

			image_resize( UPLOADS_DIR . $filename,  UPLOADS_DIR . $thumb_prefix . $filename, $max_width, $max_height, $square );
			return $filename;

			//try to create the thumbnail
			if( $thumb_prefix && !image_resize( UPLOADS_DIR . $filename,  UPLOADS_DIR . $thumb_prefix . $filename, $max_width, $max_height, $square ) ){
				unlink( UPLOADS_DIR . $filename );
				return false;
			}
			return $filename;
		}
	}



        function reduce_path( $path ){
                $path = str_replace( "://", "@not_replace@", $path );
                $path = preg_replace( "#(/+)#", "/", $path );
                $path = preg_replace( "#(/\./+)#", "/", $path );
                $path = str_replace( "@not_replace@", "://", $path );

                while( preg_match( '#\.\./#', $path ) ){
                    $path = preg_replace('#\w+/\.\./#', '', $path );
                }
                return $path;
        }


//-------------------------------------------------------------
//
//					IMAGE FUNCTIONS
//
//-------------------------------------------------------------



        /**
        * resize
        */
        function image_resize($source, $dest, $new_width, $new_height, $quality) {

            if( $memory_limit = get_setting('memory_limit') ){
                $old_memory_limit = ini_get('memory_limit');
                ini_set('memory_limit', $memory_limit );
            }


            // increase the memory limit for resizing the image
            switch ($ext = file_ext($source)) {
                case 'jpg':
                case 'jpeg': $source_img = imagecreatefromjpeg($source);
                    break;
                case 'png': $source_img = imagecreatefrompng($source);
                    break;
                case 'gif': $source_img = imagecreatefromgif($source);
                    break;
                default:
                    return false;
            }

            list($width, $height) = getimagesize($source);

            // create a new true color image
            $dest_img = imagecreatetruecolor($new_width, $new_height);

            imagealphablending($dest_img, false);

            $origin_x = $origin_y = 0;

            $dest_canvas_color = 'ffffff';
            $dest_img_color_R = hexdec(substr($dest_canvas_color, 0, 2));
            $dest_img_color_G = hexdec(substr($dest_canvas_color, 2, 2));
            $dest_img_color_B = hexdec(substr($dest_canvas_color, 2, 2));

            // Create a new transparent color for image
            $color = imagecolorallocatealpha($dest_img, $dest_img_color_R, $dest_img_color_G, $dest_img_color_B, 127);

            // Completely fill the background of the new image with allocated color.
            imagefill($dest_img, 0, 0, $color);

            // Restore transparency blending
            imagesavealpha($dest_img, true);

            $src_x = $src_y = 0;
            $src_w = $width;
            $src_h = $height;


            $cmp_x = $width / $new_width;
            $cmp_y = $height / $new_height;

            // calculate x or y coordinate and width or height of source
            if ($cmp_x > $cmp_y) {
                $src_w = round($width / $cmp_x * $cmp_y);
                $src_x = round(($width - ($width / $cmp_x * $cmp_y)) / 2);
            } else if ($cmp_y > $cmp_x) {
                $src_h = round($height / $cmp_y * $cmp_x);
                $src_y = round(($height - ($height / $cmp_y * $cmp_x)) / 2);
            }

            imagecopyresampled($dest_img, $source_img, $origin_x, $origin_y, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h);

            switch ($ext) {
                case 'png': imagepng($dest_img, $dest, ceil($quality / 10));
                    break;
                case 'gif': imagegif($dest_img, $dest, $quality);
                    break;
                default: imagejpeg($dest_img, $dest, $quality);
            }

            imagedestroy($source_img);
            imagedestroy($dest_img);

            if( !$memory_limit )
                ini_set( 'memory_limit', $old_memory_limit );

            return true;
        }




//-------------------------------------------------------------
//
//					HOOKS FUNCTIONS
//
//-------------------------------------------------------------





//-------------------------------------------------------------
//
//					Settings
//
//-------------------------------------------------------------

	function get_setting( $key = null ){
		global $settings;
		if( !$key )
			return $settings;
		if( isset( $settings[$key] ) )
			return $settings[$key];
	}



//-------------------------------------------------------------
//
//					 Language
//
//-------------------------------------------------------------

	/**
	 * Get the translated string if in language dictionary, return the string if not
	 *
	 * @param string $msg Msg to translate
	 * @param string $modifier You can choose a modifier from: strtoupper, strtolower, ucwords, ucfirst
	 * @return translated string
	 */
	function get_msg( $msg, $modifier = null ){
		global $lang;
		if( isset($lang[$msg]))
			$msg = $lang[$msg];
		return $modifier ? $modifier($msg) : $msg;
	}

	function get_lang(){
		return LANG_ID;
	}

	function load_lang( $file ){
		require_once LANGUAGE_DIR . get_lang() . "/" . $file . ".php";
	}
    
        function get_installed_language(){
            return dir_list(LANGUAGE_DIR);
        }

	// draw a message styled as SUCCESS, WARNING, ERROR or INFO. See .box in style.css for the style
	function draw_msg( $msg, $type = SUCCESS, $close = false, $autoclose = 0 ){
		add_script("jquery.min.js", JQUERY_DIR );
		add_style( "box.css", CSS_DIR );
		$box_id = rand(0,9999) . "_" . time();
		if( $close )
			$close = '<div class="close"><a onclick="$(\'#box_'.$box_id.'\').slideUp();">x</a></div>';
		if($autoclose)
			add_javascript( 'setTimeout("$(\'#box_'.$box_id.'\').slideUp();", "'.($autoclose*1000).'")', $onload=true );

		switch( $type ){
			case SUCCESS: 	$class = 'success'; break;
			case WARNING: 	$class = 'warning'; break;
			case ERROR:  	$class = 'error'; break;
			case INFO: 	$class = 'info'; break;
		}

		// style defined in style.css as .box
		return '<div class="box box_'.$class.'" id="box_'.$box_id.'">'.$close.$msg.'</div>';
	}



//-------------------------------------------------------------
//
//					 Javascript & CSS
//
//-------------------------------------------------------------

	//style sheet and javascript
	global $style, $script, $javascript, $javascript_onload;
	$style = $script = array();
	$javascript = $javascript_onload = "";


	//add style sheet
	function add_style( $file, $dir = CSS_DIR, $url = null ){
		if( !$url )
			$url = URL . $dir;
		$GLOBALS['style'][$dir . $file] = $url . $file;
	}

	//add javascript file
	function add_script( $file, $dir = JAVASCRIPT_DIR, $url = null ){
		if( !$url )
			$url = URL . $dir;
		$GLOBALS['script'][$dir . $file] = $url . $file;
	}

	//add javascript code
	function add_javascript( $javascript, $onload = false ){
		if( !$onload )
			$GLOBALS['javascript'] .= "\n".$javascript."\n";
		else
			$GLOBALS['javascript_onload'] .= "\n".$javascript."\n";
	}

	/**
	 * get javascript
	 */
	function get_javascript( $compression = false ){
		global $script, $javascript, $javascript_onload;
		$html = "";
		if( $script ){

			if( $compression ){
				$js_file = "";
				foreach( $script as $file => $url)
					$js_file .= "$url,";
				$html = '<script src="/js.php?'.$js_file.'" type="text/javascript"></script>' . "\n";

			}
			else{
				foreach( $script as $s )
					$html .= '<script src="'.$s.'" type="text/javascript"></script>' . "\n";
			}

		}
		if( $javascript_onload ) $javascript .=  "\n" . "$(function(){" . "\n" . "	$javascript_onload" . "\n" . "});" . "\n";
		if( $javascript ) $html .= "<script type=\"text/javascript\">" . "\n" .$javascript . "\n" . "</script>";
		return $html;
	}

	/**
	 * get the style
	 */
	function get_style( $compression = false ){
		global $style;
		$html = "";

		if( $style ){

			if( $compression ){
				$css_file = "";
				foreach( $style as $file => $url)
					$css_file .= "$url,";
				$html = '	<link rel="stylesheet" href="/css.php?'.$css_file.'" type="text/css" />' . "\n";
			}
			else{
				foreach( $style as $file => $url)
					$html .= '	<link rel="stylesheet" href="'.$url.'" type="text/css" />' . "\n";
			}

		}

		return $html;

	}


//-------------------------------------------------------------
//
//					LOCALIZATION FUNCTIONS
//
//-------------------------------------------------------------

	function get_ip(){
		if( !defined("IP") ){
			$ip = getenv( "HTTP_X_FORWARDED_FOR" ) ? getenv( "HTTP_X_FORWARDED_FOR" ) : getenv( "REMOTE_ADDR" );
			if( !preg_match("^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}^", $ip ) ) $ip = null;
			define( "IP", $ip );
		}
		return IP;
	}



	/**
	 * Return true if $ip is a valid ip
	 */
	function is_ip($ip){
		return preg_match("^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}^", $ip );
	}



	/**
	 * Return the array with all geolocation info of user selected by IP
	 * ip = your IP
	 * assoc = true if you want the result as array
	 */
	if( !defined("IPINFODB_KEY" ) )
		define( "IPINFODB_KEY", "YOUR_KEY" );
	function ip_to_location( $ip = IP, $assoc = true ){
		// if ip is correct and it can access to the URL it will get the array with all the user localization info
		if( is_ip( $ip ) && file_exists( $url = "http://api.ipinfodb.com/v2/ip_query.php?key=".IPINFODB_KEY."&ip={$ip}&output=json&timezone=true" ) && ($json = file_get_contents( $url ) ) )
				return json_decode( $json, $assoc );
	}


	/**
	 * Return the browser information of the logged user
	 */
	function get_browser_info(){

		if( !isset( $GLOBALS['rain_browser_info'] ) ){
			$known = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape', 'konqueror', 'gecko');
			preg_match( '#(' . join('|', $known) . ')[/ ]+([0-9]+(?:\.[0-9]+)?)#', strtolower($_SERVER['HTTP_USER_AGENT']), $br );
			preg_match_all( '#\((.*?);#', $_SERVER['HTTP_USER_AGENT'], $os );

			global $rain_browser_info;
			$rain_browser_info['lang_id'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
			$rain_browser_info['browser'] = isset( $br[1][1] ) ? $br[1][1] : null;
			$rain_browser_info['version'] = isset( $br[2][1] ) ? $br[2][1] : null;
			$rain_browser_info['os'] = $od[1][0];

		}
		return $GLOBALS['rain_browser_info'];


	}


        
    //-------------------------------------------------------------
    //
    //                    URL FUNCTIONS
    //
    //-------------------------------------------------------------

    // alias for redirect
    function reindex( $url ){
        redirect( $url );
    }
    
    function redirect( $url ){
        header( "location: $url" );
    }


// -- end



	function arrayToObject($d) {
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return (object) array_map(__FUNCTION__, $d);
		}
		else {
			// Return object
			return $d;
		}
	}


		function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}

		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__FUNCTION__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}

/**
 * Attach (or remove) multiple callbacks to an event and trigger those callbacks when that event is called.
 *
 * @param string $event name
 * @param mixed $value the optional value to pass to each callback
 * @param mixed $callback the method or function to call - FALSE to remove all callbacks for event
 */
function event($event, $value = NULL, $callback = NULL)
{
	static $events;

	// Adding or removing a callback?
	if($callback !== NULL)
	{
		if($callback)
		{
			$events[$event][] = $callback;
		}
		else
		{
			unset($events[$event]);
		}
	}
	elseif(isset($events[$event])) // Fire a callback
	{
		foreach($events[$event] as $function)
		{
			$value = call_user_func($function, $value);
		}
		return $value;
	}
}


/**
 * Fetch a config value from a module configuration file
 *
 * @param string $file name of the config
 * @param boolean $clear to clear the config object
 * @return object
 */
function config($file = 'Config', $clear = FALSE)
{
	static $configs = array();

	if($clear)
	{
		unset($configs[$file]);
		return;
	}

	if(empty($configs[$file]))
	{
		//$configs[$file] = new \Micro\Config($file);
		require(SP . 'Config/' . $file . EXT);
		$configs[$file] = (object) $config;
		//print dump($configs);
	}

	return $configs[$file];
}


/**
 * Return an HTML safe dump of the given variable(s) surrounded by "pre" tags.
 * You can pass any number of variables (of any type) to this function.
 *
 * @param mixed
 * @return string
 */
function dump()
{
	$string = '';
	foreach(func_get_args() as $value)
	{
		$string .= '<pre>' . h($value === NULL ? 'NULL' : (is_scalar($value) ? $value : print_r($value, TRUE))) . "</pre>\n";
	}
	return $string;
}




/**
 * Safely fetch a $_SESSION value, defaulting to the value provided if the key is
 * not found.
 *
 * @param string $k the post key
 * @param mixed $d the default value if key is not found
 * @return mixed
 */
function session($k, $d = NULL)
{
	return isset($_SESSION[$k]) ? $_SESSION[$k] : $d;
}


/**
 * Create a random 32 character MD5 token
 *
 * @return string
 */
function token()
{
	return md5(str_shuffle(chr(mt_rand(32, 126)) . uniqid() . microtime(TRUE)));
}


/**
 * Write to the application log file using error_log
 *
 * @param string $message to save
 * @return bool
 */
function log_message($message)
{
	$path = SP . 'Storage/Log/' . date('Y-m-d') . '.log';

	// Append date and IP to log message
	return error_log(date('H:i:s ') . getenv('REMOTE_ADDR') . " $message\n", 3, $path);
}



/*
 * Return the full URL to a path on this site or another.
 *
 * @param string $uri may contain another sites TLD
 * @return string
 *
function site_url($uri = NULL)
{
	return (strpos($uri, '://') === FALSE ? \Micro\URL::get() : '') . ltrim($uri, '/');
}
*/

/**
 * Return the full URL to a location on this site
 *
 * @param string $path to use or FALSE for current path
 * @param array $params to append to URL
 * @return string
 */
function site_url($path = NULL, array $params = NULL)
{
	// In PHP 5.4, http_build_query will support RFC 3986
	return DOMAIN . ($path ? '/'. trim($path, '/') : PATH)
		. ($params ? '?'. str_replace('+', '%20', http_build_query($params, TRUE, '&')) : '');
}


/**
 * Return the current URL with path and query params
 *
 * @return string
 *
function current_url()
{
	return DOMAIN . getenv('REQUEST_URI');
}
*/

/**
 * Convert a string from one encoding to another encoding
 * and remove invalid bytes sequences.
 *
 * @param string $string to convert
 * @param string $to encoding you want the string in
 * @param string $from encoding that string is in
 * @return string
 */
function encode($string, $to = 'UTF-8', $from = 'UTF-8')
{
	// ASCII is already valid UTF-8
	if($to == 'UTF-8' AND is_ascii($string))
	{
		return $string;
	}

	// Convert the string
	return @iconv($from, $to . '//TRANSLIT//IGNORE', $string);
}


/**
 * Tests whether a string contains only 7bit ASCII characters.
 *
 * @param string $string to check
 * @return bool
 */
function is_ascii($string)
{
	return ! preg_match('/[^\x00-\x7F]/S', $string);
}


/**
 * Encode a string so it is safe to pass through the URL
 *
 * @param string $string to encode
 * @return string
 */
function base64_url_encode($string = NULL)
{
	return strtr(base64_encode($string), '+/=', '-_~');
}


/**
 * Decode a string passed through the URL
 *
 * @param string $string to decode
 * @return string
 */
function base64_url_decode($string = NULL)
{
	return base64_decode(strtr($string, '-_~', '+/='));
}


/**
 * Convert special characters to HTML safe entities.
 *
 * @param string $string to encode
 * @return string
 */
function h($string)
{
	return htmlspecialchars($string, ENT_QUOTES, 'utf-8');
}


/**
 * Filter a valid UTF-8 string so that it contains only words, numbers,
 * dashes, underscores, periods, and spaces - all of which are safe
 * characters to use in file names, URI, XML, JSON, and (X)HTML.
 *
 * @param string $string to clean
 * @param bool $spaces TRUE to allow spaces
 * @return string
 */
function sanitize($string, $spaces = TRUE)
{
	$search = array(
		'/[^\w\-\. ]+/u',			// Remove non safe characters
		'/\s\s+/',					// Remove extra whitespace
		'/\.\.+/', '/--+/', '/__+/'	// Remove duplicate symbols
	);

	$string = preg_replace($search, array(' ', ' ', '.', '-', '_'), $string);

	if( ! $spaces)
	{
		$string = preg_replace('/--+/', '-', str_replace(' ', '-', $string));
	}

	return trim($string, '-._ ');
}


/**
 * Create a SEO friendly URL string from a valid UTF-8 string.
 *
 * @param string $string to filter
 * @return string
 */
function sanitize_url($string)
{
	return urlencode(mb_strtolower(sanitize($string, FALSE)));
}


/**
 * Filter a valid UTF-8 string to be file name safe.
 *
 * @param string $string to filter
 * @return string
 */
function sanitize_filename($string)
{
	return sanitize($string, FALSE);
}


/**
 * Return a SQLite/MySQL/PostgreSQL datetime string
 *
 * @param int $timestamp
 */
function sql_date($timestamp = NULL)
{
	return date('Y-m-d H:i:s', $timestamp ?: time());
}


/**
 * Make a request to the given URL using cURL.
 *
 * @param string $url to request
 * @param array $options for cURL object
 * @return object
 */
function curl_request($url, array $options = NULL)
{
	$ch = curl_init($url);

	$defaults = array(
		CURLOPT_HEADER => 0,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_TIMEOUT => 5,
	);

	// Connection options override defaults if given
	curl_setopt_array($ch, (array) $options + $defaults);

	// Create a response object
	$object = new stdClass;

	// Get additional request info
	$object->response = curl_exec($ch);
	$object->error_code = curl_errno($ch);
	$object->error = curl_error($ch);
	$object->info = curl_getinfo($ch);

	curl_close($ch);

	return $object;
}


/**
 * Create a RecursiveDirectoryIterator object
 *
 * @param string $dir the directory to load
 * @param boolean $recursive to include subfolders
 * @return object
 */
function directory($dir, $recursive = TRUE)
{
	$i = new \RecursiveDirectoryIterator($dir);

	if( ! $recursive) return $i;

	return new \RecursiveIteratorIterator($i, \RecursiveIteratorIterator::SELF_FIRST);
}


/**
 * Make sure that a directory exists and is writable by the current PHP process.
 *
 * @param string $dir the directory to load
 * @param string $chmod value as octal
 * @return boolean
 */
function directory_is_writable($dir, $chmod = 0755)
{
	// If it doesn't exist, and can't be made
	if(! is_dir($dir) AND ! mkdir($dir, $chmod, TRUE)) return FALSE;

	// If it isn't writable, and can't be made writable
	if(! is_writable($dir) AND !chmod($dir, $chmod)) return FALSE;

	return TRUE;
}


/**
 * Convert any given variable into a SimpleXML object
 *
 * @param mixed $object variable object to convert
 * @param string $root root element name
 * @param object $xml xml object
 * @param string $unknown element name for numeric keys
 * @param string $doctype XML doctype
 */
function to_xml($object, $root = 'data', $xml = NULL, $unknown = 'element', $doctype = "<?xml version = '1.0' encoding = 'utf-8'?>")
{
	if(is_null($xml))
	{
		$xml = simplexml_load_string("$doctype<$root/>");
	}

	foreach((array) $object as $k => $v)
	{
		if(is_int($k))
		{
			$k = $unknown;
		}

		if(is_scalar($v))
		{
			$xml->addChild($k, h($v));
		}
		else
		{
			$v = (array) $v;
			$node = array_diff_key($v, array_keys(array_keys($v))) ? $xml->addChild($k) : $xml;
			self::from($v, $k, $node);
		}
	}

	return $xml;
}



/**
 * Color output text for the CLI
 *
 * @param string $text to color
 * @param string $color of text
 * @param string $background color
 */
function colorize($text, $color, $bold = FALSE)
{
	// Standard CLI colors
	$colors = array_flip(array(30 => 'gray', 'red', 'green', 'yellow', 'blue', 'purple', 'cyan', 'white', 'black'));

	// Escape string with color information
	return"\033[" . ($bold ? '1' : '0') . ';' . $colors[$color] . "m$text\033[0m";
}
