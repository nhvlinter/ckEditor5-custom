<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 13-03-20
 * Time: 15:08
 */

namespace salesteck\_base;

use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbJoinController;
use salesteck\DataTable\DataEditorOption;
use salesteck\DataTable\DataTableColumn;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\utils\CustomDateTime;
use salesteck\utils\Debug;

class Parameter_C extends Db implements DbJoinController, DbCleaner
{
    public const
        TABLE = "_parameter",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;

    protected const
        day_of_week = "day_of_week",
        multi_day_of_week = "multi_day_of_week",
        month = "month",
        default_language = "default_language"
    ;

    public static function _getParameterType(string $language = "") : array
    {
        $arrayType = [];
        $type = DataTableColumn::arrayType;
        foreach ($type as $index => $value){
            $parameterType = new \stdClass();
            $parameterType->label = $value;
            $parameterType->value = $value;
            $parameterType->type = $value;
            array_push($arrayType, $parameterType);
        }
        $dayOfWeek = self::_getTypeDaysOfWeek($language);
        array_push($arrayType, $dayOfWeek);

        $dayOfWeekMulti = self::_getTypeMultiDaysOfWeek($language);
        array_push($arrayType, $dayOfWeekMulti);

        $month = self::_getTypeMonth($language);
        array_push($arrayType, $month);

        $defaultLanguage = self::_getTypeLanguage();
        array_push($arrayType, $defaultLanguage);

        return $arrayType;
    }

    protected static function _getTypeDaysOfWeek($language) : ? \stdClass
    {
        $dayOfWeek = new \stdClass();

        $dayOfWeek->label = self::day_of_week;
        $dayOfWeek->value = self::day_of_week;
        $dayOfWeek->type = DataTableColumn::type_select;
        $dayOfWeek->options = DataEditorOption::_getDays($language);
        return $dayOfWeek;
    }

    protected static function _getTypeMultiDaysOfWeek($language) : ? \stdClass
    {
        $dayOfWeek = new \stdClass();

        $dayOfWeek->label = self::multi_day_of_week;
        $dayOfWeek->value = self::multi_day_of_week;
        $dayOfWeek->type = DataTableColumn::type_checkBox;
        $dayOfWeek->options = DataEditorOption::_getDays($language);
        return $dayOfWeek;
    }

    protected static function _getTypeMonth($language) : ? \stdClass
    {
        $dayOfWeek = new \stdClass();

        $dayOfWeek->label = self::month;
        $dayOfWeek->value = self::month;
        $dayOfWeek->type = DataTableColumn::type_select;
        $dayOfWeek->options = DataEditorOption::_getMonth($language);
        return $dayOfWeek;
    }

    private static function _getTypeLanguage() : ? \stdClass
    {
        $dayOfWeek = new \stdClass();
        $dayOfWeek->label = self::default_language;
        $dayOfWeek->value = self::default_language;
        $dayOfWeek->type = DataTableColumn::type_select;
        $dayOfWeek->options = Language_C::_getEditorOptionLanguage();
        return $dayOfWeek;
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
        $sql = self::_getSqlTranslation();
        return $sql->innerJoin(self::TABLE_TRANSLATION,self::_col_id_code, self::TABLE, self::_col_id_code);
    }

    public static function _getClassConstants() : array
    {
        $class = new \ReflectionClass(Parameter::class);
        return ($class->getConstants());
    }

    public static function _indexParameter($parameter = null){
        $arrayDebug = [];
        $arrayDebug['parameter'] = $parameter;
        $success = false;
        $dataToInsert = [];
        $sql = self::_getSql();
        if($parameter ===  null){
            $parameter = self::_getClassConstants();
        }
        if(gettype($parameter) === gettype("")){
            if($parameter !== ""){
                array_push($dataToInsert, [
                    self::_col_id_code => $parameter
                ]);
                $arrayDebug['dataToInsert'] = $dataToInsert;
                $sql->insert($dataToInsert);
                $success = self::_indexParameterTranslation($parameter);
            }
        }elseif (gettype($parameter) === gettype([])){
            foreach ($parameter as $key => $value){
                if(!self::_parameterExist($key)){
                    array_push($dataToInsert, [
                        self::_col_id_code => $key,
                        self::_col_label => $value
                    ]);
                }
            }
            if($success = $sql->bulkInsert($dataToInsert)){
                $success = self::_indexParameterTranslation($parameter);
            }
        }

        Debug::_exposeVariable($arrayDebug, false);
        return $success;
    }

