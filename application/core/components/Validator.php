<?php

class Validator
{
    public static function required($data)
    {
        return !empty($data);
    }
    
    public static function length($data, $params)
    {
        return mb_strlen($data, 'UTF-8') >= $params[0] && mb_strlen($data, 'UTF-8') <= $params[1];
    }
    
    public static function max($data, $max)
    {
        return mb_strlen($data, 'UTF-8') <= $max;
    }
    
    public static function type($data, $type)
    {
        return gettype($data) === $type;
    }
}
