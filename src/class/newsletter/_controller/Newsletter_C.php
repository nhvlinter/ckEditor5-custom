<?php
namespace salesteck\newsletter;

use salesteck\_interface\DbController;
use salesteck\Db\Db;
use salesteck\Db\Sql;

/**
 * Created by PhpStorm.
 * User: Son
 * Date: 29-07-20
 * Time: 00:10
 */

class Newsletter_c extends Db implements DbController
{

    public const TABLE = "_newsletter";


    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }
}