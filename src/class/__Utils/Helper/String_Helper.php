<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 05-11-19
 * Time: 17:14
 */

namespace salesteck\utils;


class String_Helper
{
    public static function _endsWith(string $string, string $endString)
    {
        if(strlen($string)>0){
            $len = strlen($endString);
            return (substr($string, -$len) === $endString);
        }else{
            return false;
        }
    }

    public static function _startsWith (string $string, string $startString)
    {
        if(strlen($string)>0){
            $len = strlen($startString);
            return (substr($string, 0, $len) === $startString);
        }else{
            return false;
        }
    }

    public static function _contain (string $string, string $find) : bool
    {
        return
            self::_isStringNotEmpty($string) && self::_isStringNotEmpty($find) &&
            gettype(strpos($string, $find)) === gettype(0)
        ;
    }


    public static function _replaceAccent (string $str) : string
    {

        $search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
        $replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
        $urlTitle = str_replace($search, $replace, $str);


        return $urlTitle;
    }

    public static function _isStringNotEmpty($var){
        return is_string($var) && $var !== "";
    }

}