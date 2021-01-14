<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 21-11-20
 * Time: 00:15
 */

namespace salesteck\utils;


class Url_Helper
{
    public static function _parseUrl(string $url){

        if(String_Helper::_isStringNotEmpty($url)){
            $url = str_replace(" ", "-", $url);
            $url = urlencode(String_Helper::_replaceAccent(strtolower($url)));
            return $url;
        }
        return "";
    }
}