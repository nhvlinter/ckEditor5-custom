<?php
namespace salesteck\admin;
use salesteck\_base\Page;

/**
 * Created by PhpStorm.
 * User: Son
 * Date: 01-08-20
 * Time: 23:47
 */

class PortalPage_View
{


    public static function _getRequestPages(string $languageCode) : array
    {
        $languageCode = PortalLanguage_C::_getValidLanguage($languageCode);
        $returnPages = [];
        $arrayPages = [
        ];

        foreach ($arrayPages as $page){
            if($page instanceof Page){
                if($page->isEnable()){

                    array_push($returnPages, $page);
                }
            }
        }
        return $returnPages;
    }


    public static function _getSalesPages(string $languageCode) : array
    {
        $languageCode = PortalLanguage_C::_getValidLanguage($languageCode);
        $returnPages = [];
        $arrayPages = [
            PortalPage_C::_getPageByPath(PortalPage_C::pageCustomer, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageOrder, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pagePromotion, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageProductCategory, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageProduct, $languageCode)
        ];

        foreach ($arrayPages as $page){
            if($page instanceof Page){
                if($page->isEnable()){

                    array_push($returnPages, $page);
                }
            }
        }
        return $returnPages;
    }


    public static function _getManagementPages(string $languageCode) : array
    {
        $languageCode = PortalLanguage_C::_getValidLanguage($languageCode);
        $returnPages = [];
        $arrayPages = [
            PortalPage_C::_getPageByPath(PortalPage_C::pageCustomer, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageSocialMedia, $languageCode)
        ];

        foreach ($arrayPages as $page){
            if($page instanceof Page){
                if($page->isEnable()){

                    array_push($returnPages, $page);
                }
            }
        }
        return $returnPages;
    }


    public static function _getConfigPages(string $languageCode) : array
    {
        $languageCode = PortalLanguage_C::_getValidLanguage($languageCode);
        $returnPages = [];
        $arrayPages = [
            PortalPage_C::_getPageByPath(PortalPage_C::pageMerchant, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageCustomer, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pagePromotion, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageProductCategory, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageProduct, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageProductOptionCat, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageProductOption, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageTakeAwayHours, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageDeliveryZone, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageDeliveryHours, $languageCode)
        ];

        foreach ($arrayPages as $page){
            if($page instanceof Page){
                if($page->isEnable()){

                    array_push($returnPages, $page);
                }
            }
        }
        return $returnPages;
    }


    public static function _getSuperAdminPages(string $languageCode) : array
    {
        $languageCode = PortalLanguage_C::_getValidLanguage($languageCode);
        $returnPages = [];
        $arrayPages = [
            PortalPage_C::_getPageByPath(PortalPage_C::pagePortalUser, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageProductAllergen, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pageParameter, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pagePortalPage, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pagePortalLanguage, $languageCode),
            PortalPage_C::_getPageByPath(PortalPage_C::pagePortalConfig, $languageCode)
        ];

        foreach ($arrayPages as $page){
            if($page instanceof Page){
                if($page->isEnable()){
                    array_push($returnPages, $page);
                }
            }
        }
        return $returnPages;
    }

    public static function printRoute($page){
        if($page instanceof Page){
            echo $page->getRoute();
        }
    }

    public static function printText($page){
        if($page instanceof Page){
            echo $page->getText();
        }
    }
}