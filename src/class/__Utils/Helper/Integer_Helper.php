<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 17-11-20
 * Time: 18:29
 */

namespace salesteck\utils;


class Integer_Helper
{

    public static function _intToPrice(int $value, string $currency = "€"){
        return number_format((float)$value/100, 2, '.', '') . " $currency";
    }
}