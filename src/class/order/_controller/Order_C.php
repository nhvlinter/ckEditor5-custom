<?php
namespace salesteck\order;

use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbController;
use salesteck\_interface\DbIdCode;
use salesteck\admin\AdminI18;
use salesteck\admin\AdminI18_C;
use salesteck\_base\Language_C;
use salesteck\api\RequestResponse;
use salesteck\customer\Customer;
use salesteck\customer\Customer_C;
use salesteck\Db\CodeGenerator;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\Db\SqlOrder;
use salesteck\merchant\Merchant;
use salesteck\merchant\Merchant_C;
use salesteck\merchant\PaymentType_C;
use salesteck\security\MCrypt;
use salesteck\utils\CustomDateTime;
use salesteck\utils\Json;


/**
 * Created by PhpStorm.
 * User: Son
 * Date: 08-05-20
 * Time: 23:23
 */

class Order_C extends Db implements DbController, DbIdCode, DbCleaner
{

    public const TABLE = "_order", DEF_LIMIT_QUERY = 20;

    public const
        _col_client_name = self::_col.'_client_name',
        _col_client_email = self::_col.'_client_email',
        _col_client_phone = self::_col.'_client_phone'
    ;

    public const
        TYPE_TAKE_AWAY = 0,
        TYPE_DELIVERY = 1
    ;

    public const
        STATUS_CREATED = 0,
        STATUS_SEND = 1,
        STATUS_CONFIRMED = 2,
        STATUS_DENIED = 3,
        STATUS_EXPIRED = 4
    ;

    public static function _isStatusValid($status){
        if(is_numeric($status)){
            $status = intval($status);
            return
                $status === self::STATUS_CREATED ||
                $status === self::STATUS_SEND ||
                $status === self::STATUS_CONFIRMED ||
                $status === self::STATUS_DENIED
                ;
        }
        return false;
    }

    private static $optionType, $optionStatus, $optionPayment;

    public static function _getOptionType(string $language){
        if( !is_array(self::$optionType) ){
            $language = Language_C::_getValidLanguage($language);
            self::$optionType = self::_getEditorOptionType($language);
        }
        return self::$optionType;
    }

    public static function _getOptionStatus(string $language){
        if( !is_array(self::$optionStatus) ){
            $language = Language_C::_getValidLanguage($language);
            self::$optionStatus = self::_getEditorOptionStatus($language);
        }
        return self::$optionStatus;
    }

    public static function _getOptionPayment(string $language){
        if( !is_array(self::$optionPayment) ){
            $language = Language_C::_getValidLanguage($language);
            self::$optionPayment = PaymentType_C::_getEditorOptionPayment($language);
        }
        return self::$optionPayment;
    }

