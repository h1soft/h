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

class Crypt {

   static public function crc32($string,$ys = false)
    {
        $checksum = crc32('h1framework_' . $string);
        
        if (8 == PHP_INT_SIZE) {
            if ($checksum > 2147483647) {
                $checksum = $checksum & (2147483647);
                $checksum = ~($checksum - 1);
                $checksum = $checksum & 2147483647;
            }
        }
        
        return abs($checksum);
    }
    
    /**
     * 生成密码
     * @param type $passwd
     * @return type
     */
    static public function password($passwd) {
        return sha1('h1framework'.$passwd);
    }
    
}
