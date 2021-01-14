<?php
/**
 * Created by PhpStorm.
 * User: SON
 * Date: 31/08/2020
 * Time: 16:31
 */

namespace salesteck\custom;

use salesteck\_interface\DbJoinController;
use salesteck\_base\Image_c;
use salesteck\Db\Db;
use salesteck\Db\Sql;

class Reference_C extends Db  implements DbJoinController
{
    public const
        TABLE = "_reference",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION,
        TABLE_IMAGES = self::TABLE."_images",
        _col_mission = self::_col.'_mission'
    ;
    public const FOLDER_SRC = Image_c::SRC."reference/";



    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getSqlTranslation(): Sql
    {
        return Sql::_inst(self::TABLE_TRANSLATION);
    }

    static function _getJoinSql(): Sql
    {
        $sql = self::_getSqlTranslation();
        return $sql->innerJoin(self::TABLE_TRANSLATION, self::_col_id_code, self::TABLE, self::_col_id_code);
    }
    public static function _getUniqueId(): string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }


}