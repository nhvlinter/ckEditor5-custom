<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-05-20
 * Time: 12:29
 */
namespace salesteck\order;


use salesteck\_interface\ArrayUnique;
use salesteck\_interface\JsonToClass;
use salesteck\_base\Language_C;
use salesteck\merchant\PaymentType;
use salesteck\merchant\PaymentType_C;
use salesteck\security\MCrypt;
use salesteck\utils\Converter;
use salesteck\utils\String_Helper;
use stdClass;

class Order implements JsonToClass, ArrayUnique
{

    public static function _inst($row){
        if(
            is_array($row) &&
            array_key_exists(Order_C::_col_id_code, $row) &&
            array_key_exists(Order_C::_col_create_date, $row) &&
            array_key_exists(Order_C::_col_type, $row) &&
            array_key_exists(Order_C::_col_status, $row) &&
            array_key_exists(Order_C::_col_client_name, $row) &&
            array_key_exists(Order_C::_col_client_email, $row) &&
            array_key_exists(Order_C::_col_client_phone, $row) &&
            array_key_exists(Order_C::_col_comment, $row) &&
            array_key_exists(Order_C::_col_start_time, $row) &&
            array_key_exists(Order_C::_col_end_time, $row) &&
            array_key_exists(Order_C::_col_post_code, $row) &&
            array_key_exists(Order_C::_col_address, $row) &&
            array_key_exists(Order_C::_col_delivery_fee, $row) &&
            array_key_exists(Order_C::_col_payment, $row)
        ){
            $idCode = $row[Order_C::_col_id_code];
            if(String_Helper::_isStringNotEmpty($idCode)){
                $arrayOrderElement = OrderElement_C::_getOrderElementByOrderIdCode($idCode);
                if(sizeof($arrayOrderElement) > 0){

                    return new self(
                        $idCode, $row[Order_C::_col_create_date], $row[Order_C::_col_type], $row[Order_C::_col_status],
                        $row[Order_C::_col_client_name], $row[Order_C::_col_client_email], $row[Order_C::_col_client_phone],
                        $row[Order_C::_col_comment], $row[Order_C::_col_start_time], $row[Order_C::_col_end_time],
                        $arrayOrderElement, $row[Order_C::_col_post_code], $row[Order_C::_col_address],
                        $row[Order_C::_col_delivery_fee], $row[Order_C::_col_payment]
                    );
                }
            }
        }

        return null;
    }

    protected
        $idCode, $customerIdCode, $merchantIdCode, $createDate, $type, $status, $clientName,
        $clientEmail, $clientPhone, $comment, $startTime, $endTime,
        $arrayOrderElement, $deliveryZoneId, $address, $deliveryFee, $promotions, $payment
    ;



    /**
     * Order constructor.
     * @param string $idCode
     * @param string $createDate
     * @param int $type
     * @param int $status
     * @param string $clientName
     * @param string $clientEmail
     * @param string $clientPhone
     * @param string $comment
     * @param string $startTime
     * @param string $endTime
     * @param array $arrayOrderElement
     * @param string $deliveryZoneId
     * @param string $address
     * @param int $deliveryFee
     * @param string $payment
     */
    protected function __construct(
        string $idCode, string $createDate, int $type, int $status, string $clientName, string $clientEmail, string $clientPhone, string $comment,
        string $startTime, string $endTime, array $arrayOrderElement, string $deliveryZoneId, string $address, int $deliveryFee, string $payment
    )
    {
        $this->idCode = $idCode;
        $this->type = $type;
        $this->status = $status;
        $this->createDate = $createDate;
        $this->clientName = $clientName;
        $this->clientEmail = $clientEmail;
        $this->clientPhone = $clientPhone;
        $this->comment = $comment;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->deliveryZoneId = $deliveryZoneId;
        $this->address = $address;
        $this->deliveryFee = $deliveryFee;
        $this->arrayOrderElement = $arrayOrderElement;
        $this->payment = $payment;
        $this->promotions = [];
        if($idCode !== ""){
            $arrayPromotion = OrderPromotion_C::_getOrderPromotionFromIdCode($idCode);
            $this->setPromotions($arrayPromotion);
        }
    }

    /**
     * @return string
     */
    public function getIdCode(): string
    {
        return $this->idCode;
    }

