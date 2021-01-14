<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 19-10-20
 * Time: 19:48
 */

namespace salesteck\merchant;


use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbIdCode;
use salesteck\api\RequestResponse;
use salesteck\_base\Language_C;
use salesteck\config\Config;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\Db\SqlCondition;
use salesteck\Db\SqlJoin;
use salesteck\Db\SqlOrder;
use salesteck\product\Product_C;
use salesteck\product\ProductCategory_C;
use salesteck\takeAwayDelivery\DeliveryHours_C;
use salesteck\takeAwayDelivery\DeliveryZone_C;
use salesteck\takeAwayDelivery\TakeAwayHours_C;
use salesteck\utils\CustomDateTime;
use salesteck\utils\Debug;
use salesteck\utils\String_Helper;
use salesteck\utils\Url_Helper;


/**
 * Class Merchant_C
 * @package salesteck\merchant
 */
class Merchant_C extends Db implements DbCleaner, DbIdCode
{


    public const TABLE = "_merchant";
    public const
        _col_commercial_name = self::_col.'_commercial_name',
        _col_commercial_url = self::_col.'_commercial_url',
        _col_company_name = self::_col.'_company_name',
        _col_gender = self::_col.'_gender',
        _col_contact_number = self::_col.'_contact_number',
        _col_contact_name = self::_col.'_contact_name',
        _col_preferred_language = self::_col.'_preferred_language',
        _col_validity_date = self::_col.'_validity_date',
        _col_tva_id = self::_col.'_tva_id',
        _col_city = self::_col.'_city'
    ;

