<?php

namespace H1Soft\H\Web;

class Security {

    public static function stripSlashes(&$data) {
        if (is_array($data)) {
            if (count($data) == 0) {
                return $data;
            }
            $keys = array_map('stripslashes', array_keys($data));
            $data = array_combine($keys, array_values($data));
            return array_map(array($this, '\H1Soft\H\Web::stripSlashes'), $data);
        } else {
            return \stripslashes($data);
        }
    }

}
