<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 17-11-20
 * Time: 00:41
 */

namespace salesteck\utils;


class Array_Helper
{
    public static function _getArrayValue(array $array, $property, $defVal = null){
        $val = $defVal;
        if(String_Helper::_isStringNotEmpty($property) || is_numeric($property)){
            $val = array_key_exists($property, $array) ? $array[$property] : $val;
        }elseif (is_array($property)){
            $arrVal = $array;
            foreach ($property as $prop){
                if(String_Helper::_isStringNotEmpty($prop) || is_numeric($prop)){
                    if(array_key_exists($prop, $arrVal)){
                        $arrVal = $arrVal[$prop];
                    }else{
                        return $val;
                    }
                }else{
                    return $val;
                }
            }
            return $arrVal;
        }

        return $val;
    }

    public static function _getRandomValue(array $array){
        if(sizeof($array) > 0){
            $key = array_rand($array, 1);
            return $array[$key];
        }
        return null;
    }

    public static function _getArrayKeyFirst(array $array){
        if(sizeof($array) > 0){
            return array_keys($array)[0];
        }
        return null;
    }


}