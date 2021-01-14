<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 14-08-20
 * Time: 12:38
 */

namespace salesteck\DataCard;


use salesteck\security\Password;
use salesteck\utils\CustomDateTime;

class FieldFormat
{


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Field formatter methods
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    /**
     * SET formatter
     * return function template
     * param $value The value to be formatted
     * param $dataRow The collection of data for the row
     *      including
     *
     * function ( $fieldValue, $dataRow, $dataGroup, $options ){
     *      return formatted value|null if null is returned, the value wont be set
     * }
     *
     */


    /**
     * @return string
     */
    public static function _setUrl(){
        return function ($fieldValue, $dataRow, $dataGroup, $options){
            return implode('/', array_map('urlencode', explode('/', $fieldValue)));

        };
    }


    /**
     * @return string
     */
    public static function _setPassword(){
        return function ($fieldValue, $dataRow, $dataGroup, $options){
            return Password::_hash($fieldValue);

        };
    }


    /**
     * @return string
     */
    public static function _setPrice(){
        return function ($fieldValue, $dataRow, $dataGroup, $options){
            return abs(intval($fieldValue));

        };
    }


    /**
     * @return string
     */
    public static function _setRemoveSpace(){
        return function ($fieldValue, $dataRow, $dataGroup, $options){
            return str_replace(" ", "", $fieldValue);

        };
    }
    /**
     * @return string
     */
    public static function _setFormatDate(){
        return function ($fieldValue, $dataRow, $dataGroup, $options){
            return CustomDateTime::_formatToTimeStamp($fieldValue, CustomDateTime::F_DATE, true);

        };
    }



    /**
     * GET formatter
     * return function template
     * param $value The value to be formatted
     * param $dataRow The collection of data for the row
     *      including
     *
     * function ( $fieldValue, $dataRow, $options ){
     *      return formatted value|null if null is returned, the value wont be set
     * }
     *
     */


    /**
     * @return string
     */
    public static function _getFormatDate(){
        return function ($fieldValue, $dataRow, $options){
            return CustomDateTime::_timeStampToFormat(intval($fieldValue), CustomDateTime::F_DATE, true);

        };
    }




}