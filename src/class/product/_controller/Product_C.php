<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 09-03-20
 * Time: 22:53
 */
namespace salesteck\product;




use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbIdCode;
use salesteck\_interface\DbJoinController;
use salesteck\_base\Image_c;
use salesteck\_base\Language_C;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\utils\Debug;

class Product_C extends Db implements DbIdCode, DbJoinController, DbCleaner
{
    public const
        TABLE = "_product_element",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION,
        TABLE_IMAGES = self::TABLE.self::_IMAGES,
        MAX_SIZE = 200000
    ;
    public const FOLDER_SRC = Image_c::SRC."product/";

    public const _col_allergen = self::_col.'_allergen';

    static function _getSqlImages(): Sql
    {
        return Sql::_inst(self::TABLE_IMAGES);
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
        return $sql->innerJoin(self::TABLE_TRANSLATION,self::_col_id_code, self::TABLE, self::_col_id_code);
    }

    public static function _getAllProduct(array $columnValue = [], array $columnValueTranslation = []) : array
    {

        $sql = self::_getSql();
        $sql->leftJoin(self::TABLE,self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code);
        $sql->leftJoin(self::TABLE,self::_col_image, self::TABLE_IMAGES, self::_col_id);
        foreach ($columnValue as $column => $value){
            $sql->equal(self::TABLE, $column, $value);
        }
        foreach ($columnValueTranslation as $columnTranslation => $valueTranslation){
            $sql->equal(self::TABLE_TRANSLATION, $columnTranslation, $valueTranslation);
        }
        $sql->orderAsc(self::TABLE, self::_col_order);
        $allProduct = [];
        if($sql->select()){
            $productRows = $sql->result();
            foreach ($productRows as $row){
                $product = Product::_inst($row);
                if($product instanceof Product){
                    array_push($allProduct, [$product->getIdCode() => $product]);
                }
            }
        }
        Debug::_exposeVariableHtml(["sql"=>$sql], false);
        return $allProduct;
    }


    public static function _addProductSample(int $qty = 10){

    }



    public static function _getProductById($id){

        $sql = self::_getJoinSql();
        $sql
            ->equal(self::TABLE_TRANSLATION, self::_col_id, intval($id))
        ;
        $sql->select();
        return $sql->first();
    }

    public static function _getProductByIdCode($idCode, string $language = ""){
        $language = Language_C::_getValidLanguage($language);
        $sql = self::_getJoinSql();
        $sql
            ->equal(self::TABLE, self::_col_id_code, strval($idCode))
            ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
        ;
        $sql->select();
        $row =  $sql->first();
        return Product::_inst($row);
    }

    static function _getEditorOptionProdAllergen(string $lang){
        $options = [];
        $sql = Allergen_C::_getSql();
        $sql
            ->innerJoin(self::TABLE, Allergen_C::_col_id_code, Allergen_C::TABLE_TRANSLATION, Allergen_C::_col_id_code)
            ->equal(Allergen_C::TABLE_TRANSLATION, Allergen_C::_col_language, $lang)
        ;
        if($sql->select()){
            $arrayResult = $sql->result();
            foreach ($arrayResult as $row){
                if(
                    array_key_exists(Allergen_C::_col_id_code, $row) &&
                    array_key_exists(Allergen_C::_col_name, $row)
                ){
                    $options[$row[Allergen_C::_col_name]] = $row[Allergen_C::_col_id_code];
                }
            }
//            $options = $sql->result();
        }
        return $options;
    }

    public static function _getUniqueId(): string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }

    static function _clean(bool $debug = false)
    {
        // TODO: Implement _clean() method.
    }
}