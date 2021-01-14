<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 21-11-20
 * Time: 18:05
 */

namespace salesteck\product;


use salesteck\_base\Language_C;
use salesteck\_interface\DbControllerTranslation;
use salesteck\_interface\DbIdCode;
use salesteck\api\RequestResponse;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\Db\SqlOrder;

use salesteck\utils\String_Helper;

/**
 * Class ProductOptionCategory_C
 * @package salesteck\product
 */
class ProductOptionCategory_C extends Db implements DbControllerTranslation, DbIdCode
{
    public const
        TABLE = "_product_option_category",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;

    /**
     * @return \salesteck\Db\Sql
     */
    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    /**
     * @return \salesteck\Db\Sql
     */
    static function _getSqlTranslation(): Sql
    {
        return Sql::_inst(self::TABLE_TRANSLATION);
    }

    /**
     * @return string
     */
    static function _getUniqueId(): string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code, 8);
    }

    private static function _getQuerySql(string $merchantId, SqlOrder $order = null) : ? Sql
    {
        if(String_Helper::_isStringNotEmpty($merchantId)){
            $sql = self::_getSql();
            if($order instanceof SqlOrder){
                $sql->addOrder($order);
            }
            return $sql->equal(self::TABLE, self::_col_merchant_id_code, $merchantId);
        }
        return null;
    }

    private static function _getQuerySqlTranslation(string $merchantId, string $language, SqlOrder $order = null) : ? Sql
    {
        $language = Language_C::_getValidLanguage($language);
        if(String_Helper::_isStringNotEmpty($merchantId)){
            $sql = self::_getSql();
            $sql
                ->leftJoin(
                    self::TABLE, self::_col_id_code,
                    self::TABLE_TRANSLATION, self::_col_id_code
                )
                ->equal(self::TABLE, self::_col_merchant_id_code, $merchantId)
                ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
                ->equal(self::TABLE_TRANSLATION, self::_col_merchant_id_code, $merchantId)
            ;

            if( !$order instanceof SqlOrder){
                $order = new SqlOrder(self::TABLE, self::_col_order);
            }
            if($order instanceof SqlOrder){
                $sql->addOrder($order);
            }
            return $sql;

        }
        return null;
    }


    public static function _getAllCategory(string $merchantId, string $language, SqlOrder $order = null){
        $allCategory = [];
        $queryTranslation = self::_getQuerySqlTranslation($merchantId, $language, $order);
        $queryTranslation
            ->equal(self::TABLE, self::_col_is_enable, intval(true));
        if($queryTranslation instanceof Sql){
            if($queryTranslation->select()){
                $rows = $queryTranslation->result();
                foreach ($rows as $row){
                    $category = ProductOptionCategory::_inst($row);
                    if($category instanceof ProductOptionCategory){
                        array_push($allCategory, $category);
                    }
                }
            }
        }
        return $allCategory;
    }

    public static function _getAllCategoryWithOption(string $merchantId, string $language, SqlOrder $order = null, &$response = null){
        $returnCategory = [];
        $allCategory = self::_getAllCategory($merchantId, $language, $order);

        $optionsById = [];
        $allOptions = ProductOption_C::_getAllOption($merchantId, $language);
        if(sizeof($allOptions) > 0 ){
            foreach ($allOptions as $option){
                if($option instanceof ProductOption){
                    if( !isset( $optionsById[$option->getCategory()] ) ){
                        $optionsById[$option->getCategory()] = [];
                    }
                    array_push($optionsById[$option->getCategory()], [$option->getIdCode() => $option]);
                }
            }

        }

        foreach ($allCategory as $category){
            if($category instanceof ProductOptionCategory){
                if(isset($optionsById[$category->getIdCode()])){
                    $category->setOptions($optionsById[$category->getIdCode()]);
                    array_push($returnCategory, [$category->getIdCode() => $category]);
                }
            }
        }
        return $returnCategory;
    }



    public static function _getEditorOptionCategory(string $merchantId, string $language, SqlOrder $order = null ){
        $options = [];
        $allCategory = self::_getAllCategory($merchantId, $language, $order);
        foreach ($allCategory as $category){
            if($category instanceof ProductOptionCategory){

                $options[$category->getName()] = $category->getIdCode();
            }
        }
        return $options;
    }
}