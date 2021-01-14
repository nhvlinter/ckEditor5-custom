<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-05-20
 * Time: 01:21
 */
namespace salesteck\takeAwayDelivery;

use JsonSerializable;

class DeliveryZone implements JsonSerializable
{
    private $idCode, $name, $postCode, $fee, $minOrder;

    /**
     * DeliveryZone constructor.
     * @param $idCode
     * @param $name
     * @param $postCode
     * @param $fee
     * @param $minOrder
     */
    public function __construct(string $idCode, string $name, string $postCode, int $fee, int $minOrder)
    {
        $this->idCode = $idCode;
        $this->name = $name;
        $this->postCode = $postCode;
        $this->fee = $fee;
        $this->minOrder = $minOrder;
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
     * @return string
     */
    public function getPostCode(): string
    {
        return $this->postCode;
    }

    /**
     * @return int
     */
    public function getFee(): int
    {
        return $this->fee;
    }

    /**
     * @return int
     */
    public function getMinOrder(): int
    {
        return $this->minOrder;
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

    public function __toString()
    {
        return json_encode($this->jsonSerialize());
    }
}