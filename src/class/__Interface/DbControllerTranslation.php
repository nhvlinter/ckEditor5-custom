<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 08-11-19
 * Time: 15:30
 */

namespace salesteck\_interface;



use salesteck\Db\Sql;

interface DbControllerTranslation extends DbController
{
    static function _getSqlTranslation() : Sql;
}