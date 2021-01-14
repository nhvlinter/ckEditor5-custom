<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 29-05-20
 * Time: 00:48
 */

namespace salesteck\order;


use salesteck\_interface\JsonToClass;
use salesteck\promotion\Promotion;
use salesteck\promotion\Promotion_C;
use salesteck\utils\Converter;

class CartPromotion implements \JsonSerializable, JsonToClass
{
    private $code, $value, $valueString;

    public static function inst (Promotion $promotion, int $cartTotal) : ? self
    {
        $cartTotal = abs($cartTotal);
        if($promotion !== null){
            $code = $promotion->getPromoCode();
            $value = $promotion->getValue();
            $valueType = $promotion->getType();
            $valueString = Converter::_intToPrice($value);
            if(intval($valueType) === Promotion_C::type_percent){
                $valueString = intval($value /100) . " %";
                $value = ceil(($cartTotal * ($value /100)) /100);

            }
            return new self($code, $value, "'$code' ($valueString)");
        }
        return null;
    }

    /**
     * OrderPromotion constructor.
     * @param string $code
     * @param int $value
     * @param string $valueString
     */
    private function __construct(string $code, int $value, string $valueString)
    {
        $this->code = $code;
        $this->value = $value;
        $this->valueString = $valueString;
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

    public function jsonToClass(array $json)
    {
        foreach($json as $key => $value){
            $this->{$key} = $value;
        }
    }
}