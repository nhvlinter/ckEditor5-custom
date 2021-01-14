<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 01-08-20
 * Time: 23:48
 */

namespace salesteck\_base;


use salesteck\utils\String_Helper;

class Page_View
{


    public static function _getArrayPages(string $languageCode, array $pages){
        $languageCode = Language_C::_getValidLanguage($languageCode);
        $returnPages = [];
        foreach ($pages as $pagePath){
            if(String_Helper::_isStringNotEmpty($pagePath)){
                $page = Page_C::_getPageByPath($pagePath, $languageCode);
                if($page instanceof Page){
                    if($page->isEnable()){
                        array_push($returnPages, $page);
                    }
                }
            }
        }
        return $returnPages;

    }

    public static function _getRoute($page, array $routeVariable = []) : string
    {
        if($page instanceof Page){
            $route = $page->getRoute();
            $routeVariable = sizeof($routeVariable)>0 ? implode("/", $routeVariable) : "";

            return $route.$routeVariable;
        }
        return "";
    }

    public static function printRoute($page, array $routeVariable = []){
        echo self::_getRoute($page, $routeVariable);
    }

    public static function printText($page){
        if($page instanceof Page){
            echo $page->getText();
        }
    }

}