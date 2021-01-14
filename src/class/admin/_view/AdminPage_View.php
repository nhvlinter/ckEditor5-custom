<?php
namespace salesteck\admin;
use salesteck\_base\Page;
use salesteck\utils\String_Helper;

/**
 * Created by PhpStorm.
 * User: Son
 * Date: 01-08-20
 * Time: 23:47
 */

class AdminPage_View
{


    public static function _getRequestPages(string $languageCode) : array
    {
        $languageCode = AdminLanguage_C::_getValidLanguage($languageCode);
        $returnPages = [];
        $arrayPages = [
            AdminPage_C::_getPageByPath(AdminPage_C::pageContactPage, $languageCode)
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


    public static function _getMerchantPages(string $languageCode) : array
    {
        $languageCode = AdminLanguage_C::_getValidLanguage($languageCode);
        $returnPages = [];
        $arrayPages = [
            AdminPage_C::_getPageByPath(AdminPage_C::pageOrder, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pagePromotion, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageProductCategory, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageProduct, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageProductOptionCat, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageProductOption, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageOpeningHours, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageTakeAwayHours, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageDeliveryZone, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageDeliveryHours, $languageCode)
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
        $languageCode = AdminLanguage_C::_getValidLanguage($languageCode);
        $returnPages = [];
        $arrayPages = [
            AdminPage_C::_getPageByPath(AdminPage_C::pageCustomer, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageMerchant, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageContactPage, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageNewsletter, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageFaq, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageSocialMedia, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageGallery, $languageCode)
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
        $languageCode = AdminLanguage_C::_getValidLanguage($languageCode);
        $returnPages = [];
        $arrayPages = [
            AdminPage_C::_getPageByPath(AdminPage_C::pageConfig, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pagePayment, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageMerchantCategory, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageProductAllergen, $languageCode)
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
        $languageCode = AdminLanguage_C::_getValidLanguage($languageCode);
        $returnPages = [];
        $arrayPages = [
            AdminPage_C::_getPageByPath(AdminPage_C::pageAdminUser, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageWebPage, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageLanguage, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageConstant, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageParameter, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageAdminPage, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageAdminLanguage, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageAdminConstant, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageAdminParameter, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pageAdminConfig, $languageCode),
            AdminPage_C::_getPageByPath(AdminPage_C::pagePortalPage, $languageCode)
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

    public static function printRoute($page, $queryUrl = ""){
        if($page instanceof Page){
            echo $page->getRoute();
            if(String_Helper::_isStringNotEmpty($queryUrl)){
                echo $queryUrl;
            }
        }
    }

    public static function printText($page){
        if($page instanceof Page){
            echo $page->getText();
        }
    }
}