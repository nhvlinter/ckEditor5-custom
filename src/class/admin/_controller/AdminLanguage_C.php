<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 06-11-19
 * Time: 16:55
 */

namespace salesteck\admin;


use salesteck\_base\Language;
use salesteck\_base\Language_C;
use salesteck\_base\Page;
use salesteck\config\Config;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\utils\Debug;

class AdminLanguage_C extends Db
{
    private const _FR = "fr", _NL = "nl", _EN = "en";

    public const _DEFAULT_LANGUAGE_CODE = Config::defaultLanguage;

    public const
        TABLE = "_admin_language"
    ;

    public static function _count(array $columnValue = []){
        $sql = self::_getSql();
        foreach ($columnValue as $columnName => $value){
            $sql->equal(self::TABLE, $columnName, $value);
        }
        return $sql->count();
    }

    public static function _getValidLanguage(string $language) : string
    {
        if($language !== ""){
            if(self::_count([self::_col_language => $language]) > 0){
                return $language;
            }
        }
        return self::_getDefaultLanguage();
    }


    /**
     * get the the default language of the app
     * @return string
     */
    public static function _getDefaultLanguage(): string
    {
        $defaultLanguage = self::_DEFAULT_LANGUAGE_CODE;
        $sql = self::_getSql();
        $sql
            ->equal(self::TABLE, self::_col_is_default, strval(true))
        ;
        if($sql->select()){
            $rowLanguage = Language::_inst($sql->first());
            if($rowLanguage !== null && $rowLanguage instanceof Language){
                $defaultLanguage = $rowLanguage->getIdCode() !== "" ? $rowLanguage->getIdCode() : $defaultLanguage;
            }
        }
        Debug::_exposeVariable(["sql"=>$sql], false);
        return $defaultLanguage;
    }

    public static function _getBrowserLanguage(){
        return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }

    private static function _getLanguageFromDb(bool $activeLanguage = false) : array
    {
        $allLanguage = [];
        $sql = self::_getSql();
        if($activeLanguage){
            $sql->equal(self::TABLE, self::_col_is_enable, strval($activeLanguage));
        }
        if($sql->select()){
            $result = $sql->result();
            foreach ($result as $row){
                $language = Language::_inst($row);
                if($language !== null && $language instanceof Language){
                    array_push($allLanguage, $language);
                }
            }

        }
        return $allLanguage;
    }
    
    public static function _getAllActiveLanguage() : array
    {
        return self::_getLanguageFromDb(true);
    }

    public static function _getLanguageFromCode(string $languageCode = self::_FR) : Language
    {
        $language = self::_getDefaultLanguage();
        $sql = self::_getSql();
        $sql
            ->equal(self::TABLE, self::_col_language, $languageCode)
        ;
        if($sql->select()){
            $rowLanguage = Language::_inst($sql->first());
            if($rowLanguage !== null && $rowLanguage instanceof Language){
                $language = $rowLanguage;
            }
        }
        return $language;
    }

    public static function _getLanguageFromUri() : string
    {
        $page = AdminPage_C::_getPageByUri();
        $languageCode = self::_getDefaultLanguage();
        if($page instanceof Page){
            $pageLanguage = $page->getLanguage();
            if($pageLanguage !== ""){
                $languageCode = $pageLanguage;
            }
        }
        return $languageCode;
    }


    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }


    public static function _getEditorOptionLanguage()
    {
        $options = [];
        $sql = self::_getSql();
        $sql
            ->orderAsc(self::TABLE, self::_col_name)
            ->equal(self::TABLE, self::_col_is_enable, intval(true))
        ;
        if($sql->select()){
            $arrayResult = $sql->result();
            foreach ($arrayResult as $row){
                if(
                    array_key_exists(Language_C::_col_name, $row) &&
                    array_key_exists(Language_C::_col_language, $row)
                ){
                    $options[$row[Language_C::_col_name]] = $row[Language_C::_col_language];
                }
            }
        }
        return $options;
    }


    public static function _getMultiItemLanguage() : array
    {
        $multiItems = [];

        $allLanguage = self::_getAllActiveLanguage();

        foreach ($allLanguage as $language){
            if($language instanceof Language){
                array_push($multiItems, $language->getIdCode());
            }
        }
        return $multiItems;
    }


}