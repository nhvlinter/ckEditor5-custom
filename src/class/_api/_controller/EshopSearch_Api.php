<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 17-11-20
 * Time: 23:17
 */

namespace salesteck\api;

use salesteck\_base\Language_C;
use salesteck\config\Config;
use salesteck\Db\Sql;
use salesteck\Db\SqlCondition;
use salesteck\Db\SqlConditionGroup;

use salesteck\Db\SqlOrder;
use salesteck\merchant\Eshop;
use salesteck\merchant\Eshop_C;
use salesteck\merchant\Merchant_C;
use salesteck\merchant\MerchantCategory_C;
use salesteck\merchant\MerchantProfile_C;
use salesteck\product\Product_C;
use salesteck\promotion\Promotion_C;
use salesteck\takeAwayDelivery\DeliveryZone_C;
use salesteck\utils\String_Helper;

/**
 * Class EshopSearch_Api
 * @package salesteck\api
 */
class EshopSearch_Api
{
    /**
     *
     */
    private const TABLE_NAME = Merchant_C::TABLE;


    /**
     * @param      $language
     * @param      $searchValue
     * @param      $category
     * @param      $postCode
     * @param      $promotion
     * @param      $isNew
     * @param int  $limit
     * @param null $response
     *
     * @return array|bool
     */
    public static function _search(
        $language, $searchValue, $category, $postCode, $promotion, $isNew, int $limit = 0, &$response = null
    )
    {
        $__LINE__ = __LINE__;

        $options = [];
        Config::_getRootAddress();

        $merchants = false;
        $searchValue = is_string($searchValue) ? trim($searchValue) : $searchValue;
        $category = is_string($category) ? trim($category) : $category;
        $postCode = is_string($postCode) ? trim($postCode) : $postCode;
        $promotion = is_string($promotion) ? trim($promotion) : $promotion;
        $limit = intval($limit);
        $isNew = boolval($isNew);
        $arg = [
            "language" => $language, "search" => $searchValue, "category" => $category,
            "postCode" => $postCode, "promotion" => $promotion, "limit" => $limit
        ];

        if($response instanceof RequestResponse){
            $response
                ->_function(__FUNCTION__, self::class )
                ->debug( $arg)
            ;
        }

        if(String_Helper::_isStringNotEmpty($language)){
            $__LINE__ = __LINE__;

            $intValTrue = intval(true);

            $merchantTable = self::TABLE_NAME;
            $language = Language_C::_getValidLanguage($language);
            $promotion = (is_numeric($promotion) || is_integer($promotion)) ? intval($promotion) : false;





            $merchantSql = Eshop_C::_getSql();

            $merchantSql->column(Eshop::ARRAY_ROW_KEY);

            $merchantSql->equal($merchantTable, Merchant_C::_col_is_valid, $intValTrue);
                // add order


            if($isNew){
                $orderBy = new SqlOrder($merchantTable, Merchant_C::_col_merchant_id_code, SqlOrder::ASC);
                $merchantSql->addOrder($orderBy);

            }
            if(is_integer($limit) && $limit > 0){
                $merchantSql
                    ->limit($limit)
                ;
            }


            if(String_Helper::_isStringNotEmpty($category)){
                $merchantSql = self::_addCategoryConditionGroup($merchantSql, $category, $response);
            }

            if(boolval($promotion)){
                $merchantSql
                    ->leftJoin(
                        $merchantTable, Merchant_C::_col_merchant_id_code,
                        Promotion_C::TABLE, Merchant_C::_col_merchant_id_code
                    )
                    ->equal(Promotion_C::TABLE, Promotion_C::_col_is_valid, $intValTrue)
                ;
            }

            $searchConditionGroup = self::_addSearchConditionGroup($searchValue);

            $merchantSql->addConditionGroup($searchConditionGroup);

            $merchantSql = self::_addPostCodeConditionGroup($merchantSql, $postCode);

            try{

                $success = $merchantSql->select(true);

                if($success){
                    $__LINE__ = __LINE__;
                    $arrayResult = $merchantSql->result();
                    $merchants = self::_resultsRowToMerchantArray($arrayResult, $language);
                    if($response instanceof RequestResponse){

                        $response
                            ->debug("merchants", $merchants)
                            ->debug("arrayResult", $arrayResult)
                        ;
                    }
                }

                if($response instanceof RequestResponse){
                    $response
                        ->debug("merchantSql", $merchantSql)
                        ->debug("searchConditionGroup", $searchConditionGroup)
                        ->debug("success", $success)
                    ;
                }
            }catch (\Exception $exception){
                $response
                    ->debug("merchantSql", $merchantSql)
                    ->exception( $exception)
                ;

            }


        }


        $categoryMerchantOption = MerchantCategory_C::_getEditorCategoryParentOption($language);
        $options["category"] = $categoryMerchantOption;
        if($response instanceof RequestResponse){
            $response
                ->_line($__LINE__)
                ->_endFile()
            ;
            $response->setOptions($options);
        }


        return $merchants;
    }


