<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 17-11-20
 * Time: 23:35
 */

namespace salesteck\utils;


use ReflectionProperty;

class Class_Helper
{

    public static function _getClassName($var){
        if(String_Helper::_isStringNotEmpty($var) || is_object($var)){
            return (new \ReflectionClass($var))->getShortName();
        }
        return "";
    }



    public static function _getClassStaticProperty(string $className){
        $arrayProperty = [];
        if(String_Helper::_isStringNotEmpty($className)){
            $class = new \ReflectionClass($className);
            $arrayProperty = $class->getProperties();
            foreach ($arrayProperty as $property){
                if($property instanceof ReflectionProperty && $property->isStatic()){
                    array_push($arrayProperty, $property->getName());
                }
            }
        }
        return $arrayProperty;
    }

    public static function _getClassPrivateProperty(string $className){
        $properties = [];
        if(String_Helper::_isStringNotEmpty($className)){
            $class = new \ReflectionClass($className);
            $arrayProperty = $class->getProperties();
            foreach ($arrayProperty as $property){
                if($property instanceof ReflectionProperty && !$property->isStatic() && $property->isPrivate()){
                    array_push($properties, $property->getName());
                }
            }
        }
        return $properties;
    }
}