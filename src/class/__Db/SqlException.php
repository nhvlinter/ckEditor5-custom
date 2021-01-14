<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 09-12-20
 * Time: 00:03
 */

namespace salesteck\Db;


use Exception;

class SqlException extends \Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}