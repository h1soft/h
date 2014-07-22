<?php

/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace H1Soft\H\Web;

/**
 * Description of Helper
 *
 * @author Administrator
 */
class Model extends \H1Soft\H\Singleton {

    public function db($_dbname = 'db') {
        return \H1Soft\H\Db\Db::getConnection($_dbname);
    }

}
