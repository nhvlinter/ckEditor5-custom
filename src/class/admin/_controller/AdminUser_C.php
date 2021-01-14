<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 02-12-19
 * Time: 02:42
 */

namespace salesteck\admin;


use salesteck\_interface\DbControllerObject;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\Db\SqlCondition;
use salesteck\security\Password;
use salesteck\utils\CustomDateTime;
use salesteck\utils\Debug;

class AdminUser_C extends Db implements DbControllerObject
{
    public const TABLE = "_admin_user";

    public const
        _col_role =                 self::_col."_role",
        _col_permission = self::_col."_permission"
    ;

    public const
        ROLE_SUPER_ADMIN = 0,
        ROLE_ADMIN = 1,
        ROLE_USER = 2
    ;

    public static function _create(string $login, string $email, string $password){
        $arrayDebug = [];
        $result = null;
        if($password !== "" && $login !== "" && $email !== ""){
            if( ! self::_userExist($login) &&  ! self::_userExist($email)){

                $sql = self::_getSql();
                $password = Password::_hash($password);
                $createDate = CustomDateTime::_getTimeStamp();
                $arrayDebug["login"] = $login;
                $arrayDebug["email"] = $email;
                $arrayDebug["password"] = $password;
                $arrayDebug["createDate"] = $createDate;
                $insert = [
                    self::_col_name => $login,
                    self::_col_email => $email,
                    self::_col_password => $password,
                    self::_col_create_date => $createDate,
                    self::_col_last_modified => $createDate,
                    self::_col_last_connection => $createDate,
                    self::_col_attempt => 0,
                    self::_col_category => 0
                ];

                $arrayDebug["insert"] = $insert;
                if($sql->insert($insert)){
                    $result =  $sql->result();
                    $arrayDebug["result"] = $result;
                }
                $arrayDebug["sql"] = $sql;
            }

        }
        Debug::_exposeVariableHtml($arrayDebug, true);
        return $result;


    }

    public static function _userExist(string $loginOrEmail) : bool
    {
        $sql = self::_getSql();
        $sql->equal(self::TABLE, self::_col_name, $loginOrEmail, SqlCondition::_OR);
        $sql->equal(self::TABLE, self::_col_email, $loginOrEmail, SqlCondition::_OR);
        return $sql->count() > 0;
    }

    public static function _getUser(string $loginOrEmail)
    {
        $sql = self::_getSql();
        $sql->equal(self::TABLE, self::_col_name, $loginOrEmail, SqlCondition::_OR);
        $sql->equal(self::TABLE, self::_col_email, $loginOrEmail, SqlCondition::_OR);
        $sql->select();
        return self::_getObjectClassFromResultRow($sql->first());
    }




    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getObjectClassFromResultRow($row) : ? AdminUser
    {

        if(
            $row !== null && gettype($row) === gettype([]) &&
            array_key_exists(self::_col_id, $row) &&
            array_key_exists(self::_col_name, $row) &&
            array_key_exists(self::_col_email, $row) &&
            array_key_exists(self::_col_role, $row) &&
            array_key_exists(self::_col_permission, $row) &&
            array_key_exists(self::_col_password, $row)
        ){
            return new AdminUser(
                $row[self::_col_id],
                $row[self::_col_name],
                $row[self::_col_email],
                intval($row[self::_col_role]),
                $row[self::_col_permission],
                $row[self::_col_password]
            );
        }else{
            return null;
        }
    }

    public static function _getEditorOptionUserRole(array $i18n, $adminUser = null)
    {
        $superAdmin = AdminI18_C::_getValueFromKey(AdminI18::ADMIN_ROLE_SUPER_ADMIN, $i18n);
        $admin = AdminI18_C::_getValueFromKey(AdminI18::ADMIN_ROLE_ADMIN, $i18n);
        $user = AdminI18_C::_getValueFromKey(AdminI18::ADMIN_ROLE_USER, $i18n);
        $arrayOption = [
            $superAdmin => strval(self::ROLE_SUPER_ADMIN),
            $admin => strval(self::ROLE_ADMIN),
            $user => strval(self::ROLE_USER)
        ];
        if($adminUser !== null && $adminUser instanceof AdminUser){
            $role = $adminUser->getRole();
//
//            foreach ($arrayOption as $key => $value){
//                if($role >= $value){
//                    unset($arrayOption[$key]);
//                }
//            }
            if($role !== self::ROLE_SUPER_ADMIN){

                foreach ($arrayOption as $key => $value){
                    if($role >= $value){
                        unset($arrayOption[$key]);
                    }
                }
            }
        }

        return $arrayOption;
    }
}