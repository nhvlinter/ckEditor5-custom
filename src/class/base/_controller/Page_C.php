<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 06-11-19
 * Time: 15:55
 */

namespace salesteck\_base;


use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbJoinController;
use salesteck\Db\CodeGenerator;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\utils\CustomDateTime;
use salesteck\utils\Debug;
use salesteck\utils\File;
use salesteck\utils\String_Helper;
use salesteck\utils\Session;

/**
 * Class Page_C
 * @package salesteck\_base
 */
class Page_C extends Db implements DbJoinController, DbCleaner
{
    public const SrcDir = "/src/page/";

    public const
        TABLE = "_page",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;

    /**
     * get url without parameter
     * @return string
     */
    public static function _getUri() : string
    {
        Debug::_trace();
        $uri = strtok($_SERVER["REQUEST_URI"],'?');
        Debug::_exposeVariable(['uri'=>$uri], false);
        return $uri;
    }

    /**
     *
     */
    public const
        pageHome =                  self::SrcDir."home/index.php",

        pageAboutUs =               self::SrcDir."about-us/index.php",
        pageContact =               self::SrcDir."contact/index.php",

        pageError =                 self::SrcDir."error-404/index.php",

        pageUnderConstruction =     self::SrcDir."under-construction/index.php",
        pageFaq =                   self::SrcDir."faq/index.php",
        pageGallery =               self::SrcDir."gallery/index.php",
        pageLegal =                 self::SrcDir."legal/index.php",
        pageMaintenance =           self::SrcDir."maintenance/index.php",
        pagePrivacy =               self::SrcDir."privacy/index.php",
        pageRequest =               self::SrcDir."request/index.php",
        pageSiteMap =               self::SrcDir."sitemap/index.php",

        pageService_solution =               self::SrcDir."service-solution/index.php",
        pageService_ecommerce =               self::SrcDir."service-e-commerce/index.php",
        pageService_website =               self::SrcDir."service-website/index.php",





        pageEshop =                 self::SrcDir."eshop/index.php",

        pageCheckOut =                 self::SrcDir."checkout/index.php",

        pageMerchant =              self::SrcDir."merchant/index.php",
        pageSearch =                   self::SrcDir."search/index.php",

        pageCustomerAccount =       self::SrcDir."account/index.php",

        pageSignUp =                self::SrcDir."signup/index.php",
        pageOrderConfirmation =     self::SrcDir."confirm/index.php"
    ;


    /**
     * @param array $columnsValues
     * @param array $columnsValuesTranslation
     *
     * @return array
     */
    private static function _getPage (array $columnsValues = [], array $columnsValuesTranslation = []) : array
    {
        $arrayResult = [];
        $sql = self::_getJoinSql();
        foreach ($columnsValues as $columnName => $value){
            if(is_string($columnName) && $columnName !== "" && is_string($value) && $value !== ""){
                $sql->equal(self::TABLE, $columnName, $value);
            }
        }
        foreach ($columnsValuesTranslation as $columnName => $value){
            if(is_string($columnName) && $columnName !== "" && is_string($value) && $value !== ""){
                $sql->equal(self::TABLE_TRANSLATION, $columnName, $value);
            }
        }
        if($sql->select()){
            $arrayResult = $sql->result();
        }
        return $arrayResult;

    }

    /**
     * @param string $pagePath
     * @param string $lang
     *
     * @return null|\salesteck\_base\Page
     */
    public static function _getPageByPath(string $pagePath, string $lang = "") : ? Page
    {
        if($lang === ""){
            $lang = Session::_getLanguage();
        }

        $results = self::_getPage(
            [self::_col_file_absolute_path => $pagePath],
            [self::_col_language => $lang]
        );

        if(sizeof($results) > 0 ){
            return Page::_inst($results[0]);
        }
        return null;
    }

    /**
     * @param string $pagePath
     * @param string $lang
     *
     * @return null|\salesteck\_base\Page
     */
    public static function _getPageByPathWithVariable(string $pagePath, string $lang = "") : ? Page
    {
        $page = null;
        if($lang === ""){
            $lang = Language_C::_getValidLanguage($lang);
        }

        $results = self::_getPage(
            [self::_col_file_absolute_path => $pagePath, self::_col_route_variable => intval(true)],
            [self::_col_language => $lang]
        );

        if(sizeof($results) > 0 ){
            return Page::_inst($results[0]);
        }
        return $page;
    }

    /**
     * @return null|\salesteck\_base\Page
     */
    public static function _getPageByUri() : ? Page
    {
        $arrayDebug = [];
        $page = null;
        $uri = self::_getUri();
        $uri = strtolower($uri);
        $uri = String_Helper::_startsWith($uri, "/") ? $uri : "/$uri";
        $uri = String_Helper::_endsWith($uri, "/") ? $uri : "$uri/";

        $arrayDebug ["uri"] = $uri;

        $sql = self::_getJoinSql();
        $sql
            ->equal(self::TABLE, self::_col_is_enable, intval(true))
        ;

        if($uri === "/"){
            $language = Language_C::_getDefaultLanguage();
            $sql
                ->equal(self::TABLE, self::_col_file_absolute_path, Page_C::pageHome)
                ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
            ;

        }else{
            $sql->equal(self::TABLE_TRANSLATION, self::_col_route, $uri);
        }

        if($sql->select()){
            $row = $sql->first();
            $arrayDebug ["row"] = $row;
            $page = Page::_inst($row);
        }
        $arrayDebug ["sql"] = $sql;
        Debug::_exposeVariable($arrayDebug, false);

        if($page === null){
            $page = self::_getPageByUriWithVariable();
        }
        return $page;
    }

