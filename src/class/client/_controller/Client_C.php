<?php
namespace salesteck\client;
use salesteck\_interface\DbCleaner;
use salesteck\Db\Db;
use salesteck\Db\Sql;


/**
 * Created by PhpStorm.
 * User: Son
 * Date: 15-03-20
 * Time: 23:26
 */

class Client_C extends Db implements DbCleaner
{


    static function _clean(bool $debug = false)
    {
        // TODO: Implement _clean() method.
    }

    static function _getSql(): Sql
    {
        // TODO: Implement _getSql() method.
    }
}