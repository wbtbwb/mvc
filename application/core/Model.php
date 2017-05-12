<?php

abstract class Model
{   
    protected $_logs = ['validation' => [], 'db' => []];
    public $errors = [];
    
    abstract protected function rules();
    
    protected function attributes()
    {
        return array_keys($this->rules());
    }
    
    protected function errors()
    {
        return [];
    }
    
    public function setAttributes($data)
    {
        if (!is_array($data)) {
            return false;
        }
        
        if ($this instanceOf ActiveRecord && !$this->_isNew) {
            foreach ($data as $key => $value) {
                if (in_array($key, $this->attributes())) {
                    $this->$key = $value;
                }
            }
        } else {
            foreach ($this->attributes() as $attr) {
                $this->$attr = isset($data[$attr]) ? $data[$attr] : null;
            }
        }
        return $this;
    }
    
    protected function validate($attr, $rules, $sequence)
    {
        if (empty($rules)) {
            return true;
        }
        foreach ($rules as $key => $value) {
            $method = (is_string($key)) ? $key : $value;
            $args = (is_string($key)) ? $value : null;
            $attrName = $this->attributes()[$sequence];
            
            if (method_exists($this, $method)) {
                $result = $this->$method($attr, $args);
            } else {
                $result = Validator::$method($attr, $args);
            }
            
            if (!$result) {
                if (is_null($attr) && !in_array('required', $this->rules($attr))) {
                    return true;
                }
                
                $this->_logs['validation'][] = [
                    'model' => get_class($this),
                    'attribute' => $this->attributes()[$sequence],
                    'method' => $method,
                    'params' => [$attr, $args],
                ];
                if(in_array($attrName, array_keys($this->errors()))) {
                    $this->errors[] = $this->errors()[$attrName][$method];
                }
                return false;
            }
        }
        return true;
    }
    
    public function isValid()
    {
        $sequence = 0;
        foreach ($this->rules() as $attr => $rules) {
            $this->validate($this->$attr, $rules, $sequence);
            $sequence++;
        }
        return empty($this->_logs['validation']) ? true : false;
        
        /*
        $sequence = 0;
        foreach ($this->rules() as $attr => $rules) {
            if (!$this->validate($this->$attr, $rules, $sequence)) {
                return false;
            }
            $sequence++;
        }
        return true;
        */
    }
    
    public function hasErrors()
    {
        return !empty($this->_logs['validation']) || !empty($this->_logs['db']);
    }
    
    public function getLogs()
    {
        return $this->hasErrors() ?  $this->_logs : null;
    }
}
