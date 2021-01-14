<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 17-10-19
 * Time: 10:47
 */

namespace salesteck\custom;


class Industry implements \JsonSerializable
{
    private $idCode, $name;

    /**
     * Industry constructor.
     * @param string $idCode
     * @param string $name
     */
    public function __construct(string $idCode, string $name)
    {
        $this->idCode = $idCode;
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getIdCode() :string
    {
        return $this->idCode;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
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