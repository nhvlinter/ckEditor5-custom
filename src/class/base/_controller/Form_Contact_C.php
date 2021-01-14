<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 30-06-20
 * Time: 20:33
 */

namespace salesteck\_base;


use salesteck\_interface\DbControllerObject;
use salesteck\_interface\DbIdCode;
use salesteck\Db\Sql;

class Form_Contact_C extends Form_C implements DbIdCode, DbControllerObject
{


    public const
        TABLE = "_form_contact"
    ;


    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getUniqueId(): string
    {
        return self::_createUniqueId(self::TABLE);
    }

    static function _getObjectClassFromResultRow($row)
    {
        // TODO: Implement _getObjectClassFromResultRow() method.
    }
}