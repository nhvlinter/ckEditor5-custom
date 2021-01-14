<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 28-05-20
 * Time: 16:01
 */
namespace salesteck\promotion;

use salesteck\admin\AdminI18;
use salesteck\admin\AdminI18_C;
use salesteck\_base\Language_C;
use salesteck\Db\Db;
use salesteck\order\CartPromotion;
use salesteck\utils\Converter;
use salesteck\utils\CustomDateTime;

/**
 * Class Promotion
 * @package salesteck\promotion
 */
class Promotion implements \JsonSerializable
{

    /**
     * @param string $promoCode
     * @return null|Promotion
     */
    public static function _fromPromoCode( $promoCode) : ? self
    {
        if(is_string($promoCode) && $promoCode !== "" ){
            $sql = Promotion_C::_getSql();
            $sql
                ->equal(Promotion_C::TABLE, Promotion_C::_col_code, $promoCode)
                //TODO
                ->equal(Promotion_C::TABLE, Promotion_C::_col_is_enable, intval(true))
            ;

            if($sql->select()){
                $row = $sql->first();
                if(
                    array_key_exists(Promotion_C::_col_id_code, $row) &&
                    array_key_exists(Promotion_C::_col_code, $row) &&
                    array_key_exists(Promotion_C::_col_start_time, $row) &&
                    array_key_exists(Promotion_C::_col_end_time, $row) &&
                    array_key_exists(Promotion_C::_col_type, $row) &&
                    array_key_exists(Promotion_C::_col_minimum_order, $row) &&
                    array_key_exists(Promotion_C::_col_is_enable, $row) &&
                    array_key_exists(Promotion_C::_col_days, $row) &&
                    array_key_exists(Promotion_C::_col_value, $row) &&
                    array_key_exists(Promotion_C::_col_used, $row) &&
                    array_key_exists(Promotion_C::_col_qty, $row) &&
                    array_key_exists(Promotion_C::_col_sales_type, $row)
                ){
                    return new self(
                        $row[Promotion_C::_col_id_code],
                        $row[Promotion_C::_col_code],
                        $row[Promotion_C::_col_start_time],
                        $row[Promotion_C::_col_end_time],
                        $row[Promotion_C::_col_type],
                        $row[Promotion_C::_col_minimum_order],
                        $row[Promotion_C::_col_is_enable],
                        $row[Promotion_C::_col_days],
                        $row[Promotion_C::_col_value],
                        $row[Promotion_C::_col_used],
                        $row[Promotion_C::_col_qty],
                        $row[Promotion_C::_col_sales_type]
                    );

                }
            }

        }

        return null;

    }

    static function _inst($row) : ? self
    {
        if(
            is_array($row) &&
            array_key_exists(Promotion_C::_col_id_code, $row) &&
            array_key_exists(Promotion_C::_col_code, $row) &&
            array_key_exists(Promotion_C::_col_start_time, $row) &&
            array_key_exists(Promotion_C::_col_end_time, $row) &&
            array_key_exists(Promotion_C::_col_type, $row) &&
            array_key_exists(Promotion_C::_col_minimum_order, $row) &&
            array_key_exists(Promotion_C::_col_is_enable, $row) &&
            array_key_exists(Promotion_C::_col_days, $row) &&
            array_key_exists(Promotion_C::_col_value, $row) &&
            array_key_exists(Promotion_C::_col_used, $row) &&
            array_key_exists(Promotion_C::_col_qty, $row) &&
            array_key_exists(Promotion_C::_col_sales_type, $row)
        ){
            return new self(
                $row[Promotion_C::_col_id_code],
                $row[Promotion_C::_col_code],
                $row[Promotion_C::_col_start_time],
                $row[Promotion_C::_col_end_time],
                $row[Promotion_C::_col_type],
                $row[Promotion_C::_col_minimum_order],
                $row[Promotion_C::_col_is_enable],
                $row[Promotion_C::_col_days],
                $row[Promotion_C::_col_value],
                $row[Promotion_C::_col_used],
                $row[Promotion_C::_col_qty],
                $row[Promotion_C::_col_sales_type]
            );

        }
        return null;
    }


    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var int|string
     */
    /**
     * @var int|string
     */
    /**
     * @var int|string
     */
    /**
     * @var int|string
     */
    /**
     * @var bool|int|string
     */
    /**
     * @var array|bool|int|string
     */
    /**
     * @var array|bool|int|string
     */
    /**
     * @var array|bool|int|string
     */
    /**
     * @var array|bool|int|string
     */
    /**
     * @var array|bool|int|string
     */
    protected
        $idCode,
        $promoCode,
        $startTime,
        $endTime,
        $type,
        $minOrder,
        $isEnable,
        $daysValidity,
        $value,
        $usedQty,
        $qty,
        $salesType
    ;

    /**
     * Promotion constructor.
     * @param string $idCode
     * @param string $promoCode
     * @param int $startTime
     * @param int $endTime
     * @param int $type
     * @param int $minOrder
     * @param bool $isEnable
     * @param string $daysValidity
     * @param int $value
     * @param int $usedQty
     * @param int $qty
     * @param string $salesType
     */
    public function __construct(
        string $idCode, string $promoCode, int $startTime, int $endTime, int $type, int $minOrder,
        bool $isEnable, string $daysValidity, int $value, int $usedQty, int $qty, string $salesType
    )
    {
        $this->idCode = $idCode;
        $this->promoCode = $promoCode;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->type = $type;
        $this->minOrder = $minOrder;
        $this->isEnable = $isEnable;
        $this->daysValidity = explode(Db::ARRAY_DELIMITER, $daysValidity);
        $this->value = $value;
        $this->usedQty = $usedQty;
        $this->qty = $qty;
        $this->salesType = $salesType;
    }

