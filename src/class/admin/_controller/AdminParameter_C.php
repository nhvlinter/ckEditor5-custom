<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 19-03-20
 * Time: 15:57
 */

namespace salesteck\admin;


use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbJoinController;
use salesteck\_base\Language;
use salesteck\_base\Parameter_C;
use salesteck\DataTable\DataTableColumn;
use salesteck\Db\Sql;
use salesteck\utils\CustomDateTime;
use salesteck\utils\Debug;

class AdminParameter_C extends Parameter_C implements DbJoinController, DbCleaner
{
    public const
        TABLE = "_admin_parameter",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;

    static function _getParameterType(string $language = "") : array
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

    private static function _getTypeLanguage() : ? \stdClass
    {
        $language = new \stdClass();
        $language->label = self::default_language;
        $language->value = self::default_language;
        $language->type = DataTableColumn::type_select;
        $language->options = AdminLanguage_C::_getEditorOptionLanguage();
        return $language;
    }


    public static function _getClassConstants() : array
    {
        $class = new \ReflectionClass(AdminParameter::class);
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
        if(is_string($parameter) && $parameter !== ""){
            $dataToInsert[self::_col_id_code] = $parameter;
            $arrayDebug['dataToInsert'] = $dataToInsert;
            $sql->insert($dataToInsert);
            $success = self::_indexParameterTranslation($parameter);
        }
        elseif (gettype($parameter) === gettype([])){
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
        $arrayLang = AdminLanguage_C::_getAllActiveLanguage();
        $dataToInsert = [];
        if($parameter ===  null){
            $parameter = self::_getClassConstants();
        }

        if(is_string($parameter) && $parameter !== ""){
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