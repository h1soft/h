<?php

namespace H1Soft\H\Utils;

/**
 * 
 * @author Allen <h@h1soft.net>
 */
class Formater {

    function byte($size) {
        if ($size > 0) {
            $unim = array("B", "KB", "MB", "GB", "TB", "PB");
            for ($i = 0; $size >= 1024; $i++) {
                $size = $size / 1024;
            }
            return number_format($size, $i ? 2 : 0, DEC_POINT, THOUSANDS_SEP) . " " . $unim[$i];
        }
    }

    /**
     * 格式化单位
     */
    static public function byteFormat($size, $dec = 2) {
        $a = array("B", "KB", "MB", "GB", "TB", "PB");
        $pos = 0;
        while ($size >= 1024) {
            $size /= 1024;
            $pos++;
        }
        return round($size, $dec) . " " . $a[$pos];
    }

    function money($number, $add_currency = false) {
        return ( $add_currency && CURRENCY_SIDE == 0 ? CURRENCY . " " : "" ) . number_format($number, 2, DEC_POINT, THOUSANDS_SEP) . ( $add_currency && CURRENCY_SIDE == 1 ? " " . CURRENCY : "" );
    }

    static function priceFormat($price) {
        if ($price < 10000) {
            return $price;
        } else if ($price >= 10000) {
            return (ceil($price / 10000)) . '万';
        }
    }

}
