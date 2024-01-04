<?php

namespace utils;

abstract class CommonUtils
{

    static function ArrayKeyExists($key, $array) {
        if (array_key_exists(strtolower($key), $array)) {
            return true;
        }
        if (array_key_exists(strtoupper($key), $array)) {
            return true;
        }
        return false;
    }

    static function GetArrayValue($key, $array) {
        if (array_key_exists(strtolower($key), $array)) {
            return $array[strtolower($key)];
        }
        if (array_key_exists(strtoupper($key), $array)) {
            return $array[strtoupper($key)];
        }
        return null;
    }

    static function EDS_ID() {
        return 1;
    }

}