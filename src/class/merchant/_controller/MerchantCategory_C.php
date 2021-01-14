<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 16-11-20
 * Time: 18:46
 */

namespace salesteck\merchant;


use salesteck\_interface\DbControllerTranslation;
use salesteck\_interface\DbIdCode;
use salesteck\_base\Language_C;
use salesteck\Db\CodeGenerator;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\Db\SqlOrder;
use salesteck\utils\String_Helper;

class MerchantCategory_C extends Db implements DbControllerTranslation, DbIdCode
{
    public const
        TABLE = "_merchant_category",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;

    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getSqlTranslation(): Sql
    {
        return Sql::_inst(self::TABLE_TRANSLATION);
    }

    static function _getUniqueId(): string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code, 4, CodeGenerator::CHARACTER);
    }

    public static function _getEditorCategoryParentOption(string $language) : array
    {
        $arrayOption = [];
        $language = Language_C::_getValidLanguage($language);

        $sql = self::_getSql();
        $sql
            ->leftJoin(self::TABLE, self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code)
            ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
            ->equal(self::TABLE, self::_col_category_parent, "")
        ;
        if($sql->select()){
            $results = $sql->result();
            foreach ($results as $row){
                if(
                    array_key_exists(self::_col_id_code, $row) &&
                    array_key_exists(self::_col_name, $row)
                ){
                    $idCode = $row[self::_col_id_code];
                    $name = $row[self::_col_name];
                    $arrayOption[$name] = $idCode;
                }
            }
        }


        return $arrayOption;
    }

    public static function _getElementFromId(string $idCode, string $language) : ? MerchantCategory
    {
        $language = Language_C::_getValidLanguage($language);
        if(String_Helper::_isStringNotEmpty($idCode) && String_Helper::_isStringNotEmpty($language)){
            $sql = self::_getSql();
            $sql
                ->leftJoin(self::TABLE, self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code)
                ->equal(self::TABLE, self::_col_id_code, $idCode)
                ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
            ;
            if($sql->select()){
                $row = $sql->first();
                return MerchantCategory::_inst($row);
            }
        }
        return null;
    }

    public static function _getAllMainCategory($language) : array
    {
        $arrayCategory = [];
        $language = Language_C::_getValidLanguage($language);
        $sql = self::_getSql();

        $sqlOrder = new SqlOrder(self::TABLE_TRANSLATION, self::_col_name, SqlOrder::ASC);
        $sql
            ->leftJoin(self::TABLE, self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code)
            ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
            ->equal(self::TABLE, self::_col_category_parent, "")
            ->addOrder($sqlOrder)
        ;
        if($sql->select()){
            $result = $sql->result();
            foreach ($result as $row){
                $category = MerchantCategory::_inst($row);
                if($category instanceof MerchantCategory){
                    array_push($arrayCategory, $category);
                }
            }
        }

        return $arrayCategory;
    }


    public static function _getAllCategory($language) : array
    {
        $arrayCategory = [];
        $language = Language_C::_getValidLanguage($language);
        $sql = self::_getSql();

        $sqlOrder = new SqlOrder(self::TABLE_TRANSLATION, self::_col_name, SqlOrder::ASC);
        $sql
            ->leftJoin(self::TABLE, self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code)
            ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
            ->different(self::TABLE, self::_col_tree, "")
            ->addOrder($sqlOrder)
        ;
        if($sql->select()){
            $result = $sql->result();
            foreach ($result as $row){
                $category = MerchantCategory::_inst($row);
                if($category instanceof MerchantCategory){
                    array_push($arrayCategory, $category);
                }
            }
        }

        return $arrayCategory;
    }


}