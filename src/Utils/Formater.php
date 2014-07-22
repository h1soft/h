<?php
/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    /**
     * 格式化日期 e.g. 3 days ago, or 5 minutes ago to a maximum of a week ago
     *
     * @param int $time unix timestamp
     * @param string format of time (use the constant fdate_format or ftime_format)
     */
    function time_elapsed($time = null, $format) {

        $diff = TIME - $time;
        if ($diff < MINUTE)
            return $diff . " " . get_msg('seconds_ago');
        elseif ($diff < HOUR)
            return ceil($diff / 60) . " " . get_msg('minutes_ago');
        elseif ($diff < 12 * HOUR)
            return ceil($diff / 3600) . " " . get_msg('hours_ago');
        elseif ($diff < DAY)
            return get_msg('today') . " " . strftime(TIME_FORMAT, $time);
        elseif ($diff < DAY * 2)
            return get_msg('yesterday') . " " . strftime(TIME_FORMAT, $time);
        elseif ($diff < WEEK)
            return ceil($diff / DAY) . " " . get_msg('days_ago') . " " . strftime(TIME_FORMAT, $time);
        else
            return strftime($format, $time);
    }

}
