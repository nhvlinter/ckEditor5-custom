<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-05-20
 * Time: 01:08
 */

namespace salesteck\_interface;


/**
 * Interface DbIdCode
 * @package salesteck\_interface
 */
interface DbIdCode
{
    /**
     * @return string
     */
    static function _getUniqueId() : string ;
}