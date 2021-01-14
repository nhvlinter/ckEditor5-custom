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
use salesteck\security\MCrypt;
use salesteck\utils\CustomDateTime;
use salesteck\utils\String_Helper;
use stdClass;


/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-05-20
 * Time: 13:08
 */

class OrderTranslation extends Order implements JsonToClass, ArrayUnique
{



    public static function _instTranslation($row, string $language, $arrayOrderElement = null)
    {
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
            if( !is_array($arrayOrderElement) && String_Helper::_isStringNotEmpty($idCode)){
                $arrayOrderElement = OrderElement_C::_getOrderElementByOrderIdCode($idCode);
            }
            return new OrderTranslation(
                $idCode, $row[Order_C::_col_create_date], $row[Order_C::_col_type], $row[Order_C::_col_status],
                $row[Order_C::_col_client_name], $row[Order_C::_col_client_email], $row[Order_C::_col_client_phone],
                $row[Order_C::_col_comment], $row[Order_C::_col_start_time], $row[Order_C::_col_end_time],
                $arrayOrderElement, $row[Order_C::_col_post_code], $row[Order_C::_col_address],
                $row[Order_C::_col_delivery_fee], $language, $row[Order_C::_col_payment]
            );
        }

        return null;
    }
    private $language, $typeString, $statusString, $paymentString, $createDateString, $timeStartString, $timeEndString;

    /**
     * OrderTranslation constructor.
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
     * @param string $language
     * @param $payment
     * @internal param $typeString
     * @internal param $statusString
     * @internal param $createDateString
     * @internal param $timeStartString
     * @internal param $timeEndString
     */
    protected function __construct(
        string $idCode, string $createDate, int $type, int $status, string $clientName, string $clientEmail,
        string $clientPhone, string $comment, string $startTime, string $endTime, array $arrayOrderElement,
        string $deliveryZoneId, string $address, int $deliveryFee, string $language, $payment
    )
    {

        parent::__construct(
            $idCode, $createDate, $type, $status, $clientName, $clientEmail, $clientPhone,
            $comment, $startTime, $endTime, $arrayOrderElement, $deliveryZoneId, $address, $deliveryFee, $payment
        );

        $language = Language_C::_getValidLanguage($language);
        $this->typeString = self::_getTypeString($language, $type);
        $this->statusString = self::_getStatusString($language, $status);
        $this->paymentString = self::_getPaymentString($language, $payment);
        $this->createDateString = CustomDateTime::_timeStampToFormat($createDate, CustomDateTime::F_DATE_TIME_FULL);
        $this->timeStartString = CustomDateTime::_timeStampToFormat($startTime, CustomDateTime::F_DATE_TIME_NO_SECOND);
        $this->timeEndString = CustomDateTime::_timeStampToFormat($endTime, CustomDateTime::F_HOUR_MINUTE);
        $this->language = $language;
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
        $this->setTypeString(self::_getTypeString($this->getLanguage(), $type));
        return $this;
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
     * @return $this
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
        $this->setStatusString(self::_getStatusString($this->getLanguage(), $status));
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getTypeString(): string
    {
        return $this->typeString;
    }

    /**
     * @param string $typeString
     */
    public function setTypeString(string $typeString)
    {
        $this->typeString = $typeString;
    }

    /**
     * @return string
     */
    public function getStatusString(): string
    {
        return $this->statusString;
    }

    /**
     * @param string $statusString
     */
    public function setStatusString(string $statusString)
    {
        $this->statusString = $statusString;
    }

    /**
     * @return string
     */
    public function getPaymentString(): string
    {
        return $this->paymentString;
    }

    /**
     * @param string $paymentString
     */
    public function setPaymentString(string $paymentString)
    {
        $this->paymentString = $paymentString;
    }



    /**
     * @return string
     */
    public function getCreateDateString(): string
    {
        return $this->createDateString;
    }

    /**
     * @param string $createDateString
     */
    public function setCreateDateString(string $createDateString)
    {
        $this->createDateString = $createDateString;
    }

    /**
     * @return string
     */
    public function getTimeStartString(): string
    {
        return $this->timeStartString;
    }

    /**
     * @param string $timeStartString
     */
    public function setTimeStartString(string $timeStartString)
    {
        $this->timeStartString = $timeStartString;
    }

    /**
     * @return string
     */
    public function getTimeEndString(): string
    {
        return $this->timeEndString;
    }

    /**
     * @param string $timeEndString
     */
    public function setTimeEndString(string $timeEndString)
    {
        $this->timeEndString = $timeEndString;
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

    public function encryptUrl(int $newStatus = -1, string $language = ""){
        $language = $language !== "" ? $language : $this->getLanguage();
        $language = Language_C::_getValidLanguage($language);
        $object = new stdClass();
        $object->className = self::class;
        $object->idCode = $this->getIdCode();
        if($newStatus >= Order_C::STATUS_CREATED  && $newStatus <= Order_C::STATUS_DENIED){
            $object->newStatus = $newStatus;
        }
        $object->language = $language;
        $jsonString = json_encode($object);
        return MCrypt::url_encode($jsonString);
    }






    private static function _getTypeString(string $language, int $type) : string
    {
        $optionType = Order_C::_getOptionType($language);
        foreach ($optionType as $optionStr => $optionVal){
            if(intval($optionVal) === $type){
                return $optionStr;
            }
        }
        return "";
    }

    private static function _getStatusString(string $language, int $status) : string
    {
        $optionStatus = Order_C::_getOptionStatus($language);
        foreach ($optionStatus as $optionStr => $optionVal){
            if(intval($optionVal) === $status){
                return $optionStr;
            }
        }
        return "";
    }

    private static function _getPaymentString(string $language, string $payment) : string
    {
        if(String_Helper::_isStringNotEmpty($payment)){
            $optionPayment = Order_C::_getOptionPayment($language);
            foreach ($optionPayment as $optionStr => $optionVal){
                if(($optionVal) === $payment){
                    return $optionStr;
                }
            }
        }
        return "";
    }
}