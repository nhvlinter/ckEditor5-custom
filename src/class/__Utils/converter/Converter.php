<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 28-10-19
 * Time: 02:13
 */

namespace salesteck\utils;


abstract class Converter
{
    public abstract static function convertToString($from):string ;
    public abstract static function convertFromString(string $string);

    public static function _intToDec(int $val){
        return number_format((float)$val/100, 2, '.', '');
    }


    public static function _intToPrice(int $val, string $currency = "€"){
        $price = self::_intToDec($val);
        if($currency !== ""){
            $price = "$price $currency";
        }
        return $price;
    }
}