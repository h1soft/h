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

class Plugin extends \hmvc\Singleton{
    public $assetPath;
    
    


    public static function app() {
        return Application::app();
    }
    
    public static function basePath() {
        return Application::$basePath;
    }
    
    public static function assetPath($_class) {
        $folder_name = substr(md5(\hmvc\Utils\Crypt::crc32($_class)), 0, 11);
        return Application::$basePath.'/assets/'.$folder_name;
    }
    
    public static function assets($_class) {
        $folder_name = substr(md5(\hmvc\Utils\Crypt::crc32($_class)), 0, 11);
        $path = Application::$rootPath.'assets/'.$folder_name ;
        if(!is_dir($path) && \hmvc\Utils\File::dir_is_writable(Application::$rootPath.'assets/')){
            mkdir($path,0777);
        }
        return $path;
    }
}