    private static function _addCategoryConditionGroup(Sql $merchantSql, $category, &$response = null) : Sql
    {
        if(String_Helper::_isStringNotEmpty($category)){

            $likePattern = SqlCondition::LIKE_PATTERN;

            $sqlConditionCategory = SqlCondition::_inst(
                self::TABLE_NAME, Merchant_C::_col_category,
                SqlCondition::LIKE, "'$category'", SqlCondition::_OR
            );
            $sqlConditionCategoryTag = SqlCondition::_inst(
                self::TABLE_NAME, Merchant_C::_col_category_tag,
                SqlCondition::LIKE, "'$likePattern$category$likePattern'", SqlCondition::_OR
            );
            $sqlCategoryGroup = SqlConditionGroup::_inst();
            $sqlCategoryGroup
                ->addCondition($sqlConditionCategory)
                ->addCondition($sqlConditionCategoryTag)
                ->setOperator(SqlCondition::_AND)
            ;
            if($response instanceof Request){
                $response
                    ->_function(__FUNCTION__, self::class)
                    ->debug("sqlCategoryGroup", $sqlCategoryGroup)
                ;
            }
            $merchantSql->addConditionGroup($sqlCategoryGroup);
        }

        return $merchantSql;
    }

    private static function _addPostCodeConditionGroup(Sql $merchantSql, $postCode) : Sql
    {

        $intValTrue = intval(true);
        $OR = SqlCondition::_OR;
        if(String_Helper::_isStringNotEmpty($postCode) || is_numeric($postCode)){
            $merchantSql
                ->leftJoin(
                    self::TABLE_NAME, Merchant_C::_col_merchant_id_code,
                    DeliveryZone_C::TABLE, Merchant_C::_col_merchant_id_code
                )
            ;
            $likePattern = SqlCondition::LIKE_PATTERN;

            $postCodeConditionGroup = new SqlConditionGroup();


            $takeAwayPostCodeCondition = new SqlCondition(
                self::TABLE_NAME, Merchant_C::_col_post_code,
                SqlCondition::LIKE, "'$likePattern$postCode$likePattern'"
            );
            $takeAwayEnableCondition = new SqlCondition(
                MerchantProfile_C::TABLE, MerchantProfile_C::_col_take_away,
                SqlCondition::EQUAL, "'$intValTrue'"
            );

            $takeAwayConditionGroup = new SqlConditionGroup([
                $takeAwayPostCodeCondition,
                $takeAwayEnableCondition
            ], $OR);
            $postCodeConditionGroup->addConditionGroup($takeAwayConditionGroup);


            $deliveryEnableCondition = new SqlCondition(
                MerchantProfile_C::TABLE, MerchantProfile_C::_col_delivery,
                SqlCondition::EQUAL, "'$intValTrue'"
            );
            $deliveryPostCodeCondition = new SqlCondition(
                DeliveryZone_C::TABLE, DeliveryZone_C::_col_post_code,
                SqlCondition::LIKE, "'$likePattern$postCode$likePattern'"
            );
            $deliveryZoneEnableCondition = new SqlCondition(
                DeliveryZone_C::TABLE, DeliveryZone_C::_col_is_enable,
                SqlCondition::EQUAL, "'$intValTrue'"
            );
            $deliveryConditionGroup = new SqlConditionGroup([
                $deliveryEnableCondition,
                $deliveryPostCodeCondition,
                $deliveryZoneEnableCondition
            ], $OR);

            $postCodeConditionGroup->addConditionGroup($deliveryConditionGroup);

            $merchantSql->addConditionGroup($postCodeConditionGroup);
        }/*else{

            $takeAwayCondition = new SqlCondition(
                MerchantProfile_C::TABLE,
                MerchantProfile_C::_col_take_away,
                SqlCondition::EQUAL,
                $intValTrue,
                $OR
            );
            $deliveryCondition = new SqlCondition(
                MerchantProfile_C::TABLE,
                MerchantProfile_C::_col_delivery,
                SqlCondition::EQUAL,
                $intValTrue,
                $OR
            );
            $takeAwayOrDeliveryConditionGroup = new SqlConditionGroup([
                $takeAwayCondition, $deliveryCondition
            ]);
            $merchantSql->addConditionGroup($takeAwayOrDeliveryConditionGroup);

        }*/

        return $merchantSql;
    }


    private static function _addSearchConditionGroup($searchValue) : ? SqlConditionGroup
    {
        if(String_Helper::_isStringNotEmpty($searchValue)){
            $arraySearchValue = explode(" ", $searchValue);
            $likePattern = SqlCondition::LIKE_PATTERN;
            $OR = SqlCondition::_OR;
            $sqlConditionGroup = new SqlConditionGroup();

            foreach ($arraySearchValue as $searchString){

                $nameCondition = new SqlCondition(
                    self::TABLE_NAME, Merchant_C::_col_commercial_name,
                    SqlCondition::LIKE, "'$likePattern$searchString$likePattern'", $OR
                );
//                $takeAwayPostCodeCondition = new SqlCondition(
//                    self::TABLE_NAME, Merchant_C::_col_post_code,
//                    SqlCondition::LIKE, "'$likePattern$searchString$likePattern'", $OR
//                );

                $sqlConditionGroup
                    ->addCondition($nameCondition)
//                    ->addCondition($takeAwayPostCodeCondition)
                ;
            }
            return $sqlConditionGroup;

        }



        return null;
    }

    private static function _resultsRowToMerchantArray (array $resultsRow, string $language): array
    {
        $arrayMerchant = [];
        $arrayId = [];
        foreach ($resultsRow as $index => $row){
            $merchantEshop = Eshop::_inst($row, $language);
            if($merchantEshop !== null && $merchantEshop instanceof Eshop){
                if( !in_array($merchantEshop->getMerchantId(), $arrayId) && $merchantEshop){
                    array_push($arrayMerchant, $merchantEshop);
                    array_push($arrayId, $merchantEshop->getMerchantId());
                }
            }
        }
        return $arrayMerchant;
    }



}