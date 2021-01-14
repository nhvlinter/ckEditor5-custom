<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 06-10-19
 * Time: 17:24
 */
namespace salesteck\utils;
class Util
{
    static function _isAssociativeArray($array) : bool
    {
        return is_array($array) && array_keys($array) !== range(0, count($array) - 1);
    }

    static function _isArrayOfString(array $array) : bool
    {
        if(sizeof($array)>0){
            foreach ($array as $element){
                if (gettype($element) !== gettype("")){
                    return false;
                }
            }
            return true;
        }else{
            return false;
        }
    }
}