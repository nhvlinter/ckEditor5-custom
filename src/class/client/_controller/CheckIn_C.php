<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 28-07-20
 * Time: 18:07
 */

namespace salesteck\client;


use salesteck\_interface\DbCleaner;
use salesteck\Db\Db;
use salesteck\Db\Sql;

class CheckIn_C extends Db implements DbCleaner
{
    public const TABLE = "_check_in";


    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _clean(bool $debug = false)
    {
        // TODO: Implement _clean() method.
    }
}