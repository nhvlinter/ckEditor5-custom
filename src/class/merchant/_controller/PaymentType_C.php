<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 05-11-20
 * Time: 00:17
 */

namespace salesteck\merchant;


use salesteck\_base\Language_C;
use salesteck\_interface\DbController;
use salesteck\Db\Db;
use salesteck\Db\Sql;

class PaymentType_C extends Db implements DbController
{

    public const TABLE = "_all_payment", TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION;


    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    public static function _getPaymentFromIdCode(string $idCode, string $language = "") : ? PaymentType
    {
        $language = Language_C::_getValidLanguage($language);
        if(is_string($idCode) && $idCode !== ""){
            $sql = self::_getSql();
            $sql->equal(self::TABLE, self::_col_id_code, $idCode)
                ->leftJoin(self::TABLE,self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code)
                ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
            ;
            if($sql->select()){
                $row = $sql->first();
                return PaymentType::_inst($row);
            }
        }
        return null;
    }

    public static function _getAllPaymentType (){
        $array = [];
        $sql = self::_getSql();
        $sql
            ->leftJoin(self::TABLE,self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code)
            ->equal(self::TABLE_TRANSLATION, self::_col_language, "fr")
        ;
        if($sql->select()){
            $result = $sql->result();
            foreach ($result as $row){
                $payment = PaymentType::_inst($row);
                if($payment !== null && $payment instanceof PaymentType){
                    array_push($array, $payment);
                }
            }

        }
        return $array;

    }

    public static function indexPayment(){
        $allPayment = self::_getAllPaymentType();
        $arrayInsert = [];
        $arrayLang = ["fr", "nl", "en"];
        foreach ($allPayment as $payment){
            if($payment !== null && $payment instanceof PaymentType){
                foreach ($arrayLang as $lang){
                    array_push($arrayInsert, [
                        self::_col_id_code => $payment->getIdCode(),
                        self::_col_name =>$payment->getName(),
                        self::_col_language =>$lang
                    ]);
                }
            }
        }

        $sqlInsert = Sql::_inst(self::TABLE_TRANSLATION);
        $sqlInsert->bulkInsert($arrayInsert, self::_col_id);
    }



    public static function _getEditorOptionPayment(string $language){
        $options = [];
        $sql = self::_getSql();
        $sql
            ->innerJoin(self::TABLE,self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code)
            ->equal(self::TABLE_TRANSLATION, self::_col_language, $language)
            ->equal(self::TABLE, self::_col_is_enable, intval(true))
        ;
        if($sql->select()){
            $arrayResult = $sql->result();
            foreach ($arrayResult as $row){
                if(
                    array_key_exists(self::_col_id_code, $row) &&
                    array_key_exists(self::_col_name, $row)
                ){
                    $options[$row[self::_col_name]] = $row[self::_col_id_code];
                }
            }
        }
        return $options;
    }

    public static function _getUniqueId(){

        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }
}