<?php

class Registry
{
    protected static $_store = [];
    
    protected function __construct()
    {
    }
    
    protected function __clone()
    {
    }
    
    protected function __wakeup()
    {
    }
    
    public static function set($name, $value)
    {
        return self::$_store[$name] = $value;
    }
    
    public static function get($name)
    {
        return (isset(self::$_store[$name])) ? self::$_store[$name] : null;
    }
    
    public static function exists($name)
    {
        return isset(self::$_store[$name]);
    }
}
