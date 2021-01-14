<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 08-08-20
 * Time: 00:43
 */

namespace salesteck\_interface;


interface DbCleaner extends DbController
{
    static function _clean(bool $debug = false);
}