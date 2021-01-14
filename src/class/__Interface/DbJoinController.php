<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 08-11-19
 * Time: 16:55
 */

namespace salesteck\_interface;



use salesteck\Db\Sql;

interface DbJoinController extends DbControllerTranslation
{
    static function _getJoinSql() : Sql;
}