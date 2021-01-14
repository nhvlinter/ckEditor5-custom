<?php

namespace salesteck\order;

use ReflectionClass;
use salesteck\_interface\ArrayUnique;
use salesteck\_interface\JsonToClass;
use salesteck\utils\Class_Helper;
use salesteck\utils\Integer_Helper;


/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-05-20
 * Time: 15:18
 */

class CartElement  implements JsonToClass, ArrayUnique
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
     * CartElement constructor.
     * @param int $qty
     */
    private $idCode, $categoryIdCode, $price, $name, $description, $allergen, $qty, $_extraOptions, $_extraOptionsTotal;

    /**
     * CartElement constructor.
     *
     * @param $idCode
     * @param $categoryIdCode
     * @param $price
     * @param $name
     * @param $description
     * @param $allergen
     * @param $qty
     * @param $_extraOptions
     * @param $_extraOptionsTotal
     */
    public function __construct($idCode, $categoryIdCode, $price, $name, $description, $allergen, $qty, $_extraOptions, $_extraOptionsTotal)
    {
        $this->idCode = $idCode;
        $this->categoryIdCode = $categoryIdCode;
        $this->price = $price;
        $this->name = $name;
        $this->description = $description;
        $this->allergen = $allergen;
        $this->qty = $qty;
        $_extraOptionsTotal = abs(intval($_extraOptionsTotal));
        $this->_extraOptionsTotal = $_extraOptionsTotal;
        $this->_extraOptions = $_extraOptions;
        if( is_array($_extraOptions) && sizeof($_extraOptions) > 0 ){
            $arrayExtraOptions = [];
            $optionTotal = 0;
            foreach ( $_extraOptions as $option){
                $option = json_decode(json_encode($option), true);
                $optionId = array_keys($option)[0];
                $option = $option[$optionId];
//                Debug::_prettyPrint($optionId);
//                Debug::_prettyPrint($option);
                $cartOption = CartElementOption::_arrayInst($option);
//                var_dump($cartOption);
                if($cartOption instanceof CartElementOption ){
                    $optionTotal += $cartOption->getPrice();
                    array_push($arrayExtraOptions, $cartOption);
                }
            }
            $this->_extraOptions = $arrayExtraOptions;
            if(sizeof($arrayExtraOptions) > 0){
                $this->_extraOptionsTotal = $optionTotal;
            }

        }
    }

    /**
     * @return mixed
     */
    public function getIdCode()
    {
        return $this->idCode;
    }

    /**
     * @param mixed $idCode
     *
     * @return CartElement
     */
    public function setIdCode($idCode)
    {
        $this->idCode = $idCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategoryIdCode()
    {
        return $this->categoryIdCode;
    }

    /**
     * @param mixed $categoryIdCode
     *
     * @return CartElement
     */
    public function setCategoryIdCode($categoryIdCode)
    {
        $this->categoryIdCode = $categoryIdCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     *
     * @return CartElement
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return CartElement
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @return CartElement
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllergen()
    {
        return $this->allergen;
    }

    /**
     * @param mixed $allergen
     *
     * @return CartElement
     */
    public function setAllergen($allergen)
    {
        $this->allergen = $allergen;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @param mixed $qty
     *
     * @return CartElement
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtraOptions()
    {
        return $this->_extraOptions;
    }

    /**
     * @param mixed $extraOptions
     *
     * @return CartElement
     */
    public function setExtraOptions($extraOptions)
    {
        $this->_extraOptions = $extraOptions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtraOptionsTotal()
    {
        $total = 0;
        if( is_array($this->_extraOptions) && sizeof($this->_extraOptions) > 0 ){
            foreach ( $this->_extraOptions as $option){
                if($option instanceof CartElementOption ){
                    $total += $option->getPrice();
                }
            }

        }
        return $total;
    }

    /**
     * @param mixed $extraOptionsTotal
     *
     * @return CartElement
     */
    public function setExtraOptionsTotal($extraOptionsTotal)
    {
        $this->_extraOptionsTotal = $extraOptionsTotal;
        return $this;
    }

    public function extraOptionToString(){
        $arrStr = [];
        $_extraOptions = $this->_extraOptions;
        if(is_array($_extraOptions)){
            foreach ($_extraOptions as $option){
                if($option instanceof CartElementOption){
                    array_push($arrStr, $option->getName());
                }
            }
        }


        return implode(", ", $arrStr);
    }




    public function getTotal(){
        return ($this->getPrice() + $this->getExtraOptionsTotal()) * $this->getQty();
    }

    public function getTotalString(){
        return Integer_Helper::_intToPrice($this->getTotal());
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

    public function jsonToClass(array $json)
    {
        foreach($json as $key => $value){
            $this->{$key} = $value;
        }
    }

}