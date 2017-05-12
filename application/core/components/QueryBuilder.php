<?php

class QueryBuilder
{
    private $_db;
    private $_valueSymbol;
    private $_query = '';
    private $_params = [];
    private $_method = 'query';
    public $error = [];
    
    public function __construct(Database $db)
    {
        $this->_db = $db;
        $this->_valueSymbol = $this->_db->getValueSymbol();
    }
    
    public function insert($tableName, $columns = [], $duplicate = [])
    {
        if (array_key_exists(0, $columns)) {
            $this->_params = $columns;
            $columnsStr = '';
        } else {
            $this->_params = array_values($columns);
            $columnsStr = '(' . implode(',', array_keys($columns)) . ')';
        }
        
        $this->_query = "INSERT INTO $tableName" . "$columnsStr VALUES(";
        for ($i = 0; $i < count($this->_params); $i++) {
            $this->_query .= "{$this->_valueSymbol}, ";
        }
        $this->_query = substr_replace($this->_query, ')', -2);
        
        if ($duplicate) {
            $this->_query .= " ON DUPLICATE KEY UPDATE ";
            foreach ($duplicate as $column => $value) {
                $this->_query .= "$column = ";
                if (!is_array($value)) {
                    $this->_query .= "{$this->_valueSymbol}, ";
                    array_push($this->_params, $value);
                } elseif (in_array($value[0]{0}, ['+', '-', '*', '/'])) {
                    $numeric = substr_replace($value[0], '', 0, 1) * 1;
                    $this->_query .= "$column " . $value[0]{0} . " $numeric, ";
                } else {
                    $this->_query .= $value[0] . ', ';
                }
            }
            $this->_query = substr_replace($this->_query, '', -2);
        }
        return $this;
    }
    
    private function createSelectQuery($tableNames, $columnNames, $on, $operator)
    {
        if (!is_array($tableNames)) {
            $tableNames = [$tableNames];
        }
        if (!is_array(current($columnNames))) {
            $columnNames = [$columnNames];
        }
        
        $columnsStr = '';
        for ($i = 0; $i < count($tableNames); $i++) {
            foreach ($columnNames[$i] as $key => $value) {
                if (is_string($key)) {
                    $columnsStr .= $tableNames[$i] . ".$key AS $value, ";
                } else {
                    $columnsStr .= $tableNames[$i] . ".$value, ";
                }
            }
        }
        $columnsStr = substr_replace($columnsStr, '', -2);
        
        $this->_query = "SELECT $columnsStr FROM " . $tableNames[0];
        
        if ($on && count($tableNames) > 1) {
            foreach ($on as $foreign => $references) {
                $this->_query .= " $operator " . next($tableNames) . " ON $foreign = $references";
            }
        }
        return $this;
    }
    
    public function select($tableNames, $columnNames, $on = [], $operator = 'INNER JOIN')
    {
        $this->_method = 'select';
        return $this->createSelectQuery($tableNames, $columnNames, $on, $operator);
    }
    
    public function selectRow($tableNames, $columnNames, $on = [], $operator = 'INNER JOIN')
    {
        $this->_method = 'selectRow';
        return $this->createSelectQuery($tableNames, $columnNames, $on, $operator);
    }
    
    public function selectCell($tableNames, $columnNames, $on = [], $operator = 'INNER JOIN')
    {
        $this->_method = 'selectCell';
        return $this->createSelectQuery($tableNames, $columnNames, $on, $operator);
    }
    
    public function update($tableName, $columns)
    {    
        $expressions = [];
        foreach ($columns as $column => $value) {
            $expression = '';
            if (!is_array($value)) {
                $expression .= "$column = {$this->_valueSymbol}";
                array_push($this->_params, $value);
            } elseif (in_array($value[0]{0}, ['+', '-', '*', '/'])) {
                $numeric = substr_replace($value[0], '', 0, 1) * 1;
                $expression .= "$column = $column" . $value[0]{0} . " $numeric";
            } else {
                $expression .= "$column = " . $value[0];
            }
            $expressions[] = $expression;
        }
        
        $columnsStr = implode(', ', $expressions);
        $this->_query = "UPDATE $tableName SET $columnsStr";
        return $this;
    }
    
    public function delete($tableName)
    {
        $this->_query = "DELETE FROM $tableName";
        return $this;
    }
    
    public function where($tableNames, $columns, $operator = 'AND')
    {    
        if (empty($columns)) {
            return $this;
        }
        
        if (!is_array($tableNames)) {
            $tableNames = [$tableNames];
        }
        if (is_string(array_keys($columns)[0])) {
            $columns = [$columns];
        }
        
        $expressions = [];
        for ($i = 0; $i < count($tableNames); $i++) {
            foreach ($columns[$i] as $column => $value) {
                $expression = '';
                if (!is_array($value)) {
                    $expression .= $tableNames[$i] . ".$column = {$this->_valueSymbol}";
                    array_push($this->_params, $value);
                } elseif (count($value) > 1) {
                    $expression .= $tableNames[$i] . ".$column IN (";
                    foreach ($value as $v) {
                        $expression .= "{$this->_valueSymbol}, ";
                        array_push($this->_params, $v);
                    }
                    $expression = substr_replace($expression, ')', -2);
                } elseif ($value[0]{0} === '%') {
                    $expression .= $tableNames[$i] . ".$column LIKE {$this->_valueSymbol}";
                    array_push($this->_params, '%' . substr_replace($value[0], '', 0, 1) . '%');
                } elseif(in_array($value[0]{0}, ['>', '<'])) {
                    $numeric = substr_replace($value[0], '', 0, 1) * 1;
                    $expression .= $tableNames[$i] . ".$column " . $value[0]{0} . " $numeric";
                } else {
                    $expression .= $tableNames[$i] . ".$column = " . $value[0];
                }
                $expressions[] = $expression;
            }
        }
        
        $this->_query .= ' WHERE ' . implode(" $operator ", $expressions);
        return $this;
    }
    
    public function orderBy($columnName, $keyword = 'ASC')
    {
        $this->_query .= " ORDER BY $columnName $keyword";
        return $this;
    }
    
    public function limit($count)
    {
        $this->_query .= " LIMIT $count";
        return $this;
    }
    
    public function send()
    {
        $method = $this->_method;
        $result = $this->_db->$method($this->_query, $this->_params);
        
        $this->_query = '';
        $this->_params = [];
        $this->_method = 'query';
        $this->error = [];
        
        if (false === $result) {
            $this->error = $this->_db->error;
            return false;
        }
        return $result;
    }
}
