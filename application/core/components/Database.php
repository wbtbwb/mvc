<?php

class Database
{
    private static $_instance = null;
    private $_mysqli;
    private $_valueSymbol = '{?}';
    public $error = [];
    
    private function __construct()
    {
        $this->_mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        
        if ($this->_mysqli->connect_error) {
            $this->error = [$this->_mysqli->connect_error, null];
        } else {
            $this->_mysqli->query("SET NAMES 'utf8'");
            $this->_mysqli->query("SET lc_time_names 'ru_RU'");
        }
    }
    
    private function __clone()
    {
    }
    
    private function __wakeup()
    {
    }
    
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    
    public function getValueSymbol()
    {
        return $this->_valueSymbol;
    }
    
    private function getQuery($query, $params)
    {
        if ($params) {
            foreach ($params as $param) {
                $pos = strpos($query, $this->_valueSymbol);
                
                if (is_string($param)) {
                    $param = "'" . $this->_mysqli->real_escape_string($param) . "'";
                }
                if (is_null($param)) {
                    $param = 'NULL';
                }
                
                $query = substr_replace($query, $param, $pos, strlen($this->_valueSymbol));
            }
        }
        return $query;
    }
    
    public function query($query, $params = false)
    {
        $query = $this->getQuery($query, $params);
        if (!$this->_mysqli->query($query)) {
            $this->error = [$this->_mysqli->error, $query];
            return false;
        }
        if ($this->_mysqli->insert_id === 0) {
            return true;
        }
        return $this->_mysqli->insert_id;
    }
    
    public function select($query, $params = false)
    {
        $query = $this->getQuery($query, $params);
        $result = $this->_mysqli->query($query);
        if (!$result) {
            $this->error = [$this->_mysqli->error, $query];
            return false;
        }
        if ($result->num_rows === 0) {
            return null;
        }
        return $this->resultToArray($result);
    }
    
    public function selectRow($query, $params = false)
    {
        $query = $this->getQuery($query, $params);
        $result = $this->_mysqli->query($query);
        if (!$result) {
            $this->error = [$this->_mysqli->error, $query];
            return false;
        }
        return $result->fetch_assoc();
    }
    
    public function selectCell($query, $params = false)
    {
        $result = $this->selectRow($query, $params);
        if (!$result) {
            return false;
        }
        return array_values($result)[0];
    }
    
    public function getCount($tableName)
    {
        $query = "SELECT COUNT(id) FROM $tableName";
        return $this->selectCell($query);
    }
    
    private function resultToArray($result)
    {
        $array = [];
        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }
        return $array;
    }
    
    public function __destruct()
    {
        if ($this->_mysqli) {
            $this->_mysqli->close();
        }
    }
}
