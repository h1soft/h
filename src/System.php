<?php
/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace H1Soft\H;

class System extends Singleton {

    //Windows
    public static function isWin() {

        if (isset($_SERVER['WINDIR'])) {
            return true;
        }
        return false;
    }

    public static function delDir($dir) {

        if ($l == self::dir_scan($dir)) {
            foreach ($l as $f)
                if (is_dir($dir . "/" . $f))
                    self::delDir($dir . '/' . $f);
                else
                    unlink($dir . "/" . $f);
            return rmdir($dir);
        }
    }

    public static function dir_scan($dir) {
        if (is_dir($dir) && $dh = opendir($dir)) {
            $f = array();
            while ($fn = readdir($dh)) {
                if ($fn != '.' && $fn != '..')
                    $f[] = $fn;
            } return $f;
        }
    }

    public static function file_list($dir, $ext = null) {
        if ($dl = self::dir_scan($dir)) {
            $l = array();
            foreach ($dl as $f)
                if (is_file($dir . '/' . $f) && ($ext ? preg_match('/\.' . $ext . '$/', $f) : 1))
                    $l[] = $f;
            return $l;
        }
    }

    function dir_copy($source, $dest) {
        if (is_file($source)) {
            copy($source, $dest);
            chmod($dest, fileperms($source));
        } else {
            mkdir($dest, 0777);
            if ($l = self::dir_scan($source)) {
                foreach ($l as $f)
                    self::dir_copy("$source/$f", "$dest/$f");
            }
        }
    }

}
