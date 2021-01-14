<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 18-03-20
 * Time: 18:13
 */

namespace salesteck\admin;



use salesteck\utils\CustomDateTime;
use salesteck\utils\Session;

class AdminSession extends Session
{
    private const
        ADMIN = "admin",
        USER = self::ADMIN."-user",
        LAST_ACTIVITY = self::ADMIN."-activity",
        ERROR = self::ADMIN."-error",
        LANGUAGE = self::ADMIN."-language"
    ;

    public static function _setAdminUser(AdminUser $adminUser){
        if($adminUser !== null && $adminUser instanceof AdminUser){
            self::_setVariable(self::USER, $adminUser);
            self::_updateLastActivity();
        }
    }

    public static function _isUserActive() : bool
    {
        $lastActivity = AdminSession::_getLastActivity();
        $sec = 1800;
        $now = CustomDateTime::_getTimeStamp();
        if( $now > ($lastActivity + $sec) ){
            self::_destroyAdminUser();
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

    public static function _getAdminUser() : ? AdminUser
    {
        return self::_getVariable(self::USER);
    }

    public static function _destroyAdminUser(){
        self::_destroyVariable(self::USER);
        self::_destroyVariable(self::LAST_ACTIVITY);
    }
}