<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 08-11-19
 * Time: 17:16
 */

namespace salesteck\custom;


use salesteck\_interface\DbControllerObject;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\utils\Debug;

class Country_C extends Db implements DbControllerObject
{
    public const
        TABLE = "_country"
    ;

    public static function _getAllCountries() : array
    {
        $arrayDebug = [];
        $arrayCountry = [];
        $sql = self::_getSql();
        $sql->orderAsc(self::TABLE, self::_col_name);
        if($sql->select()){
            $result = $sql->result();
            $arrayDebug["result"] = $result;
            foreach ($result as $row){
                $arrayDebug["row"] = $row;
                $country = self::_getObjectClassFromResultRow($row);
                if($country !== null && $country instanceof Country){
                    array_push($arrayCountry, $country);
                }
            }
        }
        $arrayDebug["arrayCountry"] = $arrayCountry;
        Debug::_exposeVariable($arrayDebug);
        return $arrayCountry;
    }

    public static function _getCountryBy(string $idCode = "", string $languageCode = "", string $name = "", string $phonePrefix = "")
    {
        $sql = self::_getSql();
        if($idCode !== ""){
            $sql->equal(self::TABLE, self::_col_id_code, $idCode);

        }
        if($languageCode !== ""){
            $sql->equal(self::TABLE, self::_col_code, $languageCode);

        }
        if($name !== ""){
            $sql->equal(self::TABLE, self::_col_name, $name);

        }
        if($phonePrefix !== ""){
            $sql->equal(self::TABLE, self::_col_phone_prefix, $phonePrefix);

        }
        if($sql->select()){
             return self::_getObjectClassFromResultRow($sql->first());
        }
        return null;
    }




    public static function _getCountryNameByCode(string $code){
        return self::_getCountryBy($code);
    }

    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getObjectClassFromResultRow($row) : ? Country
    {
        if(
            $row !== null && gettype($row) === gettype([]) &&
            array_key_exists(self::_col_id_code, $row) &&
            array_key_exists(self::_col_code, $row) &&
            array_key_exists(self::_col_name, $row) &&
            array_key_exists(self::_col_phone_prefix, $row)
        ){
            return new Country(
                $row[self::_col_id_code],
                $row[self::_col_code],
                $row[self::_col_name],
                $row[self::_col_phone_prefix]
            );
        }else{
            return null;
        }
    }
}