    public static function _isTypeValid($type){
        if(is_numeric($type)){
            $type = intval($type);
            return $type === self::TYPE_TAKE_AWAY || $type === self::TYPE_DELIVERY;
        }
        return false;
    }


    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getUniqueId(): string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code, 8, CodeGenerator::CHARACTER);
    }

    public static function _insert(
        array $columnsValue, Customer $customer, Merchant $merchant,
        array $arrayCartElement, $promotion, string $language
    ) : ? Order
    {
        $sql = self::_getSql();
        if(sizeof($arrayCartElement)){

            $columnsValue[self::_col_status] = self::STATUS_CREATED;

            $orderIdCode = Order_C::_getUniqueId();
            $columnsValue[self::_col_id_code] = $orderIdCode;

            $merchantIdCode = $merchant->getIdCode();
            $customerIdCode = $customer->getIdCode();

            $createDate = CustomDateTime::_getTimeStamp();
            $columnsValue[self::_col_create_date] = $createDate;

            $columnsValue[Merchant_C::_col_merchant_id_code] = $merchantIdCode;
            $columnsValue[Customer_C::_col_customer_id_code] = $customerIdCode;

            $isOrderInserted = $sql->insert($columnsValue);
            if($isOrderInserted){
                $arrayOrderElementInserted = [];
                foreach ($arrayCartElement as  $cartElement){
                    $cartElement = (Array)  json_decode(json_encode($cartElement), true);
                    $cartElementToClass = CartElement::_arrayInst($cartElement);
                    if ($cartElementToClass instanceof CartElement){
                        $insertedOrderElement = OrderElement_C::_insert2(
                            $merchantIdCode, $customerIdCode, $orderIdCode, $cartElementToClass, $language
                        );
                        if( $insertedOrderElement !== null && $insertedOrderElement instanceof OrderElement ){
                            array_push($arrayOrderElementInserted, $insertedOrderElement);
                        }
                    }else{
                        throw new OrderException("Invalid conversion CartElement : " . json_encode($cartElement) );
                    }
                }
                if(sizeof($arrayOrderElementInserted) === sizeof($arrayCartElement)){
                    if($promotion !== null && $promotion instanceof CartPromotion){
                        OrderPromotion_C::_insert(
                            $orderIdCode, $promotion->getCode(), $promotion->getValue(), $promotion->getValueString()
                        );
                    }
                    return self::_getOrderByIdCode($orderIdCode, $language);
                }else{
                    self::_removeOrder($orderIdCode);
                    throw new OrderException(json_encode($arrayOrderElementInserted));
                }
            }else{
                throw  new OrderException($sql->error());
            }
        }
        else{
            throw  new OrderException("No elements in cart");
        }
    }

    public static function _insertOrder(){

    }



    public static function _update(string $idCode, array $arrayColumnValue){
        $isUpdated = false;
        if($idCode !== "") {
            $sql = self::_getSql();
            $sql->equal(self::TABLE, self::_col_id_code, $idCode);
            $isUpdated = $sql->update($arrayColumnValue);
        }
        return $isUpdated;
    }

    public static function _removeOrder(string $idCode) : bool
    {
        $isRemove = false;
        if($idCode !== ""){
            $sql = self::_getSql();
            $sql->equal(self::TABLE, self::_col_id_code, $idCode);
            if($sql->delete()){
                $sqlOrderElement = OrderElement_C::_getSql();
                $sqlOrderElement->equal(
                    OrderElement_C::TABLE, OrderElement_C::_col_order_id_code, $idCode
                );
                $isRemove = $sqlOrderElement->delete();
            }
        }
        return $isRemove;
    }

    public static function _getOrderByIdCode(string $idCode, string $language) : ? OrderTranslation
    {
        $orders = self::_getOrders($language, [self::_col_id_code => $idCode], null, 1);
        if(sizeof($orders) === 1){
            return $orders[0];
        }
        return null;
    }


    public static function _getOrders(
        string $language, array $columnsValues, $sqlOrder = null, int $limit = self::DEF_LIMIT_QUERY, &$response = null
    ) : array
    {
        $language = Language_C::_getValidLanguage($language);
        $orders = [];
        $sql = self::_getSql();

        foreach ($columnsValues as $colName => $colValue){
            $sql->equal(self::TABLE, $colName, $colValue);
        }
        if($limit > 0){
            $sql->limit($limit);
        }
        if($sqlOrder instanceof SqlOrder){
            $sql->addOrder($sqlOrder);
        }
        if($sql->select()){
            $result = $sql->result();
            if(is_array($result)){
                foreach ($result as $row){
                    $order = OrderTranslation::_instTranslation($row, $language);
                    if($order instanceof OrderTranslation){
                        array_push($orders, $order);
                    }
                }
            }
        }
        if($response instanceof RequestResponse){
            $response->_file()->debug("sql", $sql);
        }
        return $orders;

    }

    public static function _getMerchantOrders (
        string $merchantIdCode, string $language, $sqlOrder = null, int $limit = self::DEF_LIMIT_QUERY
    ) : array
    {
        return self::_getOrders($language, [Merchant_C::_col_merchant_id_code => $merchantIdCode], $sqlOrder, $limit);
    }

    public static function _getOrderFromEncryptedUrl(){
        $order = null;
        $encryptedObject = MCrypt::_getEncryptedObjectUrl();
        if (
            isset($encryptedObject->className) && isset($encryptedObject->idCode) && isset($encryptedObject->language)
        ) {
            $className = $encryptedObject->className;
            $idCode = $encryptedObject->idCode;
            $language = $encryptedObject->language;
            if ($className === OrderTranslation::class) {
                $order = self::_getOrderByIdCode($idCode, $language);
            }
        }
        return $order;
    }

    public static function _getEditorOptionType(string $language){
        $i18n = AdminI18_C::_getInstance($language);
        $takeAway = AdminI18_C::_getValueFromKey(AdminI18::TYPE_TAKE_AWAY, $i18n);
        $delivery = AdminI18_C::_getValueFromKey(AdminI18::TYPE_DELIVERY, $i18n);
        $arrayOption = [
            $takeAway => strval(self::TYPE_TAKE_AWAY),
            $delivery => strval(self::TYPE_DELIVERY)
        ];
        return $arrayOption;
    }

    public static function _getEditorOptionStatus(string $language){
        $i18n = AdminI18_C::_getInstance($language);
        $created = AdminI18_C::_getValueFromKey(AdminI18::ORDER_STATUS_CREATED, $i18n);
        $send = AdminI18_C::_getValueFromKey(AdminI18::ORDER_STATUS_SEND, $i18n);
        $confirmed = AdminI18_C::_getValueFromKey(AdminI18::ORDER_STATUS_CONFIRMED, $i18n);
        $denied = AdminI18_C::_getValueFromKey(AdminI18::ORDER_STATUS_DENIED, $i18n);
        $expired = AdminI18_C::_getValueFromKey(AdminI18::ORDER_STATUS_EXPIRED, $i18n);
        $arrayOption = [
            $created => strval(self::STATUS_CREATED),
            $send => strval(self::STATUS_SEND),
            $confirmed => strval(self::STATUS_CONFIRMED),
            $denied => strval(self::STATUS_DENIED),
            $expired => strval(self::STATUS_EXPIRED)
        ];
        return $arrayOption;
    }





    static function _clean(bool $debug = false)
    {
        // TODO: Implement _clean() method.
    }
}