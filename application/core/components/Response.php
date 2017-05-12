<?php

class Response
{
    public static function is403()
    {
        header('HTTP/1.1 403 Forbidden');
    }
    
    public static function is404()
    {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
    }
}
