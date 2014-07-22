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

class Date {

    public static function isToday($date) {
        return date('Y-m-d', $date) == date('Y-m-d', time());
    }

    public static function timeDiffToDay($start_time, $end_time) {
        if ($end_time < $start_time) {
            return 0;
        }
        $common = $end_time - $start_time;
        $a = floor($common / 86400 / 360); //整数年
        $b = floor($common / 86400 / 30) - $a * 12; //整数月
        $c = floor($common / 86400) - $a * 360 - $b * 30; //整数日
        $d = floor($common / 86400); //总的天数
        $str = '';
        if ($a) {
            $str = $a . '年 ';
        }
        if ($b) {
            $str .= $b . '个月 ';
        }
        if ($c) {
//                $str .= $c . '天 ';
        }
        return $str;
    }

    public static function isDate($date) {
        $stamp = strtotime($date);

        if (!is_numeric($stamp)) {
            return FALSE;
        }
        $month = date('m', $stamp);
        $day = date('d', $stamp);
        $year = date('Y', $stamp);

        if (checkdate($month, $day, $year)) {
            return TRUE;
        }

        return FALSE;
    }

    public static function toTimeStamp($date = NULL) {
        if (!$date) {
            return time();
        }
        return strtotime($date);
    }

}
