<?php
namespace salesteck\order;

use salesteck\_interface\DbCleaner;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\merchant\Merchant_C;

/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-05-20
 * Time: 14:07
 */

class OrderElement_C extends Db implements DbCleaner
{
    public const TABLE = "_order_element";

    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }


    public static function _create($columnsValues){
        if(is_array($columnsValues) && sizeof($columnsValues) > 0){

            $sql = self::_getSql();
            $sql
                ->idColumn(self::_col_id)
                ->column([
                    OrderElement_C::_col_id,
                    OrderElement_C::_col_order_id_code,
                    Merchant_C::_col_merchant_id_code,
                    OrderElement_C::_col_product_id_code,
                    OrderElement_C::_col_language,
                    OrderElement_C::_col_name,
                    OrderElement_C::_col_price,
                    OrderElement_C::_col_qty
                ])
            ;
            if($sql->insert($columnsValues)){
                $row = $sql->first();
                return OrderElement::_inst($row);
            }

        }
        return null;
    }

    public static function _insert(
        string $merchantIdCode, string $customerIdCode, string $orderIdCode,
        string $productIdCode, string $name, int $price, int $qty, string $language
    ) : ? OrderElement
    {
        $sql = self::_getSql();
        $sql->idColumn(self::_col_id);
        $arrayInsert = [
            self::_col_merchant_id_code => $merchantIdCode,
            self::_col_customer_id_code => $customerIdCode,
            self::_col_order_id_code => $orderIdCode,
            self::_col_product_id_code => $productIdCode,
            self::_col_name => $name,
            self::_col_price => $price,
            self::_col_qty => $qty,
            self::_col_language => $language
        ];
        if($sql->insert($arrayInsert)){
            $row = $sql->first();
            return OrderElement::_inst($row);

        }
        return null;
    }

    public static function _insert2(
        string $merchantIdCode, string $customerIdCode, string $orderIdCode,
        CartElement $cartElement, string $language
    ) : ? OrderElement
    {
        if($cartElement instanceof CartElement){

            $sql = self::_getSql();
            $sql->idColumn(self::_col_id);
            $arrayInsert = [
                self::_col_merchant_id_code => $merchantIdCode,
                self::_col_customer_id_code => $customerIdCode,
                self::_col_order_id_code => $orderIdCode,
                self::_col_product_id_code => $cartElement->getIdCode(),
                self::_col_name => $cartElement->getName(),
                self::_col_price => $cartElement->getPrice(),
                self::_col_qty => $cartElement->getQty(),
                self::_col_option_total => $cartElement->getExtraOptionsTotal(),
                self::_col_option => $cartElement->extraOptionToString(),
                self::_col_language => $language
            ];
            if($sql->insert($arrayInsert)){
                $row = $sql->first();
                return OrderElement::_inst($row);

            }
        }
        return null;
    }

    public static function _getElementById(string $id){

    }

    public static function _getOrderElementByOrderIdCode(string $orderIdCode){
        $arrayOrderElement = [];
        $sql = self::_getSql();
        $sql->equal(self::TABLE, self::_col_order_id_code, $orderIdCode);
        if($sql->select()){
            $resultRow = $sql->result();
            foreach ($resultRow as $row){
                $orderElement = OrderElement::_inst($row);
                if($orderElement !== null && $orderElement instanceof OrderElement){
                    array_push($arrayOrderElement, $orderElement);
                }
            }
        }
        return $arrayOrderElement;
    }


    static function _clean(bool $debug = false)
    {
        // TODO: Implement _clean() method.
    }
}