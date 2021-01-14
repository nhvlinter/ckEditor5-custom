<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-05-20
 * Time: 03:28
 */

namespace salesteck\_interface;


interface ComparableObj
{
    public static function _compare($left, $right) : int;
}