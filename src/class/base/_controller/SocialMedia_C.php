<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 12-11-19
 * Time: 01:22
 */

namespace salesteck\_base;



use salesteck\_interface\DbController;
use salesteck\Db\Db;
use salesteck\Db\Sql;

class SocialMedia_C extends Db implements DbController
{
    public const TABLE = "_social_media";

    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }
}