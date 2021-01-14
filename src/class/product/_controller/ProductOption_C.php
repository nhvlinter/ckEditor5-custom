<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 21-11-20
 * Time: 18:09
 */

namespace salesteck\product;

use salesteck\_base\Language_C;
use salesteck\_interface\DbControllerTranslation;
use salesteck\_interface\DbIdCode;

use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\Db\SqlOrder;
use salesteck\merchant\Merchant_C;

use salesteck\utils\Debug;
use salesteck\utils\String_Helper;

/**
 * Class ProductOption_C
 * @package salesteck\product
 */
class ProductOption_C extends Db implements DbControllerTranslation, DbIdCode
{
    public const
        TABLE = "_product_option_element",
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


    public static function _getEditorOption(string $merchantIdCode, string $language){
        $options = [];
        $sql = self::_getSql();
        $sql
            ->innerJoin(self::TABLE,self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code)
            ->equal(self::TABLE, Merchant_C::_col_merchant_id_code, $merchantIdCode)
            ->equal(self::TABLE_TRANSLATION, self::_col_language, strval($language))
            ->orderAsc(self::TABLE,self::_col_order)
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


    public static function _getAllOption(string $merchantId, string $language, SqlOrder $order = null){
        $allOption = [];
        $queryTranslation = self::_getQuerySqlTranslation($merchantId, $language, $order);
        $queryTranslation
            ->equal(self::TABLE, self::_col_is_enable, intval(true));
        if($queryTranslation instanceof Sql){
            if($queryTranslation->select()){
                $rows = $queryTranslation->result();
                foreach ($rows as $row){
                    $option = ProductOption::_inst($row);
                    if($option instanceof ProductOption){
                        array_push($allOption, $option);
                    }
                }
            }
        }
        return $allOption;
    }



    public static function _merchantHasOption(string $merchantIdCode, string $language){
        $language = Language_C::_getValidLanguage($language);
        $sql = self::_getQuerySqlTranslation($merchantIdCode, $language);
        return $sql->count() > 0;
    }

}