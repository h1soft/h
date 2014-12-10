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

class Security {

    public static function stripSlashes(&$data) {
        if (is_array($data)) {
            if (count($data) == 0) {
                return $data;
            }
            $keys = array_map('stripslashes', array_keys($data));
            $data = array_combine($keys, array_values($data));
            return array_map(array($this, '\hmvc\Web::stripSlashes'), $data);
        } else {
            return \stripslashes($data);
        }
    }

}
