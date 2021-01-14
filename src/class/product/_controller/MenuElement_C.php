<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 28-07-20
 * Time: 14:39
 */

namespace salesteck\product;


use salesteck\_interface\DbCleaner;
use salesteck\Db\Db;
use salesteck\Db\Sql;

class MenuElement_C extends Db implements DbCleaner
{

    public const
        TABLE = "_menu_element",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION,
        _col_allergen = Product_C::_col_allergen
    ;
    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }


    public static function _getUniqueId() : string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }

    static function _clean(bool $debug = false)
    {
        // TODO: Implement _clean() method.
    }
}