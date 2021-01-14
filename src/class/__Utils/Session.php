<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-08-19
 * Time: 19:46
 */

namespace salesteck\utils;


use salesteck\_base\Language_C;
use salesteck\customer\Customer;

class Session
{

    private const
        USER = "user",
        DEBUG = self::USER."-debug",
        LANGUAGE = self::USER."-language",
        LAST_ACTIVITY = self::USER."-activity",
        CART = self::USER."-cart"
    ;

    /**
     * initialize session
     */
    public static function _initialize(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function writeClose(){
        session_write_close();
    }

    /**
     * get session variable
     * @param string $varName
     * @return mixed
     */
    public static function _getVariable(string $varName){
        return isset($_SESSION[$varName]) ? $_SESSION[$varName] : null;
    }

    /**
     * define session variable
     * @param string $varName
     * @param $var
     * @return mixed
     */
    public static function _setVariable(string $varName, $var)
    {
        return $_SESSION[$varName] = $var;
    }

    /**
     * destroy session variable
     * @param string $varName
     */
    protected static function _destroyVariable(string $varName){
        if(isset($_SESSION[$varName])){
            unset($_SESSION[$varName]);
        }
    }





    public static function _setLanguage(string $language)
    {
        if($language !== ""){
            return self::_setVariable(self::LANGUAGE, $language);
        }
        return null;
    }

    public static function _getLanguage() : string
    {
        $language = self::_getVariable(self::LANGUAGE);
        if($language === null){
            $language = Language_C::_getDefaultLanguage();
        }
        return self::_setLanguage($language);
    }


    public static function _getDebug(){
        $debug = self::_getVariable(self::DEBUG);
        if($debug === null){
            $debug = [];
            self::_setVariable(self::DEBUG, $debug);
        }
        return $debug;
    }

    public static function _setDebug($debug){

        self::_setVariable(self::DEBUG, $debug);
    }

    public static function _destroyDebug(){

        self::_destroyVariable(self::DEBUG);
    }





    public static function _getCart(string $merchantId){
        $cart = "";
        if($merchantId !== ""){
            $merchantCartName = self::CART."_$merchantId";
            $cart = self::_getVariable($merchantCartName);
        }
        return $cart;
    }

    public static function _setCart(string $merchantId, $cart){
        if($merchantId !== ""){
            $merchantCartName = self::CART."_$merchantId";
            return self::_setVariable($merchantCartName, $cart);
        }
        return null;
    }

    public static function _destroyCart(){

        self::_destroyVariable(self::CART);
    }




    public static function _setUser(Customer $customer) : ? Customer
    {
        if($customer !== null && $customer instanceof customer){
            $customer = self::_setVariable(self::USER, $customer);
            self::_updateLastActivity();
            return $customer;
        }
        return null;
    }



    public static function _getUser() : ? Customer
    {
        self::_isUserActive();
        $customer = self::_getVariable(self::USER);

        return $customer;
    }

    public static function _destroyUser(){
        self::_destroyVariable(self::USER);
        self::_destroyVariable(self::LAST_ACTIVITY);
    }

    public static function _isLastActivityValid() : bool
    {
        $lastActivity = self::_getLastActivity();
        $sec = 1800;
        $now = CustomDateTime::_getTimeStamp();
        return ($lastActivity + $sec) > $now;
    }




    public static function _isUserActive() : bool
    {
        $lastActivityValidity = self::_isLastActivityValid();
        if($lastActivityValidity){
            return true;
        }
        self::_destroyUser();
        return false;
    }


    public static function _updateLastActivity(){
        self::_setVariable(self::LAST_ACTIVITY, CustomDateTime::_getTimeStamp());
    }

    public static function _getLastActivity() : ? int
    {
        return self::_getVariable(self::LAST_ACTIVITY);
    }
}