<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 30-07-20
 * Time: 16:39
 */

namespace salesteck\_base;


use salesteck\_interface\DbController;
use salesteck\Db\Db;
use salesteck\Db\Sql;

class OpeningHours_C extends Db implements DbController
{
    public const TABLE = "_hours_opening";

    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }
}