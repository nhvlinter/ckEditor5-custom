<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 24-10-19
 * Time: 23:03
 */

namespace salesteck\Db;


use Exception;
use PDO;
use salesteck\config\Config;
use salesteck\utils\CustomDateTime;
use salesteck\utils\String_Helper;

/**
 * Class Db Database connector main class
 * @package salesteck\Db
 */
abstract class Db
{
    public const
        Password = Config::Password,
        DefaultCollation = Config::DefaultCollation,
        DefaultCharset = Config::DefaultCharset,
        Engine = Config::Engine,
        ID_LENGTH = 6,
        _TRANSLATION = "_translation",
        _IMAGES = "_images",
        _col = Config::_col
    ;
    public const ARRAY_DELIMITER = "|";


    /**
     * @const
     */
    public const

        _col_address =              self::_col."_address",
        _col_attempt =              self::_col."_attempt",
        _col_category =             self::_col."_category",
        _col_category_id =          self::_col."_category_id",
        _col_category_parent =      self::_col."_category_parent",
        _col_category_tag =         self::_col."_category_tag",
        _col_class =                self::_col."_class",
        _col_code =                 self::_col."_code",
        _col_color =                self::_col."_color",
        _col_comment =              self::_col."_comment",
        _col_create_date =          self::_col."_create_date",
        _col_customer_id_code =     self::_col.'_customer_id_code',
        _col_date =                 self::_col."_date",
        _col_days =                 self::_col."_days",
        _col_delivery_fee =         self::_col."_delivery_fee",
        _col_delivery_payment =     self::_col.'_delivery_payment',
        _col_delivery_zone_id =     self::_col."_delivery_zone_id",
        _col_description =          self::_col."_description",
        _col_email =                self::_col."_email",
        _col_end_time =             self::_col."_end_time",
        _col_file_absolute_path =   self::_col."_file_absolute_path",
        _col_file_name =            self::_col."_file_name",
        _col_file_size =            self::_col."_file_size",
        _col_icon =                 self::_col."_icon",
        _col_id =                   self::_col."_id",
        _col_id_code =              self::_col_id."_code",
        _col_image =                self::_col."_image",
        _col_is_accept =            self::_col."_is_accept",
        _col_is_authenticated =     self::_col."_is_authenticated",
        _col_is_default =           self::_col."_is_default",
        _col_is_display =           self::_col."_is_display",
        _col_is_editable =          self::_col."_is_editable",
        _col_is_enable =            self::_col."_is_enable",
        _col_is_online =            self::_col."_is_online",
        _col_is_multi_use =         self::_col."_is_multi_use",
        _col_is_multiple =          self::_col."_is_multiple",
        _col_is_promotion =         self::_col."_is_promotion",
        _col_is_recover =           self::_col.'_is_recover',
        _col_is_valid =             self::_col."_is_valid",
        _col_key =                  self::_col."_key",
        _col_keywords =             self::_col."_keywords",
        _col_label =                self::_col."_label",
        _col_language =             self::_col."_language",
        _col_last_connection =      self::_col."_last_connection",
        _col_last_modified =        self::_col."_last_modified",
        _col_last_name =            self::_col."_last_name",
        _col_limit_time =           self::_col."_limit_time",
        _col_link =                 self::_col."_link",
        _col_link_text =            self::_col."_link_text",
        _col_max_qty =              self::_col."_max_qty",
        _col_media_preview =        self::_col."_media_preview",
        _col_merchant_id_code =     self::_col.'_merchant_id_code',
        _col_minimum_order =        self::_col."_minimum_order",
        _col_mobile =               self::_col."_mobile",
        _col_name =                 self::_col."_name",
        _col_option =               self::_col."_option",
        _col_option_total =         self::_col."_option_total",
        _col_order =                self::_col."_order",
        _col_order_id_code =        self::_col."_order_id_code",
        _col_page_id_code =         self::_col."_page_id_code",
        _col_password =             self::_col."_password",
        _col_payment =              self::_col.'_payment',
        _col_people =               self::_col."_people",
        _col_phone =                self::_col."_phone",
        _col_phone_prefix =         self::_col."_phone_prefix",
        _col_post_code =            self::_col."_post_code",
        _col_price =                self::_col."_price",
        _col_product_id_code =      self::_col."_product_id_code",
        _col_promotion_price =      self::_col."_promotion_price",
        _col_qty =                  self::_col."_qty",
        _col_route =                self::_col."_route",
        _col_route_variable =       self::_col."_route_variable",
        _col_start_time =           self::_col."_start_time",
        _col_status =               self::_col."_status",
        _col_system_path =          self::_col."_system_path",
        _col_tag =                  self::_col."_tag",
        _col_takeaway_payment =     self::_col.'_takeaway_payment',
        _col_tax =                  self::_col."_tax",
        _col_time_step =            self::_col."_time_step",
        _col_title =                self::_col."_title",
        _col_total =                self::_col."_total",
        _col_tree =                 self::_col."_tree",
        _col_type =                 self::_col."_type",
        _col_used =                 self::_col."_used",
        _col_value =                self::_col."_value",
        _col_value_string =         self::_col."_value_string",
        _col_web_path =             self::_col."_web_path"
    ;

