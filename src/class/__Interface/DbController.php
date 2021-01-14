<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 08-11-19
 * Time: 15:29
 */

namespace salesteck\_interface;



use salesteck\Db\Sql;

/**
 * Interface DbController
 * @package salesteck\_interface
 */
interface DbController
{

    /**
     * @return \salesteck\Db\Sql
     */
    static function _getSql() : Sql;
}