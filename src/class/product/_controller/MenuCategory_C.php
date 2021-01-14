<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 28-07-20
 * Time: 14:40
 */

namespace salesteck\product;


use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbControllerObject;
use salesteck\_interface\DbJoinController;
use salesteck\Db\Db;
use salesteck\Db\Sql;

class MenuCategory_C extends Db implements DbJoinController, DbControllerObject, DbCleaner
{
    public const
        TABLE = "_menu_category",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;

    public static function _getEditorOptionCategory(string $language){
        $options = [];
        $sql = self::_getSql();
        $sql
            ->innerJoin(self::TABLE,self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code)
            ->equal(self::TABLE_TRANSLATION, self::_col_language, strval($language))
            ->orderAsc(self::TABLE, self::_col_order)
        ;
        if($sql->select()){
            $arrayResult = $sql->result();
            foreach ($arrayResult as $row){
                if(
                    array_key_exists(self::_col_id_code, $row) &&
                    array_key_exists(self::_col_name, $row)
                ){
                    $options[$row[self::_col_name]] = $row[self::_col_id_code];
                }
            }
        }
        return $options;
    }

    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getObjectClassFromResultRow($row)
    {
        // TODO: Implement _getObjectClassFromResultRow() method.
    }

    static function _getSqlTranslation(): Sql
    {
        // TODO: Implement _getSqlTranslation() method.
    }

    static function _clean(bool $debug = false)
    {
        // TODO: Implement _clean() method.
    }

    static function _getJoinSql(): Sql
    {
        // TODO: Implement _getJoinSql() method.
    }

    public static function _getUniqueId() : string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }
}