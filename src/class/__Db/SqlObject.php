<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 05-11-19
 * Time: 00:41
 */

namespace salesteck\Db;


abstract class SqlObject extends Db implements \JsonSerializable
{
    public abstract function update();

    public abstract static function _delete();
}