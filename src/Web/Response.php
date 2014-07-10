<?php

namespace H1Soft\H\Web;

class Response {

    public function __construct() {
        
    }
    
    public function noCache() {
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: pre-check=0, post-check=0, max-age=0');
        header ('Pragma: no-cache');
    }
    
    public function downLoad($f) {
        header('Content-Disposition: attachment; filename=' . urlencode($f));   
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');
        header('Content-Description: File Transfer');            
        header('Content-Length: ' . filesize($f));
        echo file_get_contents($f);
    }

}
