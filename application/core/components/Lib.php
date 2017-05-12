<?php

class Lib
{
    public static function randStr($nChars)
    {
        $str = '';
        for ($i = 0; $i < $nChars; $i++) {
            $str .= chr(rand(97, 122));
        }
        return $str;
    }
}
