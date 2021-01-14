<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-11-19
 * Time: 19:19
 */

namespace salesteck\custom;




use salesteck\_interface\DbControllerObject;
use salesteck\_interface\DbIdCode;
use salesteck\_base\Language;
use salesteck\_base\Language_C;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\utils\Debug;

class Industry_C extends Db implements DbControllerObject, DbIdCode
{
    public const
        TABLE = "_industry",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;

    public static function _count(array $columnValue = []){
        $sql = self::_getSql();
        foreach ($columnValue as $columnName => $value){
            $sql->equal(self::TABLE, $columnName, $value);
        }
        return $sql->count();
    }


    public static function _getIndustry( string $lang = ""){
        $arrayDebug = [];
        $result =[];
        $sql = self::_getSqlTranslation();
        $sql
            ->orderAsc(self::TABLE, self::_col_name);
        if($lang === ""){
            $lang = Language_C::_getValidLanguage($lang);
        }
        $arrayDebug["lang"] = $lang;
        $sql->equal(self::TABLE_TRANSLATION, self::_col_language, $lang);
        if($sql->select()){
            $rows = $sql->result();
            foreach ($rows as $row){
                $industry = self::_getObjectClassFromResultRow($row);
                if($industry !== null && $industry instanceof Industry){
                    array_push($result, $industry);
                }
            }
        }
        $arrayDebug["sql"] = $sql;
        Debug::_exposeVariable($arrayDebug, true);
        return $result;
    }

    public static function _indexIndustry(){
        $allIndustry = self::_getIndustry();
        $sql = self::_getSqlTranslation();
        $itemToInsert = [];
        $allActiveLanguage = Language_C::_getAllActiveLanguage();
        foreach ($allIndustry as $industry){
            $idCode = $industry[self::_col_id_code];
            $label = $industry[self::_col_label];
            foreach ($allActiveLanguage as $language){
                if($language !== null && $language instanceof Language){

                    $row = [
                        self::_col_id_code => $idCode,
                        self::_col_language => $language->getIdCode(),
                        self::_col_name => $label
                    ];
                    array_push($itemToInsert, $row);
                }
            }
        }
        $sql->bulkInsert($itemToInsert);
    }




    public static function _getUniqueId(): string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }


    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getSqlTranslation(): Sql
    {
        return Sql::_inst(self::TABLE_TRANSLATION);
    }

    static function _getObjectClassFromResultRow($row) : ? Industry
    {
        if(
            $row !== null && gettype($row) === gettype([]) &&
            array_key_exists(self::_col_id_code, $row) &&
            array_key_exists(self::_col_name, $row)
        ){
            return new Industry(
                $row[self::_col_id_code],
                $row[self::_col_name]
            );
        }else{
            return null;
        }
    }
}