    /**
     * @return null|\salesteck\_base\Page
     */
    public static function _getPageByUriWithVariable() : ? Page
    {
        $arrayDebug = [];
        $page = null;
        $uri = self::_getUri();
        $uri = strtolower($uri);
        $uri = String_Helper::_startsWith($uri, "/") ? $uri : "/$uri";
        $uri = String_Helper::_endsWith($uri, "/") ? $uri : "$uri/";

        $arrayDebug ["uri"] = $uri;


        $sql = self::_getJoinSql();
        if($uri !== "/"){
            $sql
                ->equal(self::TABLE, self::_col_is_enable, intval(true))
                ->equal(self::TABLE, self::_col_route_variable, intval(true))
            ;
            if( $sql->select()){
                $results = $sql->result();
                foreach ($results as $row){
                    if(array_key_exists(self::_col_route, $row)){
                        $url = $row[self::_col_route];
                        if (strpos($uri, $url) === 0) {
                            $page = Page::_inst($row);
                        }
                    }
                }
            }
        }
        Debug::_exposeVariable($arrayDebug, false);
        return $page;
    }


    /**
     * index all the page from a folder
     * @param string $path
     */
    public static function _indexAllPage(string $path =  self::SrcDir)
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
                        self::TABLE, self::_col_id_code, self::ID_LENGTH, CodeGenerator::LETTER
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

        self::_indexPageTranslation();

        Debug::_exposeVariableHtml($arrayDebug, true);

    }

    /**
     *
     */
    private static function _indexPageTranslation(){
        $arrayDebug = [];
        $sql = self::_getSql();
        if($sql->select()){
            $pages = $sql->result();
            $arrayDebug['pages'] = $pages;
            $arrayInsert = [];
            $activeLanguage = Language_C::_getAllActiveLanguage();

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
                                    self::_col_id_code => $idCode,
                                    self::_col_title => "",
                                    self::_col_description => "",
                                    self::_col_keywords => "",
                                    self::_col_language => $langCode,
                                    self::_col_route => "/$langCode/$label/",
                                    self::_col_create_date => $createDate,
                                    self::_col_last_modified => $lastModified,

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


        Debug::_exposeVariableHtml($arrayDebug, false);

    }

    /**
     * @param string $filePath
     */
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
                        self::_col_is_enable => strval(true),
                        self::_col_label => $label
                    ];

                    $sql = self::_getSql();
                    if($sql->insert($row, self::_col_id_code)){
                        $activeLanguage = Language_C::_getAllActiveLanguage();

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
                                        self::_col_route => "/$langCode/$label/"

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

    /**
     * @param string $filePath
     * @param string $language
     *
     * @return string
     */
    public static function _getPageLink(string $filePath, string $language = ""){
        $link = "";
        $language = Language_C::_getValidLanguage($language);
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
        return $link;
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
        $sql = Sql::_inst(self::TABLE_TRANSLATION);
        return $sql->innerJoin(self::TABLE_TRANSLATION,self::_col_id_code, self::TABLE, self::_col_id_code);
    }

    static function _clean(bool $debug = false)
    {

        $startTime = CustomDateTime::_getTimeStampMilli();
        $sql = self::_getSql();
        $arrayCleaned = [];
        if ($sql->select()) {
            $results = $sql->result();
            foreach ($results as $row) {
                if (array_key_exists(self::_col_file_absolute_path, $row) && array_key_exists(self::_col_id_code, $row)) {
                    $filePath = $row[self::_col_file_absolute_path];
                    $idCode = $row[self::_col_id_code];
                    if ($filePath === "" || !File::_fileExist($filePath)) {
                        array_push($arrayCleaned, $row);
                        $sql->equal(self::TABLE, self::_col_id_code, $idCode);
                        if($sql->delete()){
                            $sqlTranslation = self::_getSqlTranslation();
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
                        "arrayCleaned" => $arrayCleaned,
                        "processTime" => $endTime - $startTime
                    ],
                    JSON_PRETTY_PRINT
                ) . "</pre>";
        }
    }


    public static function _getRouteVariable($page, $uri =null) : array
    {
        $variable = [];
        $uri = String_Helper::_isStringNotEmpty($uri) ? $uri : self::_getUri();
        if($page !== null && $page instanceof Page){
            if($uri === "/"){
                $uri = str_replace("/", "", $uri);
            }
            $routeVariable = str_replace($page->getRoute(), "", $uri);
            $variable = explode("/", $routeVariable);
        }

        return $variable;
    }

    public static function _getContentPage(string $language, $__FILE__){
        $dirName = File::_getDirNameAbsolutePath($__FILE__);
        return "$dirName/$language".File::PHP_EXT;
    }
}