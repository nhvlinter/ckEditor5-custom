<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 12-11-19
 * Time: 03:24
 */

namespace salesteck\customer;



use salesteck\_interface\DbIdCode;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\security\MCrypt;

class Customer_C extends Db implements DbIdCode
{

    public const TABLE = "_customer";
    public const
        _col_validity_date = self::_col.'_validity_date',
        _col_city = self::_col.'_city'
    ;
    public const
        STATUS_CREATED = 0,
        STATUS_AUTHENTICATED = 1
    ;


    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    public static function _getAllCustomer($limit = null) : array
    {
        $array = [];
        $sql = self::_getSql();
        if($sql->select()){
            if($limit === null && intval($limit)>0){
                $result = $sql->first($limit);
            }else{
                $result = $sql->result();
            }
            foreach ($result as $row){
                $customer = Customer::_inst($row);
                if($customer !== null && $customer instanceof Customer){
                    array_push($array, $customer);
                }
            }
        }
        return $array;
    }

    static function _getUniqueId(): string
    {
        return self::_createUniqueId(self::TABLE, self::_col_customer_id_code, 10);
    }


    public static function _getUser(array $arrayCondition = []) : ? Customer
    {
        $sql = self::_getSql();
        foreach ($arrayCondition as $colKey => $value){
            $sql->equal(self::TABLE, $colKey, $value);
        }

        $sql->select();
        return Customer::_inst($sql->first());
    }




    public static function _getCustomerFromEncryptedUrl(){
        $customer = null;
        $encryptedObject = MCrypt::_getEncryptedObjectUrl();
        if (
            isset($encryptedObject->className) && isset($encryptedObject->idCode)
        ) {
            $className = $encryptedObject->className;
            $idCode = $encryptedObject->idCode;
            if ($className === Customer::class) {
                $customer = self::_getUser([
                    self::_col_customer_id_code => $idCode
                ]);
            }
        }
        return $customer;
    }
}