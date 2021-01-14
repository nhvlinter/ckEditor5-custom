<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 06-11-19
 * Time: 16:55
 */

namespace salesteck\_base;




use salesteck\_interface\DbControllerObject;
use salesteck\config\Config;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\utils\Debug;

/**
 * Class Language_C
 * @package salesteck\_base
 */
class Language_C extends Db implements DbControllerObject
{
    const _FR = "fr", _NL = "nl", _EN = "en";
    private const _DEFAULT_ICON = "flag-icon-fr";


    public const
        TABLE = "_language"
    ;

    public static function _count(array $columnValue = []){
        $sql = self::_getSql();
        foreach ($columnValue as $columnName => $value){
            $sql->equal(self::TABLE, $columnName, $value);
        }
        return $sql->count();
    }

    public static function _getValidLanguage($language) : string
    {
        if(is_string($language) && $language !== ""){
            if(self::_count([self::_col_language=>$language]) > 0){
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
        $defaultLanguage = Config::defaultLanguage;
        $sql = self::_getSql();
        $sql
            ->equal(self::TABLE, self::_col_is_default, intval(true))
        ;
        if($sql->select()){
            $rowLanguage = $sql->first();
            if(array_key_exists(self::_col_language, $rowLanguage)){
                $defaultLanguage = $rowLanguage[self::_col_language];
            }
        }
        Debug::_exposeVariable(["sql"=>$sql], false);
        return $defaultLanguage;
    }

    public static function _getBrowserLanguage(){
        return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }

    public static function _getLanguageFromDb(bool $activeLanguage = false) : array
    {
        $allLanguage = [];
        $sql = self::_getSql();
        if($activeLanguage){
            $sql->equal(self::TABLE, self::_col_is_enable, strval($activeLanguage));
        }
        if($sql->select()){
            $result = $sql->result();
            foreach ($result as $row){
                $language = self::_getObjectClassFromResultRow($row);
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
            $rowLanguage = self::_getObjectClassFromResultRow($sql->first());
            if($rowLanguage !== null && $rowLanguage instanceof Language){
                $language = $rowLanguage;
            }
        }
        return $language;
    }

    public static function _getLanguageFromUri() : string
    {

        $page = Page_C::_getPageByUri();
        $languageCode = self::_getDefaultLanguage();
        if($page instanceof Page){
            $languageCode = $page->getLanguage();
        }
        return $languageCode;
    }



    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getObjectClassFromResultRow($row) : ? Language
    {
        if(
            $row !== null && gettype($row) === gettype([]) &&
            array_key_exists(self::_col_language, $row) &&
            array_key_exists(self::_col_name, $row) &&
            array_key_exists(self::_col_icon, $row) &&
            array_key_exists(self::_col_is_enable, $row)
        ){
            return new Language(
                $row[self::_col_language],
                $row[self::_col_name],
                $row[self::_col_icon],
                $row[self::_col_is_enable]
            );
        }else{
            return null;
        }
    }

    static function _getEditorOptionLanguage(){
        $options = [];
        $sql = self::_getSql();
        $sql
            ->orderAsc(self::TABLE, self::_col_name)
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

    static function _indexLanguage(){

    }
}