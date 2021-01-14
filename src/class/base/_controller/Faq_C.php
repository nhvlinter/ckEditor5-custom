<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 01-05-20
 * Time: 11:57
 */

namespace salesteck\_base;



use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbControllerObject;
use salesteck\_interface\DbIdCode;
use salesteck\_interface\DbJoinController;
use salesteck\Db\Db;
use salesteck\Db\Sql;

class Faq_C extends Db implements DbJoinController, DbIdCode, DbControllerObject, DbCleaner
{
    public const
        TABLE = "_faq",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION,
        _col_answer = self::_col."_answer"
    ;

    public static function _count(array $columnValue = []){
        $sql = self::_getSql();
        foreach ($columnValue as $columnName => $value){
            $sql->equal(self::TABLE, $columnName, $value);
        }
        return $sql->count();
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
        $sql = self::_getSql();
        return $sql->innerJoin(self::TABLE,self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code);
    }

    public static function _getUniqueId(): string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }

    public static function _getElementById(string $id){
        return parent::_getTableElementBy(self::TABLE_TRANSLATION, self::_col_id, $id);
    }

    public static function _getElementByIdCode(string $idCode){
        return parent::_getTableElementBy(self::TABLE, self::_col_id_code, $idCode);
    }

    public static function _deleteElement(string $idCode){
        if($idCode !== ""){
            $sql = self::_getSql();
            $sql->equal(self::TABLE, self::_col_id_code, $idCode);
            $sql->delete();
        }
    }

    static function _getObjectClassFromResultRow($row) : ? Faq
    {
        if(
            $row !== null && gettype($row) === gettype([]) &&
            array_key_exists(self::_col_title, $row) &&
            array_key_exists(self::_col_answer, $row)
        ){
            return new Faq(
                $row[self::_col_title],
                $row[self::_col_answer]
            );
        }else{
            return null;
        }
    }

    public static function _getAllFaq(array $columnValue = [], array $columnValueTranslation = []) : array
    {
        $allFaq = [];
        $sql = self::_getJoinSql();
        foreach ($columnValue as $column => $value){
            $sql->equal(self::TABLE, $column, $value);
        }
        foreach ($columnValueTranslation as $column => $value){
            $sql->equal(self::TABLE_TRANSLATION, $column, $value);
        }
        $sql->orderAsc(self::TABLE, self::_col_order);
        if($sql->select()){
            $arrayResult = $sql->result();
            foreach ($arrayResult as $row){
                $faq = self::_getObjectClassFromResultRow($row);
                if($faq !== null && $faq instanceof Faq){
                    array_push($allFaq, $faq);
                }
            }
        }

        return $allFaq;
    }

    static function _clean(bool $debug = false)
    {
        // TODO: Implement _clean() method.
    }
}