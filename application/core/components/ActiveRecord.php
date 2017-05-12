<?php

abstract class ActiveRecord extends Model
{
    protected $_db;
    protected $_qBuilder;
    protected $_isNew = true;
    
    public function __construct()
    {
        $this->_db = Database::getInstance();
        $this->_qBuilder = new QueryBuilder($this->_db);
    }
    
    public function __get($name)
    {
        if (empty($this->relations())) {
            return null;
        }
        
        if (isset($this->relations()['one']) && in_array($name, $this->relations()['one'])) {
            $relatedClass = $this->getClassByTable($name);
            return (new $relatedClass)
                ->loadById($this->{$name . '_id'});
        }
        
        if (!isset($this->relations()['many'])) {
            return null;
        }
        
        if (substr($name, -1) == 's' && in_array(substr($name, 0, -1), $this->relations()['many'])) {
            $relatedClass = $this->getClassByTable(substr($name, 0, -1));
            return (new $relatedClass)
                ->find([$this->tableName() . '_id' => $this->id]);
        }
        
        return null;
    }
    
    abstract protected function tableName();
    
    protected function relations()
    {
        return [];
    }
    
    protected function prepareAliases($tableNames, $columnNames)
    {
        $columns = [];
        for ($i = 0; $i < count($tableNames); $i++) {
            $aliases = [];
            foreach ($columnNames[$i] as $column) {
                $aliases[$column] = $tableNames[$i] . "_$column";
            }
            $columns[] = $aliases;
        }
        return $columns;
    }
    
    protected function getClassByTable($tableName)
    {
        $nameParts = explode('_', $tableName);
        $className = '';
        foreach ($nameParts as $part) {
            $className .= ucfirst($part);
        }
        return $className . 'Record';
    }
    
    public function validateNumeric()
    {
        foreach ($this->rules() as $attr => $rules) {
            if (in_array('integer', $rules) || in_array('double', $rules)) {
                $this->$attr *= 1;
            }
        }
        return $this;
    }
    
    public function find($condition, $greedy = false, $one = false, $self = false)
    {
        $tables = [$this->tableName()];
        $columns = [$this->attributes()];
        $on = [];
        
        if ($greedy && !empty($this->relations()['one'])) {
            foreach ($this->relations()['one'] as $table) {
                $tables[] = $table;
                $relatedClass = $this->getClassByTable($table);
                $columns[] = (new $relatedClass)->attributes();
                $on[$this->tableName() . ".$table" . '_id'] = "$table.id";
            }
        }
        $columns = $this->prepareAliases($tables, $columns);
        
        $method = $one ? 'selectRow' : 'select';
        $result = $this->_qBuilder
            ->$method($tables, $columns, $on)
            ->where($this->tableName(), $condition)
            ->send();
        
        if ($result === false) {
            $this->_logs['db'][] = $this->_qBuilder->error;
            return false;
        } elseif (is_null($result)) {
            return null;
        }
        
        $selection = [];
        if ($one) {
            $result = [$result];
        }
        foreach ($result as $row) {
            $thisClass = get_class($this);
            $record = $self ? $this : new $thisClass;
            $record->_isNew = false;
            foreach ($this->attributes() as $attr) {
                $record->$attr = $row[$this->tableName() . "_$attr"];
            }
            if ($greedy && !empty($this->relations()['one'])) {
                foreach ($this->relations()['one'] as $table) {
                    $relatedClass = $this->getClassByTable($table);
                    $subRecord = new $relatedClass;
                    $subRecord->_isNew = false;
                    foreach ($subRecord->attributes() as $subAttr) {
                        $subRecord->$subAttr = $row[$table . "_$subAttr"];
                    }
                    /*
                    unset($record->{$table . '_id'});
                    */
                    $record->$table = $subRecord->validateNumeric();
                }
            }
            $selection[] = $record->validateNumeric();
        }
        if ($one) {
            $selection = $selection[0];
        }
        return $selection;
    }
    
    public function findOne($condition, $greedy = false)
    {
        return $this->find($condition, $greedy, true);
    }
    
    public function findById($id, $greedy = false)
    {
        return $this->findOne(['id' => $id], $greedy);
    }
    
    public function findAll($greedy = false)
    {
        return $this->find(null, $greedy);
    }
    
    public function load($condition, $greedy = false)
    {
        return $this->find($condition, $greedy, true, true);
    }
    
    public function loadById($id, $greedy = false)
    {
        return $this->load(['id' => $id], $greedy);
    }
    
    protected function beforeSave()
    {
    }
    
    protected function afterSave()
    {
    }
    
    public function save()
    {
        $this->beforeSave();
        
        if (!$this->isValid()) {
            return false;
        }
        $method = $this->_isNew ? 'insert' : 'update';
        $condition = $this->_isNew ? null : ['id' => $this->id];
        
        $columns = [];
        foreach ($this->attributes() as $attr) {
            $columns[$attr] = $this->$attr;
        }
        
        $result = $this->_qBuilder
            ->$method($this->tableName(), $columns)
            ->where($this->tableName(), $condition)
            ->send();
        if (false === $result) {
            $this->_logs['db'][] = $this->_qBuilder->error;
            return false;
        }
        
        if ($method === 'insert') {
            $this->_isNew = false;
            $this->id = $result;
        }
        
        $this->afterSave();
        
        return  $this;
    }
    
    public function delete($condition = [])
    {
        if (empty($condition)) {
            $condition = ['id' => $this->id];
        }
        
        $success = $this->_qBuilder
            ->delete($this->tableName())
            ->where($this->tableName(), $condition)
            ->send();
        if (false === $success) {
            $this->_logs['db'][] = $this->_qBuilder->error;
        }
        return $success;
    }
    
    public function getCount()
    {
        return $this->_db->getCount($this->tableName());
    }
}
