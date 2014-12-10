<?php
/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace hmvc\Utils;

class File {

    /**
     * 循环创建目录
     * @param type $dir
     * @param type $mode
     * @return boolean
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

    static public function getFileNames($dir = ".") {

        if (!file_exists($dir) || !is_dir($dir)) {
            return array();
        }
        $dirPath = $dir;
        $dirList = array();
        $dir = opendir($dir);
        while (false !== ($file = readdir($dir))) {
            if ($file !== '.' && $file !== '..' && is_file($dirPath . $file)) {
                $dirList[] = $file;
            }
        }
        closedir($dir);
        return $dirList;
    }
    
    static public function listDir($dir) {        
        if (!file_exists($dir) || !is_dir($dir)) {
            return '';
        }
        $dirPath = $dir;
        $dirList = array();
        $dir = opendir($dir);
        while (false !== ($file = readdir($dir))) {
            if ($file !== '.' && $file !== '..' && is_dir($dirPath . $file)) {
                $dirList[] = $file;
            }
        }
        closedir($dir);
        return $dirList;
    }

    /**
     * 获取文件扩展
     * @param type $filename
     * @return type
     */
    static public function getExt($filename) {
        return substr(strrchr($filename, '.'), 1);
    }

    static public function reduce_path($path) {
        $path = str_replace("://", "@not_replace@", $path);
        $path = preg_replace("#(/+)#", "/", $path);
        $path = preg_replace("#(/\./+)#", "/", $path);
        $path = str_replace("@not_replace@", "://", $path);

        while (preg_match('#\.\./#', $path)) {
            $path = preg_replace('#\w+/\.\./#', '', $path);
        }
        return $path;
    }

    static public function sanitize_filename($string) {
        return sanitize($string, FALSE);
    }

    static public function dir_is_writable($dir, $chmod = 0755) {
        // If it doesn't exist, and can't be made
        if (!is_dir($dir) AND ! mkdir($dir, $chmod, TRUE))
            return FALSE;

        // If it isn't writable, and can't be made writable
        if (!is_writable($dir) AND ! chmod($dir, $chmod))
            return FALSE;

        return TRUE;
    }

    static public function recurse_copy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    self::recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

}
