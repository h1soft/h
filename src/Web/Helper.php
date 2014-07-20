<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace H1Soft\H\Web;

/**
 * Description of Helper
 *
 * @author Administrator
 */
abstract class Helper {
    public function db($_dbname = 'db') {
        return \H1Soft\H\Db\Db::getConnection($_dbname);
    }
}
