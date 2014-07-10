<?php

namespace H1Soft\H\Db\Driver;

class MySQLi extends \H1Soft\H\Db\Driver\Common {

    private $_link;
    private $_cur_result_count;

    /**
     * 
     * @var array 数据库配置信息
     */
    private $_dbconf = array(
        'driver' => 'mysqli',
        'host' => 'localhost',
        'database' => 'h',
        'username' => 'root',
        'password' => '',
        'prefix' => 'h_',
        'charset' => 'uft8',
        'schema' => '',
        'port' => '3306'
    );

    public function __construct($_dbconf) {
        $this->_dbconf['host'] = isset($_dbconf['host']) ? $_dbconf['host'] : 'localhost';
        $this->_dbconf['database'] = isset($_dbconf['database']) ? $_dbconf['database'] : 'h';
        $this->_dbconf['username'] = isset($_dbconf['username']) ? $_dbconf['username'] : 'root';
        $this->_dbconf['password'] = isset($_dbconf['password']) ? $_dbconf['password'] : 'password';
        $this->_dbconf['prefix'] = isset($_dbconf['prefix']) ? $_dbconf['prefix'] : 'h_';
        $this->_dbconf['charset'] = isset($_dbconf['charset']) ? $_dbconf['charset'] : 'utf8';
        $this->_dbconf['schema'] = isset($_dbconf['schema']) ? $_dbconf['schema'] : '';
        $this->_dbconf['port'] = isset($_dbconf['port']) ? $_dbconf['port'] : '3306';
        //初始化连接
        $this->_initConnection();
    }

    private function _initConnection() {
        $this->_link = new \mysqli($this->_dbconf['host'], $this->_dbconf['username'], $this->_dbconf['password'], $this->_dbconf['database'], $this->_dbconf['port']);
        if ($this->_link->connect_error) {
            throw new ErrorException('Error: Could not make a database link (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        }
        $this->_link->set_charset($this->_dbconf['charset']);
        $this->_link->query("SET SQL_MODE = ''");
    }

    public function query($query, $data = false) {
        if (is_array($data)) {
            $query = vsprintf($query, $data);
        }
        $result = $this->_link->query($query);
        if (!$this->_link->errno) {
            return $this->resultToArray($result);
        } else {
            throw new \ErrorException('Error: ' . $this->_link->error . '<br />Error No: ' . $this->_link->errno . '<br />' . $query);
        }
    }

    public function getRow($query, $params = MYSQLI_ASSOC, $type = MYSQLI_ASSOC) {
        if (is_array($params)) {
            $query = vsprintf($query, $params);
        } else {
            $type = $params;
        }
        $result = $this->_link->query($query);
        if (!$this->_link->errno) {
            $this->_cur_result_count = $result->num_rows;
            return $result->fetch_array($type);
        } else {
            throw new \ErrorException('Error: ' . $this->_link->error . '<br />Error No: ' . $this->_link->errno . '<br />' . $query);
        }
    }

    public function exec($query, $data = false) {
        if (is_array($data)) {
            $query = vsprintf($query, $data);
        }
        return $this->_link->query($query);
    }

    public function startTranscation() {
        $this->_link->autocommit(FALSE);
    }

    public function commit($flags = NULL, $name = NULL) {
        $this->_link->commit($flags, $name);
    }

    public function rollback($flags = NULL, $name = NULL) {
        $this->_link->rollback($flags, $name);
    }

    public function tables() {
        $result = $this->_link->query("SHOW TABLES");
        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            $rows[] = $row[0];
        }
        return $rows;
    }

    private function resultToArray($result, $resulttype = MYSQLI_ASSOC) {
        if (!$result) {
            return array();
        }
        $this->_cur_result_count = $result->num_rows;
        $rows = array();
        while ($row = $result->fetch_array($resulttype)) {
            $rows[] = $row;
        }
        $result->close();
        return $rows;
    }

    public function count() {
        return $this->_cur_result_count;
    }

    public function tb_name($_tbname) {
        return $this->_dbconf['prefix'] . $_tbname;
    }

    public function insert($_tbname, $_data) {
        if (!empty($_data) && !is_array($_data)) {
            return NULL;
        }
        $_tbname = $this->tb_name($_tbname);
        $keys = array();
        $vals = array();
        foreach ($_data as $key => $val) {
            $keys[] = sprintf('`%s`', $key);
            $vals[] = sprintf('\'%s\'', $val);
        }

        return $this->exec("INSERT INTO `%s` (%s) VALUES (%s)", array(
                    $_tbname,
                    join(',', $keys),
                    join(',', $vals)
        ));
    }

    public function update($_tbname, $_data, $_where = false) {
        if (!empty($_data) && !is_array($_data)) {
            return NULL;
        }
        $_tbname = $this->tb_name($_tbname);


        $vals = array();
        foreach ($_data as $key => $val) {
            $vals[] = sprintf('`%s`=\'%s\'', $key, $val);
        }

        if (!$_where) {
            return $this->exec("UPDATE `%s` SET %s", array(
                        $_tbname,
                        join(',', $vals)
            ));
        } else {
//            echo vsprintf("UPDATE `%s` SET %s WHERE %s", array(
//                        $_tbname,
//                        join(',', $vals),
//                        $_where
//            ));
            return $this->exec("UPDATE `%s` SET %s WHERE %s", array(
                        $_tbname,
                        join(',', $vals),
                        $_where
            ));
        }
    }

    public function delete($_tbname, $_where = false) {
        $_tbname = $this->tb_name($_tbname);
        if (!$_where) {
            return $this->exec("DELETE FROM `%s`", array(
                        $_tbname                        
            ));
        }else{
            return $this->exec("DELETE FROM `%s` WHERE %s", array(
                        $_tbname,
                        $_where
            ));
        }
    }

    public function error() {
        
    }

    public function escape($str) {
        return mysqli_real_escape_string($str);
    }

    public function getCharset() {
        return $this->_link->get_charset();
    }

    public function autocommit($mode) {
        return $this->_link->autocommit($mode);
    }

    public function affected() {
        return $this->_link->affected_rows;
    }

    public function lastId() {
        return $this->_link->insert_id;
    }

    public function close() {
        if (is_resource($this->_link)) {
            $this->_link->close();
        }
    }

    function __destruct() {
        $this->close();
    }

}
