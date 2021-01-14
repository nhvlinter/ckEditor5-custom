<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 18-03-20
 * Time: 18:13
 */

namespace salesteck\admin;



use salesteck\merchant\Merchant;
use salesteck\utils\CustomDateTime;
use salesteck\utils\Session;

class MerchantSession extends Session
{
    private const
        PORTAL = "portal",
        USER = self::PORTAL."-user",
        LAST_ACTIVITY = self::PORTAL."-activity",
        ERROR = self::PORTAL."-error",
        LANGUAGE = self::PORTAL."-language"
    ;

    public const
        NOTIFICATION_LOAD_TIME = self::PORTAL."-NOTIFICATION_LOAD_TIME",
        NOTIFICATION_ORDER_LOAD_TIME = self::PORTAL."-NOTIFICATION_ORDER_LOAD_TIME"
    ;

    public static function _setPortalUser(Merchant $merchantUser){
        if($merchantUser !== null && $merchantUser instanceof Merchant){
            self::_setVariable(self::USER, $merchantUser);
            self::_updateLastActivity();
        }
    }

    public static function _isUserActive() : bool
    {
        $lastActivity = MerchantSession::_getLastActivity();
        $sec = 1800;
        $now = CustomDateTime::_getTimeStamp();
        if( $now > ($lastActivity + $sec) ){
            self::_destroyPortalUser();
            return false;
        }
        return true;
    }

    public static function _updateLastActivity(){
        self::_setVariable(self::LAST_ACTIVITY, CustomDateTime::_getTimeStamp());
    }

    public static function _getLastActivity() : ? int
    {
        return self::_getVariable(self::LAST_ACTIVITY);
    }

    public static function _getPortalUser() : ? Merchant
    {
        return self::_getVariable(self::USER);
    }

    public static function _destroyPortalUser(){
        self::_destroyVariable(self::USER);
        self::_destroyVariable(self::LAST_ACTIVITY);
    }
}