    private static function _indexParameterTranslation($parameter = null){
        $arrayDebug = [];
        $arrayDebug['parameter'] = $parameter;
        $sqlTranslation = self::_getSqlTranslation();
        $arrayLang = Language_C::_getAllActiveLanguage();
        $dataToInsert = [];
        if($parameter ===  null){
            $parameter = self::_getClassConstants();
        }

        if(gettype($parameter) === gettype("")){
            foreach ($arrayLang as $language){
                if($language !== null && $language instanceof Language){
                    if(!self::_parameterTranslationExist(strval($parameter), $language->getIdCode())){
                        $row =[];
                        $row[self::_col_id_code] = $parameter;
                        $row[self::_col_language] = $language->getIdCode();
                        array_push($dataToInsert, $row);
                    }
                }
            }
        }elseif (gettype($parameter) === gettype([])){
            foreach ($arrayLang as $language){
                if($language !== null && $language instanceof Language){
                    foreach ($parameter as $index => $element){
                        if(!self::_parameterTranslationExist($index, $language->getIdCode())){
                            $row =[];
                            $row[self::_col_id_code] = $element;
                            $row[self::_col_language] = $language->getIdCode();
                            $row[self::_col_name] = $element;
                            array_push($dataToInsert, $row);
                        }
                    }
                }
            }
        }
        $arrayDebug['sqlTranslation'] = $sqlTranslation;
        $success = $sqlTranslation->bulkInsert($dataToInsert);

        Debug::_exposeVariableHtml($arrayDebug, false);
        return $success;
    }

    private static function _parameterExist(string $parameterKey) : bool
    {
        return self::_getSql()->equal(self::TABLE, self::_col_id_code, $parameterKey)->count() > 0;
    }

    private static function _parameterTranslationExist(string $parameterKey, string $language){
        $sqlTranslation = self::_getSqlTranslation();
        $sqlTranslation
            ->equal(self::TABLE_TRANSLATION, self::_col_id_code, $parameterKey)
            ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
        ;
        return $sqlTranslation->count() > 0;
    }


    public static function _getStringValParameter(string $parameterKey){
        $value = "";
        if($parameterKey !== ""){
            $sql = self::_getSql();
            $sql->equal(self::TABLE, self::_col_id_code, $parameterKey);
            if($sql->select()){
                $row = $sql->first();
                if(array_key_exists(self::_col_value, $row)){
                    $value = $row[self::_col_value];
                }
            }
        }
        return $value;
    }

    public static function _printStringValParameter(string $parameterKey){
        $parameterValue = self::_getStringValParameter($parameterKey);
        echo $parameterValue;
    }


    static function _clean(bool $debug = false)
    {
        $startTime = CustomDateTime::_getTimeStampMilli();
        $constants = self::_getClassConstants();
        $arrayId = [];
        $arrayCleaned = [];
        $sql = self::_getSql();
        if($sql->select()){
            $results = $sql->result();
            foreach ($results as $row){
                if(array_key_exists(self::_col_id_code, $row)){
                    $idCode = $row[self::_col_id_code];
                    array_push($arrayId, $idCode);
                    if( is_string($idCode) && $idCode !== "" && !array_key_exists($idCode, $constants) ){
                        array_push($arrayCleaned, $idCode);
                        $sqlDelete = self::_getSql();
                        $sqlDelete->equal(self::TABLE, self::_col_id_code, $idCode);
                        if($sqlDelete->delete()){
                            $sqlTranslation = self::_getSqlTranslation();
                            $sqlTranslation->equal(self::TABLE_TRANSLATION, self::_col_id_code, $idCode);
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
                if(array_key_exists(self::_col_id_code, $row)) {
                    $idCode = $row[self::_col_id_code];
                    if( is_string($idCode) && $idCode !== "" && !array_key_exists($idCode, $constants) ){
                        array_push($arrayCleaned, $idCode);
                        $sqlTranslationDelete = self::_getSqlTranslation();
                        $sqlTranslationDelete->equal(self::TABLE_TRANSLATION, self::_col_id_code, $idCode);
                        $sqlTranslationDelete->delete();
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





    public static function getParameter(string $parameterKey){
        if(!self::_parameterExist($parameterKey)){
            self::_indexParameter($parameterKey);
        }
        $sql = self::_getSql();
        $sql->equal(self::TABLE, self::_col_id_code, $parameterKey);
        if($sql->count() === 1 && $sql->select() ){
            $row = $sql->first();
            return array_key_exists(self::_col_value, $row) ? $row[self::_col_value] : null;
        }
        return null;
    }
}