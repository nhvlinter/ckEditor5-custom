<?php
namespace salesteck\booking;
use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbController;
use salesteck\_interface\DbIdCode;
use salesteck\Db\Db;
use salesteck\Db\Sql;


/**
 * Created by PhpStorm.
 * User: Son
 * Date: 15-03-20
 * Time: 23:20
 */

class Booking_C extends Db implements DbIdCode, DbController, DbCleaner
{
    public const TABLE = "_booking";

    static function _getUniqueId(): string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }

    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _clean(bool $debug = true)
    {
        // TODO: Implement _clean() method.
    }
}