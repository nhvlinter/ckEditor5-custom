<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 29-05-20
 * Time: 00:01
 */

namespace salesteck\order;


use salesteck\utils\Converter;

class OrderPromotion implements \JsonSerializable
{
    public static function _inst($row) : ? self
    {
        if(
            is_array($row) &&
            array_key_exists(OrderPromotion_C::_col_id, $row) &&
            array_key_exists(OrderPromotion_C::_col_code, $row) &&
            array_key_exists(OrderPromotion_C::_col_value, $row) &&
            array_key_exists(OrderPromotion_C::_col_value_string, $row)
        ){
            return new self(
                $row[OrderPromotion_C::_col_id],
                $row[OrderPromotion_C::_col_code],
                $row[OrderPromotion_C::_col_value],
                $row[OrderPromotion_C::_col_value_string]
            );
        }
        return null;
    }

    private $id, $code, $value, $valueString;

    /**
     * OrderPromotion constructor.
     * @param int $id
     * @param string $code
     * @param int $value
     * @param string $valueString
     */
    public function __construct(int $id, string $code, int $value, string $valueString)
    {
        $this->id = $id;
        $this->code = $code;
        $this->value = $value;
        $this->valueString = $valueString;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    public function getValuePrice() : string
    {
        return Converter::_intToPrice($this->getValue());
    }

    /**
     * @param int $value
     */
    public function setValue(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValueString(): string
    {
        return $this->valueString;
    }

    /**
     * @param string $valueString
     */
    public function setValueString(string $valueString)
    {
        $this->valueString = $valueString;
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