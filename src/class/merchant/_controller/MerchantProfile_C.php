<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 03-11-20
 * Time: 00:23
 */

namespace salesteck\merchant;


use salesteck\_interface\DbController;
use salesteck\_interface\DbIdCode;
use salesteck\_interface\DbJoinController;
use salesteck\_base\Image_c;
use salesteck\_base\Language;
use salesteck\_base\Language_C;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\utils\String_Helper;

/**
 * Class MerchantProfile_C
 * @package salesteck\merchant
 */
class MerchantProfile_C extends Db implements DbController, DbIdCode, DbJoinController
{
    public const
        TABLE = "_merchant_profile",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION,
        TABLE_IMAGES = self::TABLE.self::_IMAGES,
        MAX_BG_SIZE = 300000,
        MAX_LOGO_SIZE = 50000
    ;
    public const FOLDER_SRC = Image_c::SRC."profile/";

    public const
        _col_take_away = self::_col.'_take_away',
        _col_delivery = self::_col.'_delivery',
        _col_logo = self::_col.'_logo',
        _col_background_image = self::_col.'_background_image',
        _col_welcome_text = self::_col.'_welcome_text',
        _col_conditions = self::_col.'_conditions',
        _col_testimonials = self::_col.'_testimonials',
        _col_thanks_text = self::_col.'_thanks_text',
        _col_description_text = self::_col.'_description_text'
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
        $sql = self::_getSql();
        $sql
            ->leftJoin(
                self::TABLE,Merchant_C::_col_merchant_id_code,
                self::TABLE_TRANSLATION, Merchant_C::_col_merchant_id_code
            )
            ->leftJoin(self::TABLE,Merchant_C::_col_merchant_id_code,
                self::TABLE_IMAGES, Merchant_C::_col_merchant_id_code
            )
        ;
        return $sql;
    }

    static function _getUniqueId(): string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code, 8);
    }


    /**
     * @param array $columnsValue
     *
     * @return null|MerchantProfile
     */
    public static function _getProfile(array $columnsValue) : ? MerchantProfile
    {
        $sql = self::_getSql();
        foreach ($columnsValue as $columnName => $value){
            $sql->equal(self::TABLE, $columnName, $value);
        }
        if($sql->select()){
            $row = $sql->first();
            return MerchantProfile::_inst($row);
        }
        return null;
    }


    /**
     * @param string $merchantIdCode
     *
     * @return string
     */
    public static function _getMerchantLogo(string $merchantIdCode){
        $logo = "";
        $merchantProfile = self::_getProfile([
            Merchant_C::_col_merchant_id_code => $merchantIdCode
        ]);

        if($merchantProfile !== null && $merchantProfile instanceof MerchantProfile){
            $logoId = $merchantProfile->getLogo();
            if(is_string($logoId) && $logoId !== ""){
                $sqlMerchantImages = Sql::_inst(self::TABLE_IMAGES);
                $sqlMerchantImages->equal(self::TABLE_IMAGES, self::_col_id, $logoId);
                if($sqlMerchantImages->select()){
                    $row = $sqlMerchantImages->first();
                    if(array_key_exists(self::_col_web_path, $row)){
                        $logo = $row[self::_col_web_path];
                    }
                }
            }
        }
        return $logo;
    }


    /**
     * @param string $merchantIdCode
     *
     * @return string
     */
    public static function _getMerchantBgImage(string $merchantIdCode){
        $image = "";
        $merchantProfile = self::_getProfile([
            Merchant_C::_col_merchant_id_code => $merchantIdCode
        ]);

        if($merchantProfile !== null && $merchantProfile instanceof MerchantProfile){
            $bgImageId = $merchantProfile->getBackgroundImage();
            if(is_string($bgImageId) && $bgImageId !== ""){
                $sqlMerchantImages = Sql::_inst(self::TABLE_IMAGES);
                $sqlMerchantImages->equal(self::TABLE_IMAGES, self::_col_id, $bgImageId);
                if($sqlMerchantImages->select()){
                    $row = $sqlMerchantImages->first();
                    if(array_key_exists(self::_col_web_path, $row)){
                        $image = $row[self::_col_web_path];
                    }
                }
            }
        }
        return $image;
    }


    /**
     * @param string $merchantIdCode
     *
     * @return array
     */
    public static function _getTakeawayPayment(string $merchantIdCode){
        $arrayPayment = [];
        if(is_string($merchantIdCode) && $merchantIdCode !== ""){
            $sql = self::_getSql();
            $sql->equal(self::TABLE, Merchant_C::_col_merchant_id_code, $merchantIdCode);
            if($sql->select()){

                $row = $sql->first();
                if(array_key_exists(self::_col_takeaway_payment, $row)){
                    $takeaway = $row[self::_col_takeaway_payment];
                    $array = explode(self::ARRAY_DELIMITER, $takeaway);
                    foreach ($array as $idCode){
                        $payment = PaymentType_C::_getPaymentFromIdCode($idCode);
                        if($payment !== null && $payment instanceof PaymentType){
                            array_push($arrayPayment, $payment);
                        }
                    }
                }
            }
        }
        return $arrayPayment;
    }


    /**
     * @param string $merchantIdCode
     *
     * @return array
     */
    public static function _getDeliveryPayment(string $merchantIdCode){
        $arrayPayment = [];
        if(is_string($merchantIdCode) && $merchantIdCode !== ""){
            $sql = self::_getSql();
            $sql->equal(self::TABLE, Merchant_C::_col_merchant_id_code, $merchantIdCode);
            if($sql->select()){

                $row = $sql->first();
                if(array_key_exists(self::_col_delivery_payment, $row)){
                    $takeaway = $row[self::_col_delivery_payment];
                    $array = explode(self::ARRAY_DELIMITER, $takeaway);
                    foreach ($array as $idCode){
                        $payment = PaymentType_C::_getPaymentFromIdCode($idCode);
                        if($payment !== null && $payment instanceof PaymentType){
                            array_push($arrayPayment, $payment);
                        }
                    }
                }
            }
        }
        return $arrayPayment;
    }


    /**
     * @param string $merchantIdCode
     *
     * @param array  $columnsValues
     *
     * @param array  $translationColumnsValues
     *
     * @return bool
     */
    public static function _createMerchantProfile(string $merchantIdCode, array $columnsValues = [], array $translationColumnsValues = []) : bool
    {
        $allInserted = false;
        if(String_Helper::_isStringNotEmpty($merchantIdCode)){
            $sql = self::_getSql();
            $sql->equal(self::TABLE, self::_col_merchant_id_code, $merchantIdCode);
            $count = $sql->count();
            $inserted = false;
            if($count === 0){
                $columnsValues[self::_col_merchant_id_code] = $merchantIdCode;
                $inserted = $sql->insert($columnsValues);
            }
            $inserted = $inserted === true || $count > 0;
            $allInserted = self::_createProfileTranslation($merchantIdCode, $translationColumnsValues);
            return $inserted && $allInserted;
        }
        return $allInserted;
    }

    /**
     * @param string $merchantIdCode
     *
     * @param array  $translationColumnsValues
     *
     * @return bool
     */
    private static function _createProfileTranslation(string $merchantIdCode, array $translationColumnsValues = []) : bool
    {
        if(String_Helper::_isStringNotEmpty($merchantIdCode)){

            $allLanguage = Language_C::_getLanguageFromDb();
            if(sizeof($allLanguage) > 0){
                foreach ($allLanguage as $language){
                    if($language instanceof Language){
                        $sqlTranslation = self::_getSqlTranslation();
                        $sqlTranslation
                            ->equal(self::TABLE_TRANSLATION, self::_col_merchant_id_code, $merchantIdCode)
                            ->equal(self::TABLE_TRANSLATION, self::_col_language, $language->getIdCode())
                        ;

                        $count = $sqlTranslation->count();
                        if($count=== -1){
                            return false;
                        }

                        if($count === 0){
                            $translationColumnsValues[self::_col_merchant_id_code ] = $merchantIdCode;
                            $translationColumnsValues[self::_col_language ] = $language->getIdCode();
                            $inserted = $sqlTranslation->insert($translationColumnsValues, self::_col_id);
                            if($inserted === false){
                                return false;
                            }
                        }


                    }
                }
                return true;

            }
        }
        return false;
    }


    public static function _getProfile2(string $merchantIdCode){

        if(String_Helper::_isStringNotEmpty($merchantIdCode)){

            $sql = self::_getSql();
            $sql->equal(self::TABLE, self::_col_merchant_id_code, $merchantIdCode);
            if($sql->select()){
                $row = $sql->first();
                return MerchantProfile::_inst($row);
            }

        }
        return null;
    }

    public static function _getProfileTranslation(string $merchantIdCode, string $language){
        $language = Language_C::_getValidLanguage($language);
        if(String_Helper::_isStringNotEmpty($merchantIdCode)){

            $sql = self::_getSql();
            $sql->equal(self::TABLE, self::_col_merchant_id_code, $merchantIdCode);
            if($sql->select()){
                $row = $sql->first();
                return MerchantProfile::_inst($row);
            }

        }
    }



}