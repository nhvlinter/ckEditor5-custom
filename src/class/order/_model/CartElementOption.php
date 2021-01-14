<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 30-11-20
 * Time: 14:20
 */

namespace salesteck\order;


use ReflectionClass;
use salesteck\_interface\ArrayUnique;
use salesteck\utils\Class_Helper;

class CartElementOption implements ArrayUnique, \JsonSerializable
{


    public static function _arrayInst(array $array){
        $classProperties = Class_Helper::_getClassPrivateProperty(self::class);
        $arrayPropValue = [];
        foreach ($classProperties as $property){
            if(array_key_exists($property, $array)){
                $arrayPropValue[$property] = $array[$property];
            }
        }
        if(sizeof($classProperties) === sizeof($arrayPropValue)){
            $reflect  = new ReflectionClass(self::class);
            $instance = $reflect->newInstanceArgs($arrayPropValue);
            return $instance;
        }

        return null;
    }

    /**
     * @var string $idCode
     *
     * @var string $category
     *
     * @var integer $price
     *
     * @var string $language
     *
     * @var string $name
     *
     * @var string $description
     */
    private $idCode, $category, $price, $language, $name, $description;

    /**
     * CartElementOption constructor.
     *
     * @param string $idCode
     * @param string $category
     * @param mixed $price
     * @param string $language
     * @param string $name
     * @param string $description
     */
    public function __construct(string $idCode, string $category, $price, string $language, string $name, string $description)
    {
        $this->idCode = $idCode;
        $this->category = $category;
        $this->price = abs(intval($price));
        $this->language = $language;
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
     * @param string $idCode
     *
     * @return CartElementOption
     */
    public function setIdCode(string $idCode)
    {
        $this->idCode = $idCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory() : string
    {
        return $this->category;
    }

    /**
     * @param string $category
     *
     * @return CartElementOption
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrice() : int
    {
        return $this->price;
    }

    /**
     * @param int $price
     *
     * @return CartElementOption
     */
    public function setPrice(int $price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     *
     * @return CartElementOption
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return CartElementOption
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return CartElementOption
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }






    public function __toString() : string
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * Specify data which should be serialized to JSON
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}