    /**
     * @param string $idCode
     */
    public function setIdCode(string $idCode)
    {
        $this->idCode = $idCode;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return $this
     */
    public function setType(int $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreateDate(): string
    {
        return $this->createDate;
    }

    /**
     * @param string $createDate
     */
    public function setCreateDate(string $createDate)
    {
        $this->createDate = $createDate;
    }

    /**
     * @return string
     */
    public function getClientName(): string
    {
        return $this->clientName;
    }

    /**
     * @param string $clientName
     */
    public function setClientName(string $clientName)
    {
        $this->clientName = $clientName;
    }

    /**
     * @return string
     */
    public function getClientEmail(): string
    {
        return $this->clientEmail;
    }

    /**
     * @param string $clientEmail
     */
    public function setClientEmail(string $clientEmail)
    {
        $this->clientEmail = $clientEmail;
    }

    /**
     * @return string
     */
    public function getClientPhone(): string
    {
        return $this->clientPhone;
    }

    /**
     * @param string $clientPhone
     */
    public function setClientPhone(string $clientPhone)
    {
        $this->clientPhone = $clientPhone;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getStartTime(): string
    {
        return $this->startTime;
    }

    /**
     * @param string $startTime
     */
    public function setStartTime(string $startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return string
     */
    public function getEndTime(): string
    {
        return $this->endTime;
    }

    /**
     * @param string $endTime
     */
    public function setEndTime(string $endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getArrayOrderElement(): array
    {
        return $this->arrayOrderElement;
    }

    /**
     * @param array $arrayOrderElement
     */
    public function setArrayOrderElement(array $arrayOrderElement)
    {
        $this->arrayOrderElement = $arrayOrderElement;
    }



    /**
     * @return string
     */
    public function getDeliveryZoneId(): string
    {
        return $this->deliveryZoneId;
    }

    /**
     * @param string $deliveryZoneId
     */
    public function setDeliveryZoneId(string $deliveryZoneId)
    {
        $this->deliveryZoneId = $deliveryZoneId;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    /**
     * @return int
     */
    public function getDeliveryFee(): int
    {
        return $this->deliveryFee;
    }

    /**
     * @param int $deliveryFee
     */
    public function setDeliveryFee(int $deliveryFee)
    {
        $this->deliveryFee = $deliveryFee;
    }

    /**
     * @return array
     */
    public function getPromotions(): array
    {
        return $this->promotions;
    }

    /**
     * @param array $promotions
     */
    public function setPromotions(array $promotions)
    {
        $arrayPromotion = [];
        foreach ($promotions as $promotion){
            if($promotion !== null && $promotion instanceof OrderPromotion){
                array_push($arrayPromotion, $promotion);
            }
        }

        $this->promotions = $arrayPromotion;
    }

    /**
     * @return mixed
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param mixed $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }







    public function getPaymentName(){
        if(is_string($this->payment) && $this->payment !== ""){
            $payment = PaymentType_C::_getPaymentFromIdCode($this->payment);
            if($payment instanceof PaymentType){
                return $payment->getName();
            }
        }
        return "";
    }





    public function getTotalPromotion () : int
    {
        $promotionTotal = 0;
        $arrayPromotion = $this->getPromotions();

        foreach ($arrayPromotion as $promotion){
            if($promotion !== null && $promotion instanceof OrderPromotion){
                $promotionTotal += $promotion->getValue();
            }
        }
        return $promotionTotal;
    }

    public function getTotalPromotionString() : string
    {
        return Converter::_intToPrice($this->getTotalPromotion());
    }

    public function getTotalProduct() : int
    {
        $arrayOrderElement = $this->getArrayOrderElement();
        $total = 0;
        if(sizeof($arrayOrderElement) > 0){
            foreach ($arrayOrderElement as $orderElement){
                if($orderElement !== null && $orderElement instanceof OrderElement){
                    $total += ($orderElement->getQty() * ($orderElement->getPrice() + $orderElement->getOptionsTotal()));
                }
            }
        }
        return $total;
    }

    public function getTotalProductString() : string
    {
        return Converter::_intToPrice($this->getTotalProduct());
    }

    public function getOrderTotal(): int
    {
        $total = $this->getTotalProduct();
        $total += $this->getDeliveryFee();
        $total -= $this->getTotalPromotion();
        return $total;
    }

    public function getOrderTotalString(){
        return Converter::_intToPrice($this->getOrderTotal());
    }
    public function getDeliveryFeeString(){
        return Converter::_intToPrice($this->getDeliveryFee());
    }




    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $obj = get_object_vars($this);
        $obj['orderTotal'] = $this->getOrderTotal();
        return $obj;
    }

    public function jsonToClass(array $json)
    {
        foreach($json as $key => $value){
            $this->{$key} = $value;
        }
    }

    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }


    public static function _encryptUrl(string $idCode, string $language = "", int $status = -1){
        $language = Language_C::_getValidLanguage($language);
        $object = new stdClass();
        $object->className = OrderTranslation::class;
        $object->idCode = $idCode;
        if($status >= Order_C::STATUS_CREATED  && $status <= Order_C::STATUS_DENIED){
            $object->newStatus = $status;
        }
        $object->language = $language;
        $jsonString = json_encode($object);
        return MCrypt::url_encode($jsonString);
    }
}