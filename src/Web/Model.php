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

/**
 * Description of Helper
 *
 * @author Administrator
 */
class Model extends \hmvc\Singleton {

    public function db($_dbname = 'db') {
        return \hmvc\Db\Db::getConnection($_dbname);
    }

}
