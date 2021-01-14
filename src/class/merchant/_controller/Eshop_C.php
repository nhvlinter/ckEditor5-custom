<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 20-11-20
 * Time: 00:12
 */

namespace salesteck\merchant;
use salesteck\_base\Language_C;
use salesteck\Db\Sql;
use salesteck\Db\SqlJoin;

use salesteck\utils\String_Helper;

/**
 * Class Eshop_C
 * @package salesteck\merchant
 */
class Eshop_C extends Merchant_C
{
    public static function _getSql(): Sql
    {
        $sql = parent::_getSql();
        $sql->column(Eshop::ARRAY_ROW_KEY);
        $merchantProfileJoin = new SqlJoin(
            self::TABLE, self::_col_merchant_id_code,
            MerchantProfile_C::TABLE, self::_col_merchant_id_code,
            SqlJoin::LEFT, [MerchantProfile_C::_col_logo]
        );
        $merchantProfileImageJoin = new SqlJoin(
            MerchantProfile_C::TABLE, MerchantProfile_C::_col_logo,
            MerchantProfile_C::TABLE_IMAGES, MerchantProfile_C::_col_id,
            SqlJoin::LEFT, [MerchantProfile_C::_col_web_path]
        );
        $sql
            ->addJoin($merchantProfileJoin)
            ->addJoin($merchantProfileImageJoin)
        ;

        return $sql;
    }

    public static function _getEshop($merchant, $language){
        $language = Language_C::_getValidLanguage($language);
        if($merchant !== null && $merchant instanceof Merchant){
            $idCode = $merchant->getIdCode();
            if( String_Helper::_isStringNotEmpty($idCode) ){
                $merchantRow = self::_getRow([
                    self::_col_merchant_id_code => $merchant->getIdCode()
                ]);
                if(is_array($merchantRow)){

                    $merchantEshop = Eshop::_inst($merchantRow, $language);
                    return $merchantEshop;
                }
            }
        }

        return null;
    }

    private static function _getRow(array $columnsValues) : array
    {
        $sql = self::_getSql();
        foreach ($columnsValues as $columnName => $value){
            if(is_string($columnName) && $columnName !== "" && $value !== null)
                $sql->equal(self::TABLE, $columnName, $value);
        }
        if($sql->select()){
            return $sql->first();
        }
        return [];
    }

}