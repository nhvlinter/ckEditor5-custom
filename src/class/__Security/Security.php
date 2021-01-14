<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 10-10-19
 * Time: 00:12
 */
namespace salesteck\security;

class Security
{
    public static function checkXss(&$variable) {
        if (!is_array($variable) || !count($variable)) {
            return array();
        }
        foreach ($variable as $k => $v) {
            if (!is_array($v) && !is_object($v)) {
                $data[$k] = htmlspecialchars($v);
            }
            if (is_array($v)) {
                $data[$k] = self::checkXss($v);
            }
        }
        return $variable;
    }
}