    public const
        STATUS_CREATED = 0,
        STATUS_AUTHENTICATED = 1
    ;


    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }


    static function _getUniqueId(): string
    {
        return self::_createUniqueId(self::TABLE, self::_col_merchant_id_code, 8);
    }


    static function _clean(bool $debug = false)
    {
        // TODO: Implement _clean() method.
    }


    /**
     * @param string $postCode
     * @param array  $conditions
     * @param int    $limit
     * @param null   $sqlOrder
     * @param null   $response
     *
     * @return array
     */
    public static function _getMerchantIdByDeliveryZone(
        string $postCode , array $conditions = [], int $limit = 0, $sqlOrder = null, &$response = null
    ){
        $arrayMerchantId = [];
        $tableName = self::TABLE;
        $deliveryZoneTableName = DeliveryZone_C::TABLE;
        $colMerchantIdCode = self::_col_merchant_id_code;
        $postCodes = explode(" ", $postCode);

        if(sizeof($postCodes) > 0 ){
            $sql = Sql::_inst($tableName);
            $sql
                ->column([$colMerchantIdCode])
                ->leftJoin($tableName, $colMerchantIdCode, $deliveryZoneTableName, $colMerchantIdCode)
                ->equalTrue($deliveryZoneTableName, DeliveryZone_C::_col_is_enable)
            ;
            if($limit > 0){
                $sql->limit($limit);

            }
            if($sqlOrder instanceof SqlOrder){
                $sql->addOrder($sqlOrder);
            }
            foreach ($postCodes as $code){
                $sql->startWith($deliveryZoneTableName, DeliveryZone_C::_col_post_code, $code);
            }
            foreach ($conditions as $condition){
                if($conditions !== null && $condition instanceof SqlCondition){
                    $sql->addCondition($condition);
                }
            }

            if($sql->select()){
                $arrayResult = $sql->result();
                foreach ($arrayResult as $row){
                    if(array_key_exists($colMerchantIdCode, $row )){
                        $merchantId = $row[$colMerchantIdCode];
                        if(is_string($merchantId) && $merchantId !== ""){
                            array_push($arrayMerchantId, $row[$colMerchantIdCode]);
                        }
                    }
                }
            }
            if($response instanceof RequestResponse){
                $response->debug("sql_getMerchantIdByDeliveryZone", $sql);
            }
//            echo json_encode($sql);
        }

        return array_values(array_unique($arrayMerchantId));

    }

    /**
     * @param $merchantId
     *
     * @return null|\salesteck\merchant\Merchant
     */
    public static function _getMerchantById ($merchantId) : ? Merchant
    {
        if(is_string($merchantId) && $merchantId !== ""){
            return Merchant::_inst(self::_getRow([self::_col_merchant_id_code => $merchantId]));
        }
        return null;
    }

    /**
     * @param $merchantEmail
     *
     * @return null|\salesteck\merchant\Merchant
     */
    public static function _getMerchantByEmail ($merchantEmail) : ? Merchant
    {
        if(String_Helper::_isStringNotEmpty($merchantEmail)){
            return Merchant::_inst(self::_getRow([self::_col_email => $merchantEmail]));
        }
        return null;
    }

    /**
     * @param $commercialUrl
     *
     * @return null|\salesteck\merchant\Merchant
     */
    public static function _getMerchantByCommercialUrl ($commercialUrl) : ? Merchant
    {
        if(is_string($commercialUrl) && $commercialUrl !== ""){
            $commercialUrl = strtolower($commercialUrl);
            return Merchant::_inst(self::_getRow([self::_col_commercial_url => $commercialUrl]));
        }
        return null;

    }

    /**
     * @param array $routeVariable
     *
     * @return null|\salesteck\merchant\Merchant
     */
    public static function _getMerchantByRouteVariable (array $routeVariable) : ? Merchant
    {

        $merchantUrl = sizeof($routeVariable) > 0 ? $routeVariable[0] : null;
        return self::_getMerchantByCommercialUrl($merchantUrl);
    }

    /**
     * @param string $email
     *
     * @return null|\salesteck\merchant\Merchant
     */
    public static function _getUser(string $email) : ? Merchant
    {
        return Merchant::_inst(self::_getRow([self::_col_email=>$email]));
    }


    /**
     * @param array $columnsValue
     *
     * @return array
     */
    public static function _getRows (array $columnsValue = []) : array
    {
        $arrayRows = [];

        $sql = self::_getSql();

        foreach ($columnsValue as $columnName => $value){
            if(is_string($columnName) && $columnName !== "" && $value !== null){
                $sql->equal(self::TABLE, $columnName, $value);
            }
        }
        if($sql->select()){
            $arrayRows = $sql->result();
        }
        Debug::_exposeVariableHtml(['sql'=>$sql], false);

        return $arrayRows;
    }

    /**
     * @param array $columnsValues
     *
     * @return array
     */
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


    /**
     *
     * insert merchant in the database with profile
     * @param string $storeName
     * @param string $storeUrl
     * @param string $companyName
     * @param string $storeAddress
     * @param string $storePostCode
     * @param string $storePhone
     * @param string $contactName
     * @param string $contactPhone
     * @param string $contactEmail
     * @param string $contactGender
     * @param string $preferredLanguage
     * @param string $storeTaxId
     * @param string $hashPassword
     * @param string $category
     *
     * @return null|\salesteck\merchant\Merchant
     */
    public static function _insertMerchant(
        string $storeName, string $storeUrl, string $companyName, string $storeAddress, string $storePostCode,
        string $storePhone, string $contactName, string $contactPhone, string $contactEmail, string $contactGender,
        string $preferredLanguage, string $storeTaxId, string $hashPassword, string $category = ""

    ) : ? Merchant
    {

        if(
            String_Helper::_isStringNotEmpty($storeName) && String_Helper::_isStringNotEmpty($storeUrl) &&
            String_Helper::_isStringNotEmpty($companyName) && String_Helper::_isStringNotEmpty($storeAddress) &&
            String_Helper::_isStringNotEmpty($storePostCode) && String_Helper::_isStringNotEmpty($storePhone) &&
            String_Helper::_isStringNotEmpty($contactName) && String_Helper::_isStringNotEmpty($contactPhone) &&
            String_Helper::_isStringNotEmpty($contactEmail) && String_Helper::_isStringNotEmpty($contactGender) &&
            String_Helper::_isStringNotEmpty($preferredLanguage) && String_Helper::_isStringNotEmpty($storeTaxId) &&
            String_Helper::_isStringNotEmpty($hashPassword)
        ){

            $storeUrl = Url_Helper::_parseUrl($storeUrl);
            $timeStamp = CustomDateTime::_getTimeStamp();
            $preferredLanguage = Language_C::_getValidLanguage($preferredLanguage);
            $idCode = self::_getUniqueId();
            $arrayInsert = [
                self::_col_merchant_id_code => $idCode,
                self::_col_commercial_name => $storeName,
                self::_col_commercial_url => $storeUrl,
                self::_col_company_name => $companyName,
                self::_col_address => $storeAddress,
                self::_col_post_code => $storePostCode,
                self::_col_phone => $storePhone,

                self::_col_contact_name => $contactName,
                self::_col_contact_number => $contactPhone,
                self::_col_email => $contactEmail,
                self::_col_gender => $contactGender,
                self::_col_create_date => $timeStamp,
                self::_col_last_modified =>$timeStamp,
                self::_col_preferred_language => $preferredLanguage,
                self::_col_validity_date =>$timeStamp,
                self::_col_tva_id =>$storeTaxId,
                self::_col_password => $hashPassword,
                self::_col_is_authenticated => intval(false),
                self::_col_is_valid => intval(true),
                self::_col_is_enable => intval(true),
                self::_col_category => $category,
                self::_col_category_tag => $category
            ];
            $sql = self::_getSql();
            if($sql->insert($arrayInsert)){
                $sql->equal(self::TABLE, self::_col_merchant_id_code, $idCode);
                if($sql->select()){
                    $row = $sql->first();
                    $merchant = Merchant::_inst($row);
                    $insertProfile = MerchantProfile_C::_createMerchantProfile(
                        $idCode,
                        [self::_col_label => $storeName],
                        [self::_col_label => $storeName]
                    );
                    if($insertProfile){
                        return $merchant;
                    }else{

                        $newSql = self::_getSql();
                        $newSql->equal(self::TABLE, self::_col_merchant_id_code, $idCode);
                        $newSql->delete();
                    }
                }
            }

        }


        return null;
    }


    public static function _updateMerchantValidity(string $merchantIdCode, string $language = Config::defaultLanguage){
        $language = Language_C::_getValidLanguage($language);
        if(String_Helper::_isStringNotEmpty($merchantIdCode)){
            $isProfileValid = self::_isMerchantProfileValid($merchantIdCode);
            $hasProduct = self::_merchantHasProduct($merchantIdCode, $language);
            $hasDeliveryHours = self::_merchantHasDeliveryAwayHours($merchantIdCode);
            $hasTakeawayHours = self::_merchantHasTakeAwayHours($merchantIdCode);
            $isDisplayAndActive = self::_isMerchantDisplayAndActive($merchantIdCode);

            $isValid =  $isProfileValid && $hasProduct && ($hasDeliveryHours || $hasTakeawayHours) && $isDisplayAndActive;
            $sql = self::_getSql();
            $sql->equal(self::TABLE, self::_col_merchant_id_code, $merchantIdCode, SqlCondition::_AND, true);
            $sql->update([
                self::_col_is_valid => intval($isValid)
            ]);
            return $isValid;
        }
        return false;
    }


    /**
     * check if merchant has valid takeAwayHours
     * @param string $merchantIdCode
     *
     * @return bool
     */
    public static function _merchantHasTakeAwayHours(string $merchantIdCode){
        if(String_Helper::_isStringNotEmpty($merchantIdCode)){
            $sql = TakeAwayHours_C::_getSql();

            $profileJoin = new SqlJoin(
                TakeAwayHours_C::TABLE, TakeAwayHours_C::_col_merchant_id_code,
                MerchantProfile_C::TABLE, MerchantProfile_C::_col_merchant_id_code, SqlJoin::LEFT
            );

            $sql
                ->equal(TakeAwayHours_C::TABLE, TakeAwayHours_C::_col_merchant_id_code, $merchantIdCode)
                ->equalTrue(TakeAwayHours_C::TABLE, TakeAwayHours_C::_col_is_enable)
                ->notEmpty(TakeAwayHours_C::TABLE, TakeAwayHours_C::_col_days)
                ->notEmpty(TakeAwayHours_C::TABLE, TakeAwayHours_C::_col_start_time)
                ->notEmpty(TakeAwayHours_C::TABLE, TakeAwayHours_C::_col_end_time)
                ->addJoin($profileJoin)
                ->equal(MerchantProfile_C::TABLE, MerchantProfile_C::_col_merchant_id_code, $merchantIdCode)
                ->equalTrue(MerchantProfile_C::TABLE, MerchantProfile_C::_col_take_away)
            ;
            $count = $sql->count();
//            $countQueryString = $sql->getCountQueryString();
//            Debug::_prettyPrint($count);
            return $count > 0;
        }
        return false;

    }


    /**
     * check if merchant has valid takeAwayHours
     * @param string $merchantIdCode
     *
     * @return bool
     */
    public static function _isMerchantDisplayAndActive(string $merchantIdCode){
        if(String_Helper::_isStringNotEmpty($merchantIdCode)){
            $sql = self::_getSql();
            $sql
                ->equal(self::TABLE, self::_col_merchant_id_code, $merchantIdCode)
                ->equalTrue(self::TABLE, self::_col_is_enable)
                ->equalTrue(self::TABLE, self::_col_is_authenticated)
                ->equalTrue(self::TABLE, self::_col_is_display)
            ;
            $count = $sql->count();
            return $count > 0;
        }
        return false;

    }


    /**
     * check if merchant has valid deliveryHours
     * @param string $merchantIdCode
     *
     * @return bool
     */
    public static function _merchantHasDeliveryAwayHours(string $merchantIdCode) : bool
    {
        if(String_Helper::_isStringNotEmpty($merchantIdCode)){
            $sql = DeliveryHours_C::_getSql();

            $sql
                ->column([
                    DeliveryHours_C::_col_id, DeliveryHours_C::_col_start_time, DeliveryHours_C::_col_end_time,
                    DeliveryHours_C::_col_days, DeliveryHours_C::_col_is_enable
                ], DeliveryHours_C::TABLE)
                ->equal(DeliveryHours_C::TABLE, DeliveryHours_C::_col_merchant_id_code, $merchantIdCode)
                ->equalTrue(DeliveryHours_C::TABLE, DeliveryHours_C::_col_is_enable)
                ->notEmpty(DeliveryHours_C::TABLE, DeliveryHours_C::_col_days)
                ->notEmpty(DeliveryHours_C::TABLE, DeliveryHours_C::_col_start_time)
                ->notEmpty(DeliveryHours_C::TABLE, DeliveryHours_C::_col_end_time)
            ;

            $profileJoin = new SqlJoin(
                DeliveryHours_C::TABLE, DeliveryHours_C::_col_merchant_id_code,
                MerchantProfile_C::TABLE, MerchantProfile_C::_col_merchant_id_code, SqlJoin::LEFT
            );
            $sql
                ->addJoin($profileJoin)
                ->column([MerchantProfile_C::_col_delivery], MerchantProfile_C::TABLE)
                ->equal(MerchantProfile_C::TABLE, MerchantProfile_C::_col_merchant_id_code, $merchantIdCode)
                ->equalTrue(MerchantProfile_C::TABLE, MerchantProfile_C::_col_delivery)
            ;


            $deliveryZoneJoin = new SqlJoin(
                DeliveryHours_C::TABLE, DeliveryHours_C::_col_delivery_zone_id,
                DeliveryZone_C::TABLE, MerchantProfile_C::_col_id_code, SqlJoin::LEFT
            );
            $sql
                ->addJoin($deliveryZoneJoin)
                ->column([
                    DeliveryZone_C::_col_post_code, DeliveryZone_C::_col_delivery_fee, DeliveryZone_C::_col_is_enable
                ], DeliveryZone_C::TABLE)
                ->equalTrue(DeliveryZone_C::TABLE, DeliveryZone_C::_col_is_enable)
                ->equal(DeliveryZone_C::TABLE, DeliveryZone_C::_col_merchant_id_code, $merchantIdCode)
            ;

            $count = $sql->count();
//            $queryString = $sql->getSelectQueryString();
//            Debug::_prettyPrint($count);
            return $count > 0;
        }
        return false;
    }

    /**
     * check if a merchant has product enabled or not
     * @param string $merchantIdCode
     * @param string $language
     *
     * @return bool
     */
    private static function _merchantHasProduct(string $merchantIdCode, string $language = Config::defaultLanguage){
        $language = Language_C::_getValidLanguage($language);
        if(String_Helper::_isStringNotEmpty($merchantIdCode)){
            $sql = Product_C::_getSql();
            $categoryJoin = new SqlJoin(
                Product_C::TABLE, Product_C::_col_category_id,
                ProductCategory_C::TABLE, ProductCategory_C::_col_id_code, SqlJoin::LEFT
            );
            $categoryTranslationJoin = new SqlJoin(
                ProductCategory_C::TABLE, ProductCategory_C::_col_id_code,
                ProductCategory_C::TABLE_TRANSLATION, ProductCategory_C::_col_id_code, SqlJoin::LEFT
            );
            $productTranslationJoin = new SqlJoin(
                Product_C::TABLE, Product_C::_col_id_code,
                Product_C::TABLE_TRANSLATION, Product_C::_col_id_code, SqlJoin::LEFT
            );
            $sql
                ->column([Product_C::_col_id_code], Product_C::TABLE)
                ->equal(Product_C::TABLE, Product_C::_col_merchant_id_code, $merchantIdCode)
                ->equalTrue(Product_C::TABLE, Product_C::_col_is_enable)
                ->notEmpty(Product_C::TABLE, Product_C::_col_id_code)

                ->addJoin($categoryJoin)
                ->equalTrue(ProductCategory_C::TABLE, ProductCategory_C::_col_is_enable)

                ->addJoin($categoryTranslationJoin)
                ->different(ProductCategory_C::TABLE_TRANSLATION, ProductCategory_C::_col_name, "")
                ->equal(
                    ProductCategory_C::TABLE_TRANSLATION, ProductCategory_C::_col_language,
                    $language, SqlCondition::_AND, true
                )

                ->addJoin($productTranslationJoin)
                ->column([Product_C::_col_name], Product_C::TABLE_TRANSLATION)
                ->different(Product_C::TABLE_TRANSLATION, Product_C::_col_name, "")
                ->equal(Product_C::TABLE_TRANSLATION, Product_C::_col_language, $language, SqlCondition::_AND, true)
            ;
            return $sql->count() > 0;
        }
        return false;

    }

    /**
     * @param string $merchantIdCode
     *
     * @return bool
     */
    private static function _isMerchantProfileValid(string $merchantIdCode){

        if(String_Helper::_isStringNotEmpty($merchantIdCode)){
            $colMerchantIdCode = MerchantProfile_C::_col_merchant_id_code;

            $sqlProfile = MerchantProfile_C::_getSql();
            $sqlProfile
                ->column([
                    MerchantProfile_C::_col_merchant_id_code, MerchantProfile_C::_col_logo
                ], MerchantProfile_C::TABLE)
                ->equal(MerchantProfile_C::TABLE, $colMerchantIdCode, $merchantIdCode, SqlCondition::_AND, true)
                ->notEmpty(MerchantProfile_C::TABLE, MerchantProfile_C::_col_logo)
            ;

            $profileTranslationJoin = SqlJoin::_inst(
                MerchantProfile_C::TABLE, $colMerchantIdCode,
                MerchantProfile_C::TABLE_TRANSLATION, $colMerchantIdCode, SqlJoin::LEFT
            );
            $sqlProfile
                ->addJoin($profileTranslationJoin)
//                ->column([MerchantProfile_C::_col_language], MerchantProfile_C::TABLE_TRANSLATION)
            ;

            $profileImageJoin = SqlJoin::_inst(
                MerchantProfile_C::TABLE, MerchantProfile_C::_col_logo,
                MerchantProfile_C::TABLE_IMAGES, MerchantProfile_C::_col_id, SqlJoin::LEFT)
            ;
            $sqlProfile
                ->addJoin($profileImageJoin)
                ->column([MerchantProfile_C::_col_web_path], MerchantProfile_C::TABLE_IMAGES)
                ->notEmpty(MerchantProfile_C::TABLE_IMAGES, MerchantProfile_C::_col_web_path)
            ;

//            $queryString = $sqlProfile->getSelectQueryString();
//            Debug::_prettyPrint($queryString);

            if($sqlProfile->select2(true)){
                $result = $sqlProfile->result();
//                Debug::_prettyPrint($result);
                return sizeof($result) > 0;

            }



        }
        return false;
    }

    /**
     * delete all data from merchant
     * @param string $merchantIdCode
     *
     * @return bool
     */
    public static function _deleteMerchant(string $merchantIdCode) : bool
    {
        if(String_Helper::_isStringNotEmpty($merchantIdCode)){
            $sqlMerchant = Merchant_C::_getSql();
            $colMerchantIdCode = Merchant_C::_col_merchant_id_code;
            $sqlProfileJoin = new SqlJoin(
                Merchant_C::TABLE, $colMerchantIdCode,
                MerchantProfile_C::TABLE, $colMerchantIdCode,
                SqlJoin::LEFT
            );
            $sqlProfileTranslationJoin = new SqlJoin(
                Merchant_C::TABLE, $colMerchantIdCode,
                MerchantProfile_C::TABLE_TRANSLATION, $colMerchantIdCode,
                SqlJoin::LEFT
            );

            $sqlMerchant
                ->equal(Merchant_C::TABLE, $colMerchantIdCode, $merchantIdCode, SqlCondition::_AND, true)
                ->addJoin($sqlProfileJoin)
                ->equal(MerchantProfile_C::TABLE, $colMerchantIdCode, $merchantIdCode, SqlCondition::_AND, true)
                ->addJoin($sqlProfileTranslationJoin)
                ->equal(MerchantProfile_C::TABLE_TRANSLATION, $colMerchantIdCode, $merchantIdCode, SqlCondition::_AND, true)
            ;

            return $sqlMerchant->delete();
        }
        return false;
    }


    public static function _getMerchants() : array
    {
        $merchants = [];

        $sql = self::_getSql();
        if($sql->select(true)){
            $result = $sql->result();
            foreach ($result as $row){
                $merchant = Merchant::_inst($row);
                if($merchant instanceof Merchant){
                    array_push($merchants, $merchant);
                }
            }
        }

        return $merchants;
    }


}