<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 20-11-20
 * Time: 00:53
 */

namespace salesteck\_base;


use salesteck\utils\CustomDateTime;
use salesteck\utils\Json;
use salesteck\utils\String_Helper;

/**
 * Class Cookie_C
 * @package salesteck\base
 */
class Cookie_C
{

    public const
        LAST_SEARCH_VALUE = "SEARCH_VALUE",
        LAST_SEARCH_POST_CODES = "SEARCH_POST_CODES",
        LAST_SEARCH_CATEGORIES = "SEARCH_CATEGORIES",
        POST_CODE = "POST_CODE",
        CITY = "CITY",
        REGION = "REGION",
        CATEGORIES = "CATEGORIES",
        LAST_CONNECTION = "LAST_CONNECTION",
        SHOP_CATEGORIES = "SHOP_CATEGORIES"
    ;

    private const DEFAULT_PATH = "/";

    private static function _getValidPath($path) {
        return String_Helper::_isStringNotEmpty($path) && strpos(self::DEFAULT_PATH, $path) !== false ? $path : self::DEFAULT_PATH;
    }

    public static function _setCookie(string $cookieName, $value, int $expire = CustomDateTime::YEAR, string $path = self::DEFAULT_PATH) : bool
    {
        $path = self::_getValidPath($path);
        if(String_Helper::_isStringNotEmpty($cookieName)){
            $expire = $expire > 0 || $expire <= CustomDateTime::YEAR ? $expire : CustomDateTime::YEAR;
            $expire += CustomDateTime::_getTimeStamp();

            $cookie = new Cookie($value, $path);
            return setcookie(
                $cookieName, $cookie->__toString(), $expire, $path, null, false, true
            );
        }
        return false;
    }


    public static function _getCookie(string $cookieName, $defVal = null) : ? object
    {

        if(String_Helper::_isStringNotEmpty($cookieName) && isset($_COOKIE[$cookieName])){
            $cookieValue = $_COOKIE[$cookieName];
            if(Json::isJson($cookieValue)){
                return $cookie = Cookie::_jsonToClass(json_decode($cookieValue), Cookie::class);
            }
        }

        return $defVal;
    }
    
    public static function _destroyCookie(string $cookieName, string $path = self::DEFAULT_PATH) : bool
    {
        $path = self::_getValidPath($path);
        if(String_Helper::_isStringNotEmpty($cookieName)){
            if (isset($_COOKIE[$cookieName])) {
                unset($_COOKIE[$cookieName]);
                setcookie($cookieName, "", -1, $path);
                return true;
            }
        }
        return false;
    }

}