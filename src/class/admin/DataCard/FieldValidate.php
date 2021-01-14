<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 14-07-20
 * Time: 02:51
 */

namespace salesteck\DataCard;


use salesteck\admin\AdminI18;
use salesteck\admin\AdminI18_C;
use salesteck\admin\AdminParameter;
use salesteck\_base\Image_c;
use salesteck\api\RequestResponse;
use salesteck\config\Config;
use salesteck\Db\Sql;
use salesteck\utils\CustomDateTime;
use salesteck\utils\FileUpload;

class FieldValidate
{

    private Const
        REGEX_PHONE = '/^0[0-9]{8,12}/',
//        REGEX_PHONE = '/^((\+)32|0|0032)[1-9](\d{2}){4}$/',
        REGEX_PASSWORD = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,20}$/'
    ;

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * GLOBAL validation methods
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    /**
     *
     * return function template
     * parameter :
     *      $editor, instance of DataCard
     *      $action, action name is either read, create, edit, remove or upload
     *      $data, data submitted by the client
     *
     * function ($editor, $action, $data){
     *      return string|true (string : error message )
     * }
     *
     * if that function return a string, an error message will display on the editor
     *
     */


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Field validation methods
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    /**
     *
     * return function template
     * param $value field value
     * param $dataRow row data
     * param $field DataCard\Field instance
     * param $dataCard DataCard\DataCard instance
     * param $id id data ( edit event ) ( action event : $id = null )
     *
     * function ( $value, $dataRow, $field, $dataCard, $id ){
     *      return string|true (string : error message )
     * }
     *
     */

    /**
     * validate unique value in the table
     *
     * @param string $tableName
     * @param string $idColumnName
     * @param string $columnName
     * @param string $message
     * @param null   $response
     *
     * @return \Closure
     */
    static function unique(string $tableName, string $idColumnName, string $columnName, string $message, &$response = null){
        return function ( $value, $dataRow, $field, $dataCard, $id) use ( $tableName, $idColumnName, $columnName, $message, $response){
            $sql = Sql::_inst($tableName);
            $sql->equal($tableName, $columnName, $value);
            if($id !== null){
                $sql->different($tableName, $idColumnName, $id);
            }
            $count = $sql->count();
            if($response instanceof RequestResponse){
                $response->_file()->_line(__LINE__)->debug("sql", $sql);
            }
            return $count > 0 ? $message : true ;
        };
    }

    /**
     * validate value's minimum
     * @param int $compare
     * @param string $message
     * @return \Closure
     */
    static function min(int $compare, string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use ($compare, $message){
            if(is_numeric($value)){
                $value = intval($value);
                return $value >= $compare ? true : "$message $compare";
            }
            return "$message $compare" ;
        };
    }

    /**
     * validate value's minimum
     * @param string $message
     * @return \Closure
     */
    static function numeric(string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use ($message){
            return is_numeric($value) ? true : $message ;
        };
    }

    /**
     * validate value's maximum
     * @param int $compare
     * @param string $message error message
     * @return \Closure
     */
    static function max(int $compare, string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use ($compare, $message){
            if(is_numeric($value)){
                $value = intval($value);
                return $value <= $compare ? true : "$message $compare";
            }
            return "$message $compare" ;
        };
    }

    /**
     * validate value's maximum
     * @param int $compare
     * @param string $message error message
     * @return \Closure
     */
    static function equal($compare, string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use ($compare, $message){
            return $value === $compare ? true : "$message $compare";
        };
    }

    /**
     * validate value is greater than
     * @param int $compare
     * @param string $message error message
     * @return \Closure
     */
    static function greater(int $compare, string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use ($compare, $message){
            if(is_numeric($value)){
                $value = intval($value);
                return $value > $compare ? true : "$message $compare";
            }
            return "$message $compare" ;
        };
    }

    /**
     * validate value is lower than
     * @param int $compare
     * @param string $message error message
     * @return \Closure
     */
    static function lower(int $compare, string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use ($compare, $message){
            if(is_numeric($value)){
                $value = intval($value);
                return $value < $compare ? true : "$message $compare";
            }
            return "$message $compare" ;
        };
    }

    /**
     * validate value is not empty
     * @param string $message error message
     * @return \Closure
     */
    static function notEmpty(string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use($message){
            return $value !== "" ? true : $message;
        };
    }

    /**
     * validate string value's length minimum
     * @param int $compare
     * @param string $message error message
     * @return \Closure
     */
    static function minLength(int $compare, string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use($compare, $message){
            return strlen($value) >= $compare ? true : "$message $compare";
        };
    }

    /**
     * validate string value's length maximum
     * @param int $compare
     * @param string $message error message
     * @return \Closure
     */
    static function maxLength(int $compare, string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use($compare, $message){
            return strlen($value) <= abs($compare) ? true : "$message $compare";
        };
    }

    /**
     * validate string value's length maximum
     * @param int $compare
     * @param string $message error message
     * @return \Closure
     */
    static function lengthEqual(int $compare, string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use($compare, $message){
            return strlen($value) === abs($compare) ? true : "$message $compare";
        };
    }

    /**
     * validate string value's must contain
     * @param string $check
     * @param string $message error message
     * @return \Closure
     */
    static function strContains(string $check, string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use($check, $message){
            return strpos($value, $check) === false ? "$message $check" : true;
        };
    }

    /**
     * validate string value's must contain
     * @param string $check
     * @param string $message error message
     * @return \Closure
     */
    static function strNoContains(string $check, string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use($check, $message){
            return strpos($value, $check) === false ? true : "$message $check" ;
        };
    }

    /**
     * validate string value's must end with $check
     * @param string $check
     * @param string $message error message
     * @return \Closure
     */
    static function strStart(string $check, string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use($check, $message){
            return strpos($value, $check) === 0 ?  true : "$message $check";
        };
    }

    /**
     * validate string value's must end with $check
     * @param string $check
     * @param string $message error message
     * @return \Closure
     */
    static function strNoStart(string $check, string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use($check, $message){
            return strpos($value, $check) !== 0 ?  true : "$message $check";
        };
    }

    /**
     * validate string value's must start with $check
     * @param string $check
     * @param string $message error message
     * @return \Closure
     */
    static function strEnd(string $check, string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use($check, $message){
            $length = strlen( $check );
            if( !$length ) {
                return true;
            }
            return substr( $value, -$length ) === $check ? true : "$message $check";
        };
    }

    static function noSpace(string $message){
        return self::strNoContains(" ", $message);
    }

    static function passWord(string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use($message){
            return preg_match_all (self::REGEX_PASSWORD, $value) ? true : $message ;
        };
    }

    static function email(string $message){
        return function ($value, $dataRow, $field, $dataCard, $id) use($message){
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return $message;
            }
            return true;

        };
    }

    static function phone(string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use($message){
            return preg_match_all(self::REGEX_PHONE, $value) ? true : $message ;
        };
    }

    static function dateGreater(string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use ($message){
            if(is_numeric($value)){
                $value = intval($value);
                return $value >= CustomDateTime::_getTimeStamp() ? true : $message;
            }
            return $message ;
        };
    }

    static function dateLower(string $message){
        return function ( $value, $dataRow, $field, $dataCard, $id) use ($message){
            if(is_numeric($value)){
                $value = intval($value);
                return $value <= CustomDateTime::_getTimeStamp() ? true : $message;
            }
            return $message ;
        };
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * FILE validation methods
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    /**
     *
     * return function template
     * param $file FileUpload instance
     *
     * function ( $file ){
     *      return string|true (string : error message )
     * }
     * @param array $i18n
     * @param array $extensions
     * @param int $maxSize
     * @return \Closure
     */

    /**
     * validate file's extensions is in valid extension
     * validate file's size
     * @param array $i18n
     * @param array $extensions
     * @param int $maxSize
     * @return \Closure
     */
    static function imageMaxSize(array $i18n = [], array $extensions = [], int $maxSize = 0){
        return function ( $file )use($i18n, $extensions, $maxSize){
            $extensions = sizeof($extensions) > 0 ? $extensions : Image_c::ALLOWED_IMAGE_EXT;
            $maxSize = $maxSize > 0 ? $maxSize : AdminParameter::_maxImageSize();
            $message = "";
            $isValid = false;
            if($file instanceof FileUpload){
                $ext = $file->getExtension();
                for ( $i=0, $ien=count($extensions) ; $i<$ien ; $i++ ) {
                    if ( strtolower( $ext ) === strtolower( $extensions[$i] ) ) {
                        $isValid = true;
                    }
                }
                if($isValid === false){
                    $message = str_replace(
                        DataCard::INDEX_EXT_REPLACE, implode($extensions, ', '),
                        AdminI18_C::_getValueUcFirst(AdminI18::EDITOR_ERROR_EXT, $i18n)
                    );
                }else{
                    if($file instanceof FileUpload && $file->getSize() <= $maxSize){
                        $isValid = true;
                    }else{
                        $isValid = false;
                        $message = str_replace(
                            DataCard::INDEX_SIZE_REPLACE, Config::octetToString($maxSize),
                            AdminI18_C::_getValueUcFirst(AdminI18::EDITOR_ERROR_IMAGE_SIZE, $i18n)
                        );
                        $message = str_replace(
                            DataCard::INDEX_MAX_SIZE_REPLACE, Config::octetToString($file->getSize()), $message
                        );
                    }

                }
            }
            return $isValid ? true : $message;
        };
    }

    /**
     * validate file's extensions is in valid extension
     * @param array $extensions files extensions
     * @param string $message error message
     * @return \Closure
     */
    static function fileExtensions ( array $extensions, $message = "This file type cannot be uploaded." ) {
        return function ( $file ) use ( $extensions, $message ) {
            if($file instanceof FileUpload){
                $ext = $file->getExtension();
                for ( $i=0, $ien=count($extensions) ; $i<$ien ; $i++ ) {
                    if ( strtolower( $ext ) === strtolower( $extensions[$i] ) ) {
                        return true;
                    }
                }
            }
            $message = str_replace(DataCard::INDEX_EXT_REPLACE, implode($extensions, ', '), $message);
            return $message;
        };
    }

    /**
     * validate file's size
     * @param int $fileSize
     * @param string $message
     * @return \Closure
     */
    static function fileSize ( int $fileSize, $message = "Uploaded file is too large." ) {
        return function ( $file ) use ( $fileSize, $message ) {
            if($file instanceof FileUpload && $file->getSize() <= $fileSize){
                return true;
            }

            $message = str_replace(DataCard::INDEX_SIZE_REPLACE, Config::octetToString($fileSize), $message);
            return $message;
        };
    }

}