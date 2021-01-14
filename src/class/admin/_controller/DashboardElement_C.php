<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 30-06-20
 * Time: 01:10
 */

namespace salesteck\_base;

use salesteck\admin\PortalLanguage_C;
use salesteck\admin\PortalPage_C;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\merchant\Merchant_C;
use salesteck\order\Order_C;
use salesteck\product\Product_C;
use salesteck\product\ProductCategory_C;
use salesteck\promotion\Promotion_C;
use salesteck\takeAwayDelivery\DeliveryHours_C;
use salesteck\takeAwayDelivery\DeliveryZone_C;
use salesteck\takeAwayDelivery\TakeAwayHours_C;

class DashboardElement_C
{


    /**
     * get sales dashboard elements
     * @param string $language
     * @param string $merchantIdCode
     * @return array (DashboardElement)
     */
    public static function _getSalesElements(string $language = "", string $merchantIdCode){
        $language = PortalLanguage_C::_getValidLanguage($language);
        $arrayElement = [
            self::_getElement(
                $language, Order_C::TABLE, PortalPage_C::pageOrder, $merchantIdCode
            ),
            self::_getActiveElement(
                $language, Promotion_C::TABLE, PortalPage_C::pagePromotion, $merchantIdCode
            ),
            self::_getActiveElement(
                $language, ProductCategory_C::TABLE, PortalPage_C::pageProductCategory, $merchantIdCode
            ),
            self::_getActiveElement(
                $language, Product_C::TABLE, PortalPage_C::pageProduct, $merchantIdCode
            )
        ];
        $returnElement = [];
        foreach ($arrayElement as $element){
            if($element instanceof DashboardElement){
                array_push($returnElement, $element);
            }
        }

        return $returnElement;
    }


    /**
     * get config dashboard elements
     * @param string $language
     * @param string $merchantIdCode
     * @return array (DashboardElement)
     * @internal param string $merchantIdCode
     */
    public static function _getConfig(string $language = "", string $merchantIdCode){
        $language = PortalLanguage_C::_getValidLanguage($language);
        $arrayElement = [
            self::_getActiveElement(
                $language, TakeAwayHours_C::TABLE, PortalPage_C::pageTakeAwayHours, $merchantIdCode
            ),
            self::_getActiveElement(
                $language, DeliveryZone_C::TABLE, PortalPage_C::pageDeliveryZone, $merchantIdCode
            ),
            self::_getActiveElement(
                $language, DeliveryHours_C::TABLE, PortalPage_C::pageDeliveryHours, $merchantIdCode
            )
        ];
        $returnElement = [];
        foreach ($arrayElement as $element){
            if($element instanceof DashboardElement){
                array_push($returnElement, $element);
            }
        }

        return $returnElement;
    }


    /**
     * get dashboard element without active items
     * @param string $language
     * @param string $tableName
     * @param string $pagePath
     * @param string $merchantIdCode
     * @return null|DashboardElement
     */
    private static function _getElement(string $language, string $tableName, string $pagePath, string $merchantIdCode = ""){
        if($tableName !== ""){

            $sql = Sql::_inst($tableName);
            if(is_string($merchantIdCode)&& $merchantIdCode !== ""){
                $sql->equal($tableName, Merchant_C::_col_merchant_id_code, $merchantIdCode);
            }
            $element = $sql->count();
            return DashboardElement::_inst(
                PortalPage_C::_getPageByPath($pagePath, $language),
                $element
            );
        }
        return null;
    }


    /**
     * get dashboard element with active items
     * @param string $language
     * @param string $tableName
     * @param string $pagePath
     * @param string $merchantIdCode
     * @return null|DashboardElement
     */
    private static function _getActiveElement(string $language, string $tableName, string $pagePath, string $merchantIdCode = ""){
        if($tableName !== ""){
            $sql = Sql::_inst($tableName);
            if(is_string($merchantIdCode)&& $merchantIdCode !== ""){
                $sql->equal($tableName, Merchant_C::_col_merchant_id_code, $merchantIdCode);
            }
            $element = $sql->count();
            $sql->equal($tableName, Db::_col_is_enable, intval(true));
            $active = $sql->count();
            return DashboardElement::_inst(
                PortalPage_C::_getPageByPath($pagePath, $language),
                $element, $active
            );
        }
        return null;
    }



}