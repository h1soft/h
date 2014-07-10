<?php

namespace H1Soft\H\Utils;

class File {

    /**
     * 循环创建目录
     */
    static public function mkdir($dir, $mode = 0777) {
        if (is_dir($dir) || @mkdir($dir, $mode)) {
            return true;
        }
        if (!mk_dir(\dirname($dir), $mode)) {
            return false;
        }
        return @mkdir($dir, $mode);
    }

    static public function getFileNames($dirPath = ".") {
        
        $files = array();
        
        return $files;
    }

}
