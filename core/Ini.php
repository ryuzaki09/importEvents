<?php

class Ini 
{
    public static function getConfig($key)
    {
        $ini = parse_ini_file(".config.ini", true);

        $arr = explode(".", $key);

        if (!isset($ini[$arr[0]])) {
            return false;
        }

        if (!isset($ini[$arr[0]][$arr[1]])) {
            return false;
        }

        return $ini[$arr[0]][$arr[1]];
    }


}
