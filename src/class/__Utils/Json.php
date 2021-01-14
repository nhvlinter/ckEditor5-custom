<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-05-20
 * Time: 02:48
 */

namespace salesteck\utils;


use ReflectionClass;
use salesteck\_interface\JsonToClass;

/**
 * Class Json
 * @package salesteck\utils
 */
class Json
{


    /**
     * @param $string
     *
     * @return bool
     */
    public static function isJson($string) {
        if(String_Helper::_isStringNotEmpty($string)){
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        }
        return false;
    }

    /**
     * @param        $jsonObj
     * @param string $className
     *
     * @return null|object
     */
    public static function _arrayToClass($jsonObj , string $className){
        if($className !== "" && is_array($jsonObj)){
            try{
                $reflection = new ReflectionClass($className);
                if($reflection->implementsInterface(JsonToClass::class)){
                    $properties = $reflection->getProperties();
                    $array = [];
                    foreach ($properties as $property){
                        if( $property instanceof \ReflectionProperty){
                            $propertyString = $property->getName();
                            if(array_key_exists($propertyString, $jsonObj)){
                                $array[$propertyString] = $jsonObj[$propertyString];
                            }
                        }
                    }
                    $class =  $reflection->newInstanceWithoutConstructor();
                    $class->jsonToClass($array);
                    return $class;
                }
            }catch (\ReflectionException $exception){
                return null;
            }
        }
        return null;
    }

    /**
     * @param        $jsonObj
     * @param string $className
     *
     * @return null|object
     */
    public static function _jsonToClass($jsonObj , string $className){
        if($className !== "" && is_object($jsonObj)){
            try{
                $reflection = new ReflectionClass($className);
                if($reflection->implementsInterface(JsonToClass::class)){
                    $properties = $reflection->getProperties();
                    $array = [];
                    foreach ($properties as $property){
                        if( $property instanceof \ReflectionProperty){
                            $propertyString = $property->getName();
                            if(array_key_exists($propertyString, $jsonObj)){
                                $array[$propertyString] = $jsonObj->$propertyString;
                            }
                        }
                    }
                    if(sizeof($array) > 0){
                        $class =  $reflection->newInstanceWithoutConstructor();
                        $class->jsonToClass($array);
                        return $class;
                    }
                }
            }catch (\ReflectionException $exception){
                return null;
            }
        }
        return null;
    }

}