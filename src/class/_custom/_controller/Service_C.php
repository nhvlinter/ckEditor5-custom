<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 16-03-20
 * Time: 15:33
 */

namespace salesteck\custom;



use salesteck\_interface\DbControllerObject;
use salesteck\_interface\DbJoinController;
use salesteck\Db\Db;
use salesteck\Db\Sql;

class Service_C extends Db  implements DbJoinController, DbControllerObject
{

    public const
        TABLE = "_service",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;




    public static function _count(array $columnValue = []){
        $sql = self::_getSql();
        foreach ($columnValue as $columnName => $value){
            $sql->equal(self::TABLE, $columnName, $value);
        }
        return $sql->count();
    }

    public static function _getServices(string $lang = ""){

        $allServices = [];
        $joinSql = self::_getJoinSql();
        if($lang !== ""){
            $joinSql
                ->equal(self::TABLE_TRANSLATION, self::_col_language, $lang)
            ;
        }
        if($joinSql->select()){
            $resultRow = $joinSql->result();
            foreach ($resultRow as $row){
                $service = self::_getObjectClassFromResultRow($row);
                if($service !== null && $service instanceof Service){
                    array_push($allServices, $service);
                }
            }
        }
        return $allServices;
    }




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

    static function _getObjectClassFromResultRow($row)
    {
        if(
            $row !== null && gettype($row) === gettype([]) &&
            array_key_exists(self::_col_id, $row) &&
            array_key_exists(self::_col_id_code, $row) &&
            array_key_exists(self::_col_language, $row) &&
            array_key_exists(self::_col_label, $row) &&
            array_key_exists(self::_col_title, $row) &&
            array_key_exists(self::_col_description, $row)
        ){
            return new Service(
                $row[self::_col_id],
                $row[self::_col_id_code],
                $row[self::_col_language],
                $row[self::_col_label],
                $row[self::_col_title],
                $row[self::_col_description]

            );
        }else{
            return null;
        }
    }

    static function _clean(): bool
    {
        // TODO: Implement _clean() method.
    }
}