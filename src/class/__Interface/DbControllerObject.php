<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 08-11-19
 * Time: 16:35
 */

namespace salesteck\_interface;

interface DbControllerObject extends DbController
{
    static function _getObjectClassFromResultRow($row);
}