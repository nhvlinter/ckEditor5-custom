<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 09-03-20
 * Time: 23:01
 */
namespace salesteck\product;



use salesteck\_base\Language_C;
use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbIdCode;
use salesteck\_interface\DbJoinController;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\Db\SqlOrder;
use salesteck\utils\String_Helper;

class ProductCategory_C extends Db implements DbJoinController, DbCleaner, DbIdCode
{
    public const
        TABLE = "_product_category",
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

    static function _getJoinSql(): Sql
    {
        $sql = self::_getSqlTranslation();
        return $sql->innerJoin(self::TABLE_TRANSLATION,self::_col_id_code, self::TABLE, self::_col_id_code);
    }

    public static function _getUniqueId(): string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }

    public static function _getCategoryByIdCode(string $idCode, string $language)
    {
        $category = null;
        $sql = self::_getJoinSql();
        $sql
            ->equal(self::TABLE, self::_col_id_code, $idCode)
            ->equal(self::TABLE_TRANSLATION, self::_col_id_code, $idCode)
            ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
        ;
        if($sql->select()){
            $category = OrderedCategory::_inst($sql->first());
        }
        return $category;
    }

    public static function _getEditorOptionCategory(string $merchantId, string $language, SqlOrder $order = null ){
        $options = [];
        $sqlQueryTranslation = self::_getQuerySqlTranslation($merchantId, $language, $order);
        if($sqlQueryTranslation instanceof Sql){
            if($sqlQueryTranslation->select()){
                $rows = $sqlQueryTranslation->result();
                foreach ($rows as $row){
                    if(
                        array_key_exists(self::_col_id_code, $row) &&
                        array_key_exists(self::_col_name, $row)
                    ){
                        $options[$row[self::_col_name]] = $row[self::_col_id_code];
                    }
                }
            }

        }
        return $options;
    }

    public static function _getAllCategory(string $merchantId, string $language, SqlOrder $order = null){
        $allCategory = [];

        $queryTranslation = self::_getQuerySqlTranslation($merchantId, $language, $order);
        if($queryTranslation instanceof Sql){
            $queryTranslation->equal(self::TABLE, self::_col_is_enable, intval(true));
            if($queryTranslation->select()){
                $rows = $queryTranslation->result();
                foreach ($rows as $row){
                    $category = ProductCategory::_inst($row);
                    if($category instanceof ProductCategory){
                        array_push($allCategory, [$category->getIdCode() => $category]);
                    }
                }
            }

        }

        return $allCategory;
    }

    static function _clean(bool $debug = false)
    {
        // TODO: Implement _clean() method.
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

    public static function _getQuerySqlTranslation(string $merchantId, string $language, SqlOrder $order = null) : ? Sql
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
}