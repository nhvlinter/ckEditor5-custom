<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 23-04-20
 * Time: 17:07
 */
namespace salesteck\product;

use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbControllerObject;
use salesteck\_base\Image_c;
use salesteck\Db\Db;
use salesteck\Db\Sql;

class Allergen_C extends Db implements DbControllerObject, DbCleaner
{
    public const
        TABLE = "_product_allergen",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;

    public const FOLDER_SRC = Image_c::SRC."allergen/";

    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getSqlTranslation(): Sql
    {
        return Sql::_inst(self::TABLE_TRANSLATION);
    }

    static function _getObjectClassFromResultRow($row)
    {
        if(
            array_key_exists(self::_col_id_code, $row) &&
            array_key_exists(self::_col_name, $row) &&
            array_key_exists(self::_col_description, $row)
        ){
            return new Allergen($row[self::_col_id_code], $row[self::_col_name], $row[self::_col_description]);
        }else{
            return null;
        }
    }

    public static function _getUniqueId(): ? string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }



    public static function _getElementById(string $id){
        return parent::_getTableElementBy(self::TABLE_TRANSLATION, self::_col_id,  intval($id));
    }

    public static function _getElementByIdCode(string $idCode){
        return parent::_getTableElementBy(self::TABLE, self::_col_id_code, $idCode);
    }

    public static function _getEditorOptionAllergen($language){
        $options = [];
        $sql = self::_getSql();
        $sql
            ->innerJoin(self::TABLE,self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code)
            ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
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

    public static function _getAllAllergen(array $columnValue = [], array $columnValueTranslation = []){
        $sql = self::_getSql();
        $sql->leftJoin(self::TABLE,self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code);
        foreach ($columnValue as $column => $value){
            $sql->equal(self::TABLE, $column, $value);
        }
        foreach ($columnValueTranslation as $columnTranslation => $valueTranslation){
            $sql->equal(self::TABLE_TRANSLATION, $columnTranslation, $valueTranslation);
        }
        $allAllergen = [];
        if($sql->select()){
            $rows = $sql->result();
            foreach ($rows as $row){
                $allergen = self::_getObjectClassFromResultRow($row);
                if($allergen instanceof Allergen){
                    $allergen->_name = $allergen->getName();
                    $allergen->_value = $allergen->getIdCode();
                    array_push($allAllergen, [$allergen->getIdCode() => $allergen]);
//                    $allAllergen[$allergen->getIdCode()] = $allergen;
                }
            }
        }
        return $allAllergen;
    }

    public static function _getAllergenByIdCode(string $idCode, $language, bool $isEnable = false){
        $sql = self::_getSql();
        $sql
            ->leftJoin(self::TABLE,self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code)
            ->equal(self::TABLE, self::_col_id_code, $idCode)
            ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
        ;
        if($isEnable){
            $sql->equal(self::TABLE, self::_col_is_enable, intval(true));
        }
        $allergen = null;
        if($sql->select()){
            $row = $sql->first();
//            $allergen = $row;
            $allergen = self::_getObjectClassFromResultRow($row);

        }
//        echo "<pre>".json_encode($sql, JSON_PRETTY_PRINT)."</pre>";
        return $allergen;
    }

    static function _clean(bool $debug = false)
    {
        // TODO: Implement _clean() method.
    }
}