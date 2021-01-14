<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 28-05-20
 * Time: 16:25
 */

namespace salesteck\promotion;


use salesteck\_base\Language_C;
use salesteck\utils\String_Helper;

class PromotionTranslation extends Promotion implements \JsonSerializable
{
    public static function _fromPromoCodeLanguage( $promoCode, string $language) : ? self
    {
        if(String_Helper::_isStringNotEmpty($promoCode) && String_Helper::_isStringNotEmpty($language) ){
            $language = Language_C::_getValidLanguage($language);
            $sql = Promotion_C::_getSql();
            $sql
                ->leftJoin(
                    Promotion_C::TABLE, Promotion_C::_col_id_code, 
                    Promotion_C::TABLE_TRANSLATION, Promotion_C::_col_id_code
                )
                ->equal(Promotion_C::TABLE, Promotion_C::_col_code, $promoCode)
                ->equal(Promotion_C::TABLE, Promotion_C::_col_is_valid, intval(true))
                ->equal(Promotion_C::TABLE_TRANSLATION, Promotion_C::_col_language, $language)
            ;

            if($sql->select()){
                $row = $sql->first();
                return self::_instTranslation($row);
            }

        }

        return null;

    }

    public static function _instTranslation($row) : ? self
    {
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
            array_key_exists(Promotion_C::_col_sales_type, $row) &&
            array_key_exists(Promotion_C::_col_name, $row) &&
            array_key_exists(Promotion_C::_col_title, $row) &&
            array_key_exists(Promotion_C::_col_description, $row)
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
                $row[Promotion_C::_col_sales_type],
                $row[Promotion_C::_col_name],
                $row[Promotion_C::_col_title],
                $row[Promotion_C::_col_description]
            );

        }

        return null;
    }


    private $name, $title, $description;

    /**
     * PromotionTranslation constructor.
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
     * @param int $salesType
     *
     * @param string $name
     * @param string $title
     * @param string $description
     */
    public function __construct(
        string $idCode, string $promoCode, int $startTime, int $endTime, int $type, int $minOrder,
        bool $isEnable, string $daysValidity, int $value, int $usedQty, int $qty, int $salesType,
        string $name, string $title, string $description
    )
    {
        parent::__construct($idCode, $promoCode, $startTime, $endTime, $type, $minOrder, $isEnable, $daysValidity, $value, $usedQty, $qty, $salesType);
        $this->name = $name;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
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
}