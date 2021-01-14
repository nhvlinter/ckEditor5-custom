<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 28-05-20
 * Time: 23:59
 */

namespace salesteck\order;


use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbController;
use salesteck\Db\Db;
use salesteck\Db\Sql;

class OrderPromotion_C extends Db implements DbController, DbCleaner
{
    public const TABLE = "_order_promotion";


    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    public static function _insert(
        string $orderIdCode, string $promotionCode, int $value, string $valueString
    ) : ? OrderPromotion
    {
        $sql = self::_getSql();
        $sql->idColumn(self::_col_id);
        $arrayInsert = [
            self::_col_order_id_code => $orderIdCode,
            self::_col_code => $promotionCode,
            self::_col_value => $value,
            self::_col_value_string => $valueString
        ];
        if($sql->insert($arrayInsert)){
            $row = $sql->first();
            return OrderPromotion::_inst($row);
        }
        return null;
    }


    public static function _getOrderPromotionFromIdCode(string $idCode) : array
    {
        $arrayPromotion = [];
        $sql = self::_getSql();
        $sql->equal(
            OrderPromotion_C::TABLE, OrderPromotion_C::_col_order_id_code, $idCode
        );
        if($sql->select()){
            $arrayResultRow = $sql->result();
            foreach ($arrayResultRow as $row){
                $orderPromotion =  OrderPromotion::_inst($row);
                if($orderPromotion !== null && $orderPromotion instanceof OrderPromotion){
                    array_push($arrayPromotion, $orderPromotion);
                }
            }
        }
        return $arrayPromotion;
    }


    static function _clean(bool $debug = false)
    {
        // TODO: Implement _clean() method.
    }
}