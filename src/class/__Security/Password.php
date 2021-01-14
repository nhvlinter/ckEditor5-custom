<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 02-12-19
 * Time: 06:05
 */
namespace salesteck\security;

class Password
{

    private const salt = "hoperise";

    public static function _hash(string $password){
        return password_hash(self::salt.$password, PASSWORD_DEFAULT);
    }

    public static function _verify(string $password, string $hash){
        return password_verify(self::salt.$password, $hash);
    }
}