    /**
     * @var $instance null|PDO database instance
     */
    private static $instance;

    public static function _getConnection() : ? PDO
    {
        $dbName =  Config::_getDbName();
        $userName = Config::_getUserName();
        $password = Config::_getDbPassword();

        if(self::$instance === null ||  !self::$instance instanceof PDO){
            try{
                self::$instance = new PDO("mysql:host=localhost;$dbName=test;charset=".self::DefaultCharset, $userName, $password);
            }catch (Exception $exception){
                die('Erreur : ' . $exception->getMessage());
            }
        }
        return self::$instance;
    }

    public static function _closeConnection()
    {
        return self::$instance = null;
    }




    protected static function _createUniqueId(
        string $tableName,
        string $column = self::_col_id_code,
        int $length = CodeGenerator::CODE_LENGTH,
        string $character = CodeGenerator::LETTER
    ): ? string
    {
        $sql = Sql::_inst($tableName);
        if($sql->tableExist()){
            $code = CodeGenerator::generateCode($length, $character);
            while (self::isIdUnique($tableName, $code, $column) === false){
                $code = CodeGenerator::generateCode($length, $character);
            }
            self::_closeConnection();
            return $code;
        }
        return null;
    }

    private static function isIdUnique(string $table, string $code, string $column= self::_col_id_code) : bool
    {
        $sql = Sql::_inst($table);
        $sql->equal($table,$column, $code);
        return $sql->count() ===0;
    }


    protected static function _getTableElementBy(string $tableName, string $columnName, $value){
        $result = [];
        if($tableName !== "" && $columnName !== "" && $value){
            $sql = Sql::_inst($tableName);
            $sql->equal($tableName, $columnName, $value);
            if($sql->select()){
                $result = $sql->first();
            }
        }
        return $result;
    }


    /**
     * Update a column field with time
     * @param string $tableName
     * @param array  $columnsValues
     * @param string $columnName
     *
     * @return bool
     */
    public static function _updateTime(string $tableName, array $columnsValues, string $columnName = self::_col_last_modified){
        if($tableName !== "" && $columnName !== "" && sizeof($columnsValues) > 0){
            $timeStamp = CustomDateTime::_getTimeStamp();
            $sql = Sql::_inst($tableName);
            foreach ($columnsValues as $columnConditionName => $value){
                if(is_string($columnConditionName) && $columnConditionName !== ""){
                    $sql->equal($tableName, $columnConditionName, $value);
                }
            }
            if( $sql->update([
                $columnName => $timeStamp
            ]) ){
//                echo json_encode($sql);
                return true;
            };
        }

        return false;


    }



    /**
     * increment a column
     * @param string $tableName the name of the table
     * @param array  $columnsValues an array of column name => column value to filter
     * @param string $columnName the column name for decrement
     *
     * @return bool
     */
    public static function _increment(string $tableName, array $columnsValues, string $columnName){
        if(
            String_Helper::_isStringNotEmpty($tableName) && String_Helper::_isStringNotEmpty($columnName) && sizeof($columnsValues) > 0
        ){
            $sql = Sql::_inst($tableName);
            foreach ($columnsValues as $columnConditionName => $value){
                if(is_string($columnConditionName) && $columnConditionName !== ""){
                    $sql->equal($tableName, $columnConditionName, $value);
                }
            }
            $success = $sql->increment( [$columnName]);
            return $success;
        }

        return false;
    }

    /**
     * decrement a column
     * @param string $tableName the name of the table
     * @param array  $columnsValues an array of column name => column value to filter
     * @param string $columnName the column name for decrement
     *
     * @return bool
     */
    public static function _decrement(string $tableName, array $columnsValues, string $columnName){
        if($tableName !== "" && $columnName !== "" && sizeof($columnsValues) > 0){
            $sql = Sql::_inst($tableName);
            foreach ($columnsValues as $columnConditionName => $value){
                if(is_string($columnConditionName) && $columnConditionName !== ""){
                    $sql->equal($tableName, $columnConditionName, $value);
                }
            }
            $success = $sql->decrement( [$columnName]);
            return $success;
        }

        return false;
    }


    //TODO
    protected static function _remove(string $tableName) :bool
    {


        return false;
    }


}