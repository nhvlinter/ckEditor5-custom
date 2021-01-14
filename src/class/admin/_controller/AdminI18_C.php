<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 07-11-19
 * Time: 02:34
 */

namespace salesteck\admin;


use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbControllerTranslation;
use salesteck\_base\Language;
use salesteck\_base\Language_C;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\utils\CustomDateTime;
use salesteck\utils\Debug;

class AdminI18_C extends Db implements DbControllerTranslation, DbCleaner
{
    public const
        TABLE = "_admin_i18",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;
    private static $instance, $instanceLanguage;



    public static function _getInstance(string $language = "") : array
    {
        if( !is_array(self::$instance) || $language !== self::$instanceLanguage){
            $language = Language_C::_getValidLanguage($language);
            $translation = [];
            $language = AdminLanguage_C::_getValidLanguage($language);
            $sqlTranslation = self::_getSqlTranslation();
            $sqlTranslation
                ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
            ;
            if($sqlTranslation->select()){
                $result = $sqlTranslation->result();
                foreach ($result as $row){
                    if(
                        array_key_exists(self::_col_key, $row) &&
                        array_key_exists(self::_col_value, $row)
                    ){
                        $translation[$row[self::_col_key]] = $row[self::_col_value];
                    }
                }
            }

            self::$instance = $translation;
            self::$instanceLanguage = $language;
        }
        return self::$instance;
    }


    /**
     * get the value from the key
     * @param $key
     * @param null $i18n
     * @return string
     */
    public static function _getValueFromKey(string $key, $i18n) : string
    {
        if($i18n === null || gettype($i18n) !== gettype([])){
            $i18n = self::_getInstance();
        }

        $return = "{undefined:$key}";
        if ( array_key_exists($key, $i18n)) {
            $return = ($i18n[$key]);
        }else{
            $success = self::_indexConstant($key);
            if($success){
                $return = "{".$key."}";
            }
        }
        return $return;
    }

    public static function _getValueUcFirst(string $key, $i18n, array $rep = []){
        $val = ucfirst(self::_getValueFromKey($key, $i18n));
        foreach ($rep as $key => $value){
            $val = str_replace($key, $value, $val);
        }
        return $val;
    }

    public static function _getValueToUc(string $key, $i18n){
        return strtoupper(self::_getValueFromKey($key, $i18n));
    }

    /**
     * print the value from the key with first letter in upper case
     * @param $value
     * @param null $i18n
     */
    public static function _printValueUcFirst($value, $i18n){
        print self::_getValueUcFirst($value, $i18n);
    }

    /**
     * print the value from the key all letter in upper case
     * @param $value
     * @param null $i18n
     */
    public static function _printValueToUc($value, $i18n){
        print self::_getValueToUc($value, $i18n);
    }

    /**
     * print value from the key no modified
     * @param $value
     * @param null $i18n
     */
    public static function _printValueFromKey($value, $i18n)
    {
        print self::_getValueFromKey($value, $i18n);
    }

    public static function _indexConstant($constants = null) : bool
    {
        $arrayDebug = [];
        $arrayDebug['constants'] = $constants;
        $success = false;
        $sql = self::_getSql();
        if($constants ===  null){
            $constants = self::_getClassConstants();
        }
        if(gettype($constants) === gettype("")){
            if($constants !== ""){
                if($sql->insert([self::_col_key => $constants])){
                    $success = self::_indexConstantTranslation($constants);
                }
            }
        }elseif (gettype($constants) === gettype([])){
            $dataToInsert = [];
            foreach ($constants as $key => $value){
                if(!self::_indexExist($key)){
                    array_push($dataToInsert, [self::_col_key => $key]);
                }
            }
            $arrayDebug['dataToInsert'] = $dataToInsert;
            if($success = $sql->bulkInsert($dataToInsert)){
                $success = self::_indexConstantTranslation($constants);
            }
        }

        Debug::_exposeVariable($arrayDebug, false);
        return $success;
    }

