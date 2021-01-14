<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 05-05-20
 * Time: 22:59
 */
namespace salesteck\product;




use JsonSerializable;
use salesteck\_interface\ArrayUnique;

class Allergen implements JsonSerializable, ArrayUnique
{
    private $idCode, $name, $description;

    /**
     * ProductAllergen constructor.
     * @param string $idCode
     * @param string $name
     * @param string $description
     */
    public function __construct(string $idCode, string $name, string $description)
    {
        $this->idCode = $idCode;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getIdCode()
    {
        return $this->idCode;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
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


    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }
}