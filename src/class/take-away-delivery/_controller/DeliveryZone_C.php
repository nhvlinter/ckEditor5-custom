<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 28-04-20
 * Time: 13:47
 */

namespace salesteck\takeAwayDelivery;

use salesteck\_interface\DbControllerObject;
use salesteck\_interface\DbIdCode;
use salesteck\_interface\DbJoinController;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\merchant\Merchant_C;

class DeliveryZone_C extends Db implements DbIdCode, DbJoinController, DbControllerObject
{
    public const
        TABLE = "_delivery_zone",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;

    static function _getSql(): Sql
    {
        $sql = Sql::_inst(self::TABLE);
        return $sql->idColumn(self::_col_id);
    }

    static function _getSqlTranslation(): Sql
    {
        $sql = Sql::_inst(self::TABLE_TRANSLATION);
        return $sql->idColumn(self::_col_id);
    }

    static function _getJoinSql(): Sql
    {
        $sql = self::_getSqlTranslation();
        return $sql->innerJoin(self::TABLE_TRANSLATION,self::_col_id_code, self::TABLE, self::_col_id_code);
    }

    public static function _getZoneById($id){
        $sql = self::_getJoinSql();
        $sql
            ->equal(self::TABLE_TRANSLATION, self::_col_id, $id)
            ->select()
        ;
        return $sql->first();

    }

    public static function _getZoneByIdCode(string $idCode, string $language = ""){
        if($idCode !== ""){
            $sql = self::_getJoinSql();
            $sql->equal(self::TABLE, self::_col_id_code, $idCode);
            if($language !== ""){
                $sql->equal(self::TABLE_TRANSLATION, self::_col_language, $language);
            }
            if($sql->select()){
                return self::_getObjectClassFromResultRow($sql->first());
            }
        }
        return null;

    }

    public static function _getUniqueId(): string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }

    public static function _getEditorOptionZone($merchantIdCode,  $language){
        $options = [];
        $sql = self::_getJoinSql();
        $sql
            ->equal(self::TABLE_TRANSLATION,self::_col_language, $language)
            ->equal(self::TABLE,Merchant_C::_col_merchant_id_code, $merchantIdCode)
            ->orderAsc(self::TABLE_TRANSLATION, self::_col_name)
        ;
        if($sql->select()){
            $arrayResult = $sql->result();
            foreach ($arrayResult as $row){
                if(
                    array_key_exists(self::_col_name, $row) &&
                    array_key_exists(self::_col_id_code, $row)
                ){
                    $options[$row[self::_col_name]] = $row[self::_col_id_code];
                }
            }
        }
//        echo json_encode($sql);
        return $options;
    }

    static function _getObjectClassFromResultRow($row) : ? DeliveryZone
    {
        $obj = null;
        if(
            array_key_exists(self::_col_id_code, $row) &&
            array_key_exists(self::_col_name, $row) &&
            array_key_exists(self::_col_post_code, $row) &&
            array_key_exists(self::_col_delivery_fee, $row) &&
            array_key_exists(self::_col_minimum_order, $row)
        ){
            $obj = new DeliveryZone(
                $row[self::_col_id_code], $row[self::_col_name], $row[self::_col_post_code],
                $row[self::_col_delivery_fee], $row[self::_col_minimum_order]
            );
        }
        return $obj;
    }
}