<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 06-11-19
 * Time: 15:55
 */

namespace salesteck\admin;

use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbControllerObject;
use salesteck\_interface\DbJoinController;
use salesteck\_base\Language;
use salesteck\_base\Page;
use salesteck\_base\Page_C;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\utils\CustomDateTime;
use salesteck\utils\Debug;
use salesteck\utils\File;
use salesteck\utils\String_Helper;

class PortalPage_C extends Db implements DbJoinController, DbControllerObject, DbCleaner
{
    public const Src = "/portal";
    private const pageDir =         self::Src."/src/page/";
    public const
        pagePortalConfig =      self::pageDir."_portal-config/index.php",
        pagePortalLanguage =    self::pageDir."_portal-language/index.php",
        pagePortalPage =        self::pageDir."_portal-page/index.php",
        pagePortalUser =        self::pageDir."_portal-user/index.php",
        pageConfig =            self::pageDir."config/index.php",
        pageCustomer =          self::pageDir."customer/index.php",
        pageDashboard =         self::pageDir."dashboard/index.php",
        pagePreview =           self::pageDir."preview/index.php",
        pageMerchant =          self::pageDir."merchant/index.php",
        pageDeliveryHours =     self::pageDir."delivery-hours/index.php",
        pageDeliveryZone =      self::pageDir."delivery-zone/index.php",
        pageError =             self::pageDir."error-404/index.php",
        pageLogin =             self::pageDir."login/index.php",
        pageOrder =             self::pageDir."order/index.php",
        pageParameter =         self::pageDir."parameter/index.php",
        pageProduct =           self::pageDir."product/index.php",
        pageProductOption =     self::pageDir."product-option/index.php",
        pageProductOptionCat =  self::pageDir."product-option-category/index.php",
        pageProductAllergen =   self::pageDir."product-allergen/index.php",
        pageProductCategory =   self::pageDir."product-category/index.php",
        pagePromotion =         self::pageDir."promotion/index.php",
        pageSocialMedia =       self::pageDir."social-media/index.php",
        pageTakeAwayHours =     self::pageDir."take-away-hours/index.php"
    ;

    public static function _getUri(){
        return Page_C::_getUri();
    }
    /**
     *
     */
    public const
        TABLE = "_portal_page",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;

    public static function _getPageLink(string $filePath, string $language = "")
    {
        $link = "";
        $language = PortalLanguage_C::_getValidLanguage($language);
        $arrayDebug = [];

        $page = self::_getPageByPath($filePath, $language);
        $arrayDebug ["page"] = $page;

        if($page === null){
            self::_indexPage($filePath);
            $page = self::_getPageByPath($filePath, $language);
            $arrayDebug ["page"] = $page;
        }
        if($page!== null && $page instanceof Page){
            $link = $page->getRoute();
        }
        $arrayDebug ["link"] = $link;

        Debug::_exposeVariable($arrayDebug, false);
        return  $link;
    }

    public static function _getPageByUri() : ? Page
    {
        $page = null;
        $uri = self::_getUri();
        $uri = strtolower($uri);
        $arrayDebug = [];
        $uri = String_Helper::_startsWith($uri, "/") ? $uri : "/$uri";
        $uri = String_Helper::_endsWith($uri, "/") ? $uri : "$uri/";
        $arrayDebug ["uri"] = $uri;

        $joinSql = self::_getJoinSql();
        $joinSql
            ->equal(self::TABLE_TRANSLATION, self::_col_route, $uri)
            ->equal(self::TABLE, self::_col_is_enable, strval(true))
        ;
        if($joinSql->select()){
            $row = $joinSql->first();
            $arrayDebug ["row"] = $row;
            $page =  self::_getObjectClassFromResultRow($row);
        }
        $arrayDebug ["joinSql"] = $joinSql;
        Debug::_exposeVariable($arrayDebug, false);
        return $page;
    }

    public static function _getPagePathFromUri() : string
    {
        $uri = PortalPage_C::_getUri();
        if($uri === "/"){
             return PortalPage_C::pageDashboard;
        }else{
            $page = self::_getPageByUri();
            if($page instanceof Page){
                return $page->getAbsolutePath();
            }
        }
        return PortalPage_C::pageError;

    }