    private static function _indexConstantTranslation($constants = null) : bool
    {
        $arrayDebug = [];
        $arrayDebug['constants'] = $constants;
        $sqlTranslation = self::_getSqlTranslation();
        $arrayLang = AdminLanguage_C::_getAllActiveLanguage();
        $dataToInsert = [];
        if($constants ===  null){
            $constants = self::_getClassConstants();
        }

        if(gettype($constants) === gettype("")){
            foreach ($arrayLang as $language){
                if($language !== null && $language instanceof Language){
                    if(!self::_elementExist(strval($constants), $language->getIdCode())){
                        $row =[];
                        $row[self::_col_key] = $constants;
                        $row[self::_col_language] = $language->getIdCode();
                        $row[self::_col_value] = '{'.$constants.'}';
                        array_push($dataToInsert, $row);
                    }
                }
            }
        }elseif (gettype($constants) === gettype([])){
            foreach ($arrayLang as $language){
                if($language !== null && $language instanceof Language){
                    foreach ($constants as $index => $element){
                        if(!self::_elementExist($element, $language->getIdCode())){
                            $row =[];
                            $row[self::_col_key] = $element;
                            $row[self::_col_language] = $language->getIdCode();
                            $row[self::_col_value] = '{'.$element.'}';
                            array_push($dataToInsert, $row);
                        }
                    }
                }
            }
        }
        $success = $sqlTranslation->bulkInsert($dataToInsert);

        Debug::_exposeVariable($arrayDebug, false);
        return $success;

    }

    private static function _getClassConstants() : array
    {
        $class = new \ReflectionClass(AdminI18::class);
        return ($class->getConstants());
    }

    private static function _elementExist(string $key, string $language){
        $sqlTranslation = self::_getSqlTranslation();
        $sqlTranslation
            ->equal(self::TABLE_TRANSLATION, self::_col_key, $key)
            ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
        ;
        return $sqlTranslation->count() > 0;
    }

    private static function _indexExist(string $key) : bool
    {
        return self::_getSql()->equal(self::TABLE, self::_col_key, $key)->count() > 0;
    }

    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getSqlTranslation(): Sql
    {
        return Sql::_inst(self::TABLE_TRANSLATION);
    }

    static function _getJoinSql(): Sql
    {
        return self::_getSqlTranslation()->innerJoin(self::TABLE_TRANSLATION,self::_col_key, self::TABLE, self::_col_key);
    }

    static function _clean(bool $debug = false)
    {
        $startTime = CustomDateTime::_getTimeStampMilli();
        $constants = self::_getClassConstants();
        $sql = self::_getSql();
        $arrayCleaned = [];
        if($sql->select()){
            $results = $sql->result();
            foreach ($results as $row){
                if(array_key_exists(self::_col_key, $row)){
                    $key = $row[self::_col_key];
                    if(is_string($key) && $key !== "" && !array_key_exists($key, $constants) ){
                        array_push($arrayCleaned, $key);
                        $sql->equal(self::TABLE, self::_col_key, $key);
                        if($sql->delete()){
                            $sqlTranslation = self::_getSqlTranslation();
                            $sqlTranslation->equal(self::TABLE_TRANSLATION, self::_col_key, $key);
                            $sqlTranslation->delete();
                        }
                    }
                }
            }
        }

        $sqlTranslation = self::_getSqlTranslation();
        if($sqlTranslation->select()){
            $resultsTranslation = $sqlTranslation->result();
            foreach ($resultsTranslation as $row){
                if(array_key_exists(self::_col_key, $row)) {
                    $key = $row[self::_col_key];
                    if(is_string($key) && $key !== "" && !array_key_exists($key, $constants) ){
                        array_push($arrayCleaned, $key);
                        $sqlTranslation->equal(self::TABLE_TRANSLATION, self::_col_key, $key);
                        $sqlTranslation->delete();
                    }
                }
            }
        }

        $endTime = CustomDateTime::_getTimeStampMilli();
        if($debug){
            echo "<pre>". json_encode(
                    [
                        "class" => __CLASS__,
                        "arrayCleaned" => $arrayCleaned,
                        "processTime" => $endTime - $startTime
                    ],
                    JSON_PRETTY_PRINT
                )."</pre>";
        }
    }
}