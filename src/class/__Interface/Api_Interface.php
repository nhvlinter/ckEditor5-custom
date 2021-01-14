<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 10-03-20
 * Time: 13:52
 */

namespace salesteck\_interface;


interface Api_Interface
{
    static function _api_get($request);
    static function _api_post($request);
    static function _api_put($request);
    static function _api_delete($request);
}