    /**
     * @return string
     */
    public function getIdCode(): string
    {
        return $this->idCode;
    }

    /**
     * @return string
     */
    public function getPromoCode(): string
    {
        return $this->promoCode;
    }

    /**
     * @return int
     */
    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function getStartTimeString(string $format = CustomDateTime::F_DATE_TIME_NO_SECOND) {
        return CustomDateTime::_timeStampToFormat($this->getStartTime(), $format);
    }

    /**
     * @return int
     */
    public function getEndTime(): int
    {
        return $this->endTime;
    }

    public function getEndTimeString(string $format = CustomDateTime::F_DATE_TIME_NO_SECOND) {
        return CustomDateTime::_timeStampToFormat($this->getEndTime(), $format);
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getMinOrder(): int
    {
        return $this->minOrder;
    }

    /**
     * @return string
     */
    public function getMinOrderString() : string
    {
        return Converter::_intToPrice($this->getMinOrder());
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->isEnable;
    }

    /**
     * @return array
     */
    public function getDaysValidity(): array
    {
        return $this->daysValidity;
    }

    public function getDaysValidityString(string $language = "") : string
    {
        $language = Language_C::_getValidLanguage($language);
        $i18n = AdminI18_C::_getInstance($language);
        $arrayDaysString = [];

        foreach ($this->getDaysValidity() as $day){
            $daysString = "";
            switch (intval($day)){
                case CustomDateTime::MONDAY :
                    $daysString = AdminI18_C::_getValueFromKey(AdminI18::DAY_MONDAY, $i18n);
                    break;
                case CustomDateTime::TUESDAY :
                    $daysString = AdminI18_C::_getValueFromKey(AdminI18::DAY_TUESDAY, $i18n);
                    break;
                case CustomDateTime::WEDNESDAY :
                    $daysString = AdminI18_C::_getValueFromKey(AdminI18::DAY_WEDNESDAY, $i18n);
                    break;
                case CustomDateTime::THURSDAY :
                    $daysString = AdminI18_C::_getValueFromKey(AdminI18::DAY_THURSDAY, $i18n);
                    break;
                case CustomDateTime::FRIDAY :
                    $daysString = AdminI18_C::_getValueFromKey(AdminI18::DAY_FRIDAY, $i18n);
                    break;
                case CustomDateTime::SATURDAY :
                    $daysString = AdminI18_C::_getValueFromKey(AdminI18::DAY_SATURDAY, $i18n);
                    break;
                case CustomDateTime::SUNDAY :
                    $daysString = AdminI18_C::_getValueFromKey(AdminI18::DAY_SUNDAY, $i18n);
                    break;
            }
            if($daysString !== ""){
                array_push($arrayDaysString, $daysString);
            }
        }
        return implode(", ", $arrayDaysString);
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getUsedQty(): int
    {
        return $this->usedQty;
    }

    /**
     * @return int
     */
    public function getQty(): int
    {
        return $this->qty;
    }

    /**
     * @return string
     */
    public function getSalesType(): string
    {
        return $this->salesType;
    }

    public function getSalesTypeString(string $language) : string
    {
        $i18n = AdminI18_C::_getInstance($language);
        switch ($this->getSalesType()){
            case Promotion_C::sales_type_take_away :
                return AdminI18_C::_getValueFromKey(AdminI18::TYPE_TAKE_AWAY, $i18n);
            case Promotion_C::sales_type_delivery :
                return AdminI18_C::_getValueFromKey(AdminI18::TYPE_DELIVERY, $i18n);
            default :
                return "";
        }
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
        return get_object_vars($this);
    }


    /**
     * @param int $timeStamp
     * @return bool
     */
    public function _checkDateValidity(int $timeStamp) : bool
    {
        $promotionStartTime = $this->getStartTime();
        $promotionEndTime = $this->getEndTime();
        return $promotionStartTime < $timeStamp && $timeStamp < $promotionEndTime;
    }

    /**
     * @param int $timeStamp
     * @return bool
     */
    public function _checkDaysValidity(int $timeStamp) : bool
    {
        if($timeStamp > 0){
            $processDay = CustomDateTime::_timeStampToFormat($timeStamp, CustomDateTime::F_DAY_OF_WEEK_MON_SUN);
            $arrayValidDays = $this->getDaysValidity();
            return in_array(strval($processDay), $arrayValidDays);
        }
        return false;
    }

    /**
     * @param int $orderTotal
     * @return bool
     */
    public function _checkMinOrderValidity(int $orderTotal) : bool
    {
        if($orderTotal > 0){
            return $orderTotal >= $this->getMinOrder();
        }
        return false;
    }

    /**
     * @param string $salesType
     * @return bool
     */
    public function _checkSalesTypeValidity(string $salesType) : bool
    {
        $promotionSalesType = $this->getSalesType();
        return strpos($promotionSalesType, $salesType) !== false;
    }

    /**
     * @return bool
     */
    public function _checkUsedQyValidity() : bool
    {
        $promotionQty = $this->getQty();
        $usedQty = $this->getUsedQty();
        return $promotionQty === 0 || $usedQty < $promotionQty;
    }


    public function _toCartPromotion(int $cartTotal){
        return CartPromotion::inst($this, $cartTotal);
    }

}