<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 28-04-20
 * Time: 02:00
 */
namespace salesteck\_base;

use salesteck\_interface\DbControllerObject;
use salesteck\_interface\DbControllerTranslation;
use salesteck\_interface\DbJoinController;
use salesteck\Db\Db;
use salesteck\Db\Sql;

class PostCode_C extends Db implements DbControllerTranslation, DbJoinController, DbControllerObject
{
    public const
        TABLE = "_post_code",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION,
        _col_post_code_id = self::_col.'_post_code_id',
        _col_province_code = self::_col.'_province_code',
        _col_province_name = self::_col.'_province_name',
        _col_region_code = self::_col.'_region_code',
        _col_region_name = self::_col.'_region_name'
    ;

    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getSqlTranslation(): Sql
    {
        return Sql::_inst(self::TABLE_TRANSLATION);
    }

    static function _clean(): bool
    {
        // TODO: Implement _clean() method.
    }

    static function _getJoinSql(): Sql
    {
        return self::_getSqlTranslation()
            ->innerJoin(
                self::TABLE_TRANSLATION,self::_col_post_code_id, self::TABLE, self::_col_post_code_id
            );
    }

    static function _getObjectClassFromResultRow($row)
    {
        return PostCode::_inst($row);
    }
}