    public static function _getPageByPath(string $pagePath, string $lang = "") : ? Page
    {
        if($pagePath !== ""){
            $lang = PortalLanguage_C::_getValidLanguage($lang);
            $joinSql = self::_getJoinSql();
            $joinSql
                ->equal(self::TABLE, self::_col_file_absolute_path, $pagePath)
                ->equal(self::TABLE_TRANSLATION, self::_col_language, $lang)
            ;
            if($joinSql->select()){
                $rows = $joinSql->result();
                if(sizeof($rows)>0){
                    $row = $rows[0];
                    return self::_getObjectClassFromResultRow($row);
                }
            }
//            Debug::_exposeVariableHtml(['joinSql'=>$joinSql], true);
        }
        return null;
    }

    public static function _indexPage(string $filePath){
        if(File::_fileExist($filePath)){
            $file = new File($filePath);
            $label = $file->getDirLabel();
            if( $file->getExtension() === "php"){

                $idCode = self::_createUniqueId(
                    self::TABLE, self::_col_id_code
                );
                $absolutePath = $filePath;
                if(!self::_pathAlreadyExist(addslashes($absolutePath))){
                    $row = [
                        self::_col_id_code => $idCode,
                        self::_col_file_absolute_path => $absolutePath,
                        self::_col_label => $label,
                        self::_col_is_enable => strval(true)
                    ];

                    $sql = self::_getSql();
                    if($sql->insert($row, self::_col_id_code)){
                        $activeLanguage = PortalLanguage_C::_getAllActiveLanguage();

                        foreach ($activeLanguage as $langIndex => $lang) {
                            if ($lang instanceof Language && $lang !== null) {
                                $langCode = $lang->getIdCode();
                                $sqlCheckRowExist = Sql::_inst(self::TABLE_TRANSLATION);
                                $sqlCheckRowExist
                                    ->equal(self::TABLE_TRANSLATION,self::_col_id_code, $idCode)
                                    ->equal(self::TABLE_TRANSLATION,self::_col_language, $langCode)
                                ;
                                $elementCount = $sqlCheckRowExist->count();

                                $createDate = CustomDateTime::_getTimeStamp();
                                $lastModified = $createDate;
                                if($elementCount === 0){
                                    $rowTranslation = [
                                        self::_col_label => $label,
                                        self::_col_id_code => $idCode,
                                        self::_col_title => "",
                                        self::_col_description => "",
                                        self::_col_keywords => "",
                                        self::_col_language => $langCode,
                                        self::_col_route => PortalPage_C::Src."/$langCode/$label/"

                                    ];

                                    $sqlTranslation = self::_getSqlTranslation();
                                    $sqlTranslation->insert($rowTranslation, self::_col_id);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public static function _indexAllPage(string $path =  self::pageDir)
    {
        $arrayDebug = [];
        $folderElements = File::_getFilesFromFolder($path);
        $arrayDebug['folderElements'] = $folderElements;


        $arrayInsert = [];

        $arrayElements = [];
        foreach ($folderElements as $index =>  $element){
            $filePath = $path.$element."/index.php";

            if(File::_fileExist($filePath)){

                $file = new File($filePath);
                $arrayElements[$index] = $file;


                $extension = $file->getExtension();
                if($extension === "php"){

                    $idCode = self::_createUniqueId(
                        self::TABLE, self::_col_id_code
                    );
                    $absolutePath = $filePath;
                    if(!self::_pathAlreadyExist(addslashes($absolutePath))){
                        $row = [
                            self::_col_id_code => $idCode,
                            self::_col_file_absolute_path => $absolutePath,
                            self::_col_label => $file->getDirLabel()
                        ];
                        array_push($arrayInsert, $row);
                    }
                }

            }

        }

        $arrayDebug['arrayElements'] = $arrayElements;
        $arrayDebug['$arrayInsert'] = $arrayInsert;

        $sql = self::_getSql();
        $sql->bulkInsert($arrayInsert);
        $arrayDebug['sql'] = $sql;

        self::_indexPageTranslation();

        Debug::_exposeVariableHtml($arrayDebug, false);

    }

    private static function _indexPageTranslation(){
        $arrayDebug = [];
        $sql = self::_getSql();
        if($sql->select()){
            $pages = $sql->result();
            $arrayDebug['pages'] = $pages;
            $arrayInsert = [];
            $activeLanguage = PortalLanguage_C::_getAllActiveLanguage();

            $arrayDebug['activeLanguage'] = $activeLanguage;
            foreach ($activeLanguage as $langIndex => $lang) {
                if ($lang instanceof Language && $lang !== null) {
                    $langCode = $lang->getIdCode();
                    $arrayDebug["langCode[$langIndex]"] = $langCode;

                    foreach ($pages as $index => $page){
                        if(array_key_exists(self::_col_id_code, $page) && array_key_exists(self::_col_file_absolute_path, $page)){
                            $idCode = $page[self::_col_id_code];
                            $arrayDebug["idCode[$index]"] = $idCode;

                            $label = $page[self::_col_label];
                            $arrayDebug["label[$index]"] = $label;

                            $sqlCheckRowExist = Sql::_inst(self::TABLE_TRANSLATION);
                            $sqlCheckRowExist
                                ->equal(self::TABLE_TRANSLATION,self::_col_id_code, $idCode)
                                ->equal(self::TABLE_TRANSLATION,self::_col_language, $langCode)
                            ;
                            $elementCount = $sqlCheckRowExist->count();
                            $arrayDebug["elementCount[$index]"] = $elementCount;

                            $createDate = CustomDateTime::_getTimeStamp();
                            $lastModified = $createDate;
                            if($elementCount===0){
                                $row = [
                                    self::_col_link_text => $label,
                                    self::_col_id_code => $idCode,
                                    self::_col_title => "",
                                    self::_col_description => "",
                                    self::_col_keywords => "",
                                    self::_col_language => $langCode,
                                    self::_col_route => PortalPage_C::Src."/$langCode/$label/"

                                ];
                                array_push($arrayInsert, $row);
                            }
                        }
                    }
                }
            }
            $arrayDebug['arrayInsertData'] = $arrayInsert;
            $sqlTranslation = self::_getSqlTranslation();
            $sqlTranslation->bulkInsert($arrayInsert, self::_col_id);
            $arrayDebug['sqlTranslation'] = $sqlTranslation;
        }


        Debug::_exposeVariableHtml($arrayDebug, true);

    }

    private static function _pathAlreadyExist(string $path): bool
    {
        $sql = self::_getSql();
        $sql->equal(self::TABLE, self::_col_file_absolute_path, $path);
        return $sql->count() > 0;
    }



    static function _getSql() : Sql
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

    static function _getObjectClassFromResultRow($row)
    {
        if(
            $row !== null && gettype($row) === gettype([]) &&
            array_key_exists(self::_col_id, $row) &&
            array_key_exists(self::_col_is_enable, $row) &&
            array_key_exists(self::_col_id_code, $row) &&
            array_key_exists(self::_col_link_text, $row) &&
            array_key_exists(self::_col_file_absolute_path, $row) &&
            array_key_exists(self::_col_create_date, $row) &&
            array_key_exists(self::_col_last_modified, $row) &&
            array_key_exists(self::_col_label, $row) &&
            array_key_exists(self::_col_title, $row) &&
            array_key_exists(self::_col_description, $row) &&
            array_key_exists(self::_col_language, $row) &&
            array_key_exists(self::_col_route, $row)
        ){
            return new Page(
                $row[self::_col_id],
                $row[self::_col_id_code],
                $row[self::_col_link_text],
                $row[self::_col_file_absolute_path],
                $row[self::_col_create_date],
                $row[self::_col_last_modified],
                $row[self::_col_is_enable],
                $row[self::_col_label],
                $row[self::_col_title],
                $row[self::_col_description],
                $row[self::_col_keywords],
                $row[self::_col_language],
                $row[self::_col_route]

            );
        }else{
            return null;
        }
    }


    static function _clean(bool $debug = false)
    {

        $startTime = CustomDateTime::_getTimeStampMilli();
        $sql = self::_getSql();
        $arrayCleaned = [];
        $sqlTranslation = self::_getSqlTranslation();
        if ($sql->select()) {
            $results = $sql->result();
            foreach ($results as $row) {
                if (array_key_exists(self::_col_file_absolute_path, $row) && array_key_exists(self::_col_id_code, $row)) {
                    $filePath = $row[self::_col_file_absolute_path];
                    $idCode = $row[self::_col_id_code];
                    if ($filePath === "" || !File::_fileExist($filePath)) {
                        $sql->equal(self::TABLE, self::_col_id_code, $idCode);
                        if($sql->delete()){
                            array_push($arrayCleaned, $row);
                            $sqlTranslation->equal(self::TABLE_TRANSLATION, self::_col_id_code, $idCode);
                            $sqlTranslation->delete();
                        }
                    }
                }
            }
        }


        $endTime = CustomDateTime::_getTimeStampMilli();
        if ($debug) {
            echo "<pre>" . json_encode(
                    [
                        "class" => __CLASS__,
                        "sql" => $sql,
                        "sqlTranslation" => $sqlTranslation,
                        "arrayCleaned" => $arrayCleaned,
                        "processTime" => $endTime - $startTime
                    ],
                    JSON_PRETTY_PRINT
                ) . "</pre>";
        }
    }



}