<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-05-20
 * Time: 03:20
 */

namespace salesteck\_interface;


interface JsonToClass extends \JsonSerializable
{
    public function jsonToClass(array $json);
}