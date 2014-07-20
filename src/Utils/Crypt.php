<?php

namespace H1Soft\H\Utils;

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
