<?php
/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace hmvc\Db;

use hmvc\Web\Config;

class Db extends \hmvc\Singleton {

    /**
     * 数据库连接池
     * @var array 
     */
    private static $_connect_pools = array();

    
    /**
     * 获取数据库连接
     * @param string $_dbname
     * @return \hmvc\Db\Driver\MySQLi
     */
    public static function getConnection($_dbname = 'db') {        
        if (isset(self::$_connect_pools[$_dbname])) {
            return self::$_connect_pools[$_dbname];
        } else {
            return self::_initConnection($_dbname);
        }
    }

    private static function _initConnection($_dbname) {
        $dbconf = Config::get("databases.$_dbname");
       
        if ($dbconf && is_array($dbconf) && isset($dbconf['driver'])) {
            switch ($dbconf['driver']) {
                case 'mysqli':
                    self::$_connect_pools[$_dbname] = new \hmvc\Db\Driver\MySQLi($dbconf);                    
                    break;
                case 'pdo_mysql':
                    break;
                case 'sqlite':
                    break;
                default:
                    return NULL;
                    break;
            }
            return self::$_connect_pools[$_dbname];
        } else {
            return NULL;
        }
    }

}
