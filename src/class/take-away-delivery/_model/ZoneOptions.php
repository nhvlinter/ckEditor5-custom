<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 09-05-20
 * Time: 20:29
 */
namespace salesteck\takeAwayDelivery;

use JsonSerializable;

class ZoneOptions implements JsonSerializable
{
    private $idCode, $name, $minOrder, $fee;

    /**
     * ZoneOptions constructor.
     * @param string $idCode
     * @param string $name
     * @param int $minOrder
     * @param int $fee
     */
    private function __construct($idCode, $name, $minOrder, $fee)
    {
        $this->idCode = $idCode;
        $this->name = $name;
        $this->minOrder = $minOrder;
        $this->fee = $fee;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getMinOrder(): int
    {
        return $this->minOrder;
    }

    /**
     * @return int
     */
    public function getFee(): int
    {
        return $this->fee;
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

    public function __toString() : string
    {
        return json_encode($this->jsonSerialize());
    }

    public static function _inst(array $row) : ? self
    {
        if(
            array_key_exists(DeliveryZone_C::_col_id_code, $row) &&
            array_key_exists(DeliveryZone_C::_col_name, $row) &&
            array_key_exists(DeliveryZone_C::_col_post_code, $row) &&
            array_key_exists(DeliveryZone_C::_col_minimum_order, $row) &&
            array_key_exists(DeliveryZone_C::_col_delivery_fee, $row)
        ){
            $idCode = $row[DeliveryZone_C::_col_id_code];
            $name = $row[DeliveryZone_C::_col_name];
            $postCode = $row[DeliveryZone_C::_col_post_code];
            $min = intval($row[DeliveryZone_C::_col_minimum_order]);
            $fee = intval($row[DeliveryZone_C::_col_delivery_fee]);
            if($idCode !== "" && $name !== ""){
                $postCode = $postCode !== "" ? " - $postCode" : "";
                $name = ucfirst($name);
                $name = "$name $postCode";
                $min = $min < 0 ? 0 : $min;
                $fee = $fee < 0 ? 0 : $fee;
                return new self($idCode, $name, $min, $fee);
            }

        }
        return null;
    }
}