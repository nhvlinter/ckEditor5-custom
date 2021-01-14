<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 25-09-19
 * Time: 15:21
 */

namespace salesteck\_base;



use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbControllerTranslation;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\utils\CustomDateTime;

class I18_C extends Db implements DbControllerTranslation, DbCleaner
{
    public const
        TABLE = "_i18",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;



    public static function _getInstance(string $language = "")
    {
        $translation = [];
        $language = Language_C::_getValidLanguage($language);

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
        return $translation;
    }


    /**
     * get the value from the key
     * @param $key
     * @param null $i18n
     * @return string
     */
    public static function _getValueFromKey($key, $i18n = null) : string
    {
        if($i18n === null){
            $i18n = self::_getInstance();
        }
        $return = "{undefined:$key}";
        if ( array_key_exists($key, $i18n)) {
            $return = $i18n[$key];
        }else{
            $success = self::_indexConstant($key);
            if($success){
                $return = "{".$key."}";
            }
        }
        return $return;
    }

    /**
     * get the value from the key with first letter in upper case
     * @param $value
     * @param null $i18n
     * @return string
     */
    public static function _getValueUcFirst($value, $i18n = null){

        return ucfirst(self::_getValueFromKey($value, $i18n));
    }

    /**
     * print the value from the key with first letter in upper case
     * @param $value
     * @param null $i18n
     */
    public static function _printValueUcFirst($value, $i18n = null){

        print ucfirst(self::_getValueFromKey($value, $i18n));
    }

    /**
     * print the value from the key all letter in upper case
     * @param $value
     * @param null $i18n
     */
    public static function _printValueToUc($value, $i18n = null){
        print strtoupper(self::_getValueFromKey($value, $i18n));
    }

    /**
     * print value from the key no modified
     * @param $value
     * @param null $i18n
     */
    public static function _printValueFromKey($value, $i18n = null)
    {
        print self::_getValueFromKey($value, $i18n);
    }

    public static function _indexConstant($constant) : bool
    {
        $success = false;
        $sql = self::_getSql();
        if(gettype($constant) === gettype("")){
            if($sql->insert([self::_col_key => $constant])){
                self::_indexConstantTranslation($constant);
            }
        }else{
            $constants = self::_getClassConstants();
            $dataToInsert = [];
            foreach ($constants as $key => $value){
                if(!self::_indexExist($key)){
                    array_push($dataToInsert, [self::_col_key => $key]);
                }
            }
            if($sql->bulkInsert($dataToInsert)){
                $success = self::_indexConstantTranslation($constants);
            }
        }
        return $success;
    }

    private static function _indexConstantTranslation($constant) : bool
    {
        $sqlTranslation = self::_getSqlTranslation();
        $arrayLang = Language_C::_getAllActiveLanguage();
        $dataToInsert = [];
        if(gettype($constant) === gettype("")){
            foreach ($arrayLang as $language){
                if($language !== null && $language instanceof Language){
                    if(!self::_elementExist($constant, $language->getIdCode())){
                        $row =[];
                        $row[self::_col_key] = $constant;
                        $row[self::_col_language] = $language->getIdCode();
                        $row[self::_col_value] = '{'.$constant.'}';
                        array_push($dataToInsert, $row);
                    }
                }
            }
        }else{
            foreach ($arrayLang as $language){
                if($language !== null && $language instanceof Language){
                    foreach ($constant as $element){
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
        return $success;

    }

    private static function _getClassConstants() : array
    {
        $class = new \ReflectionClass(I18::class);
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
        return self::_getSql()->equal(self::TABLE, self::_col_key, $key)->count() >0 ;
    }

    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getSqlTranslation(): Sql
    {
        return Sql::_inst(self::TABLE_TRANSLATION);
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