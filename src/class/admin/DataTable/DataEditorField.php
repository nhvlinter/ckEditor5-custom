<?php

namespace salesteck\DataTable;
use DataTables\Editor\Field;
use salesteck\admin\AdminI18;
use salesteck\admin\AdminI18_C;
use salesteck\Db\Sql;
use salesteck\utils\CustomDateTime;

/**
 * Created by PhpStorm.
 * User: Son
 * Date: 29-04-20
 * Time: 13:09
 */

class DataEditorField
{
    private static function is_assoc(array $arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public static function _getField(string $tableName, string $fieldName){
        return Field::inst($tableName. '.' .$fieldName);
    }

    public static function _getDateField(string $tableName, string $fieldName){
        $field = self::_getField($tableName, $fieldName)
            ->validator( function ( $val, $data, $field, $host ){
                return CustomDateTime::_isValidDate($val, CustomDateTime::F_DATE) || $val === "" ? true :  'invalid date';
            })
        ;
        return $field;
    }

    public static function _validateEmptyString($language){
        return function ($val, $data, $field, $host) use ($language){
            if($val !== null){
                $val = trim(strval($val));
                $i18n = AdminI18_C::_getInstance($language);
                $errorMessage = AdminI18_C::_getValueUcFirst(AdminI18::EDITOR_ERROR_EMPTY_VALUE, $i18n);
                return $val !== "" ? true : $errorMessage;

            }
            return true;
        };
    }

    public static function _validateRequireImage($language){
        return function ($val, $data, $field, $host) use ($language){
            if($val !== null){
                $i18n = AdminI18_C::_getInstance($language);
                $errorMessage = AdminI18_C::_getValueUcFirst(AdminI18::EDITOR_ERROR_EMPTY_IMAGE, $i18n);
                $val = strval($val);
                return $val !== "" ? true : $errorMessage;

            }
            return true;
        };

    }

    public static function _validateNumeric($language){
        return function ($val, $data, $field, $host) use ($language){
            if($val !== null){
                $i18n = AdminI18_C::_getInstance($language);
                $errorMessage = AdminI18_C::_getValueUcFirst(AdminI18::EDITOR_ERROR_INTEGER_VALUE, $i18n);
                return is_numeric($val) ? true : $errorMessage;
            }
            return true;
        };
    }

    public static function _validateStringLength($language, int $strLength){
        return function ($val, $data, $field, $host) use ($language, $strLength){
            if($val !== null){
                $val = trim(strval($val));
                $i18n = AdminI18_C::_getInstance($language);
                $errorMessage = AdminI18_C::_getValueUcFirst(AdminI18::EDITOR_ERROR_STR_LENGTH, $i18n) . " : " . $strLength;
                return strlen($val) >= $strLength ? true : $errorMessage;
            }
            return true;
        };
    }

    public static function _validateUniqueField($val, string $language, string $tableName, string $columnName, array $columnId = []){

        $i18n = AdminI18_C::_getInstance($language);
        $errorMessage = AdminI18_C::_getValueUcFirst(AdminI18::EDITOR_ERROR_VALUE_EXIST, $i18n);
        $val = strval($val);
        if($tableName !== "" && $columnName !== ""){
            $sql = Sql::_inst($tableName);
            $sql->equal($tableName, $columnName, $val)
            ;
            if(sizeof($columnId) === 1 && $columnName && self::is_assoc($columnId)){
                $column = array_keys($columnId)[0];
                $value = array_key_exists($column, $columnId) ? $columnId[$column] : null;
                if($value !== null){
                    $sql->different($tableName, $column, $value);
                }
            }
            if($sql->select()){
                $rowCount = $sql->count();
                return $rowCount > 0 ?
                    $errorMessage :
                    true;
            }
        }
        return $errorMessage;
    }

    public static function _validateCompare(int $var1, int $var2, string $op, string $language){
        $i18n = AdminI18_C::_getInstance($language);
        $errorMessage = AdminI18_C::_getValueUcFirst(AdminI18::EDITOR_ERROR_EMPTY_VALUE, $i18n);
        $isValid = false;
        switch ($op) {
            case "=":
                $isValid = $var1 == $var2;
                break;
            case "!=":
                $isValid =  $var1 != $var2;
                break;
            case ">=":
                $isValid =  $var1 >= $var2;
                break;
            case "<=":
                $isValid =  $var1 <= $var2;
                break;
            case ">":
                $isValid =  $var1 >  $var2;
                break;
            case "<":
                $isValid =  $var1 <  $var2;
                break;
        }
        return $isValid ? $isValid : $errorMessage;
    }
}