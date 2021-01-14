<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 23-12-20
 * Time: 13:07
 */

namespace salesteck\api;


use Error;
use Exception;
use JsonSerializable;
use salesteck\config\Config;
use salesteck\utils\File;
use salesteck\utils\String_Helper;

class Request implements JsonSerializable
{
    public const
        STATUS_VALID = true,
        STATUS_INVALID = false,
        STATUS_SESSION_TIME_OUT = 0
    ;


    public const
        HTTP_OK_200 = 200,
        HTTP_NO_CONTENT_204 = 204,
        HTTP_BAD_REQUEST_400 = 400,
        HTTP_UNAUTHORIZED_401 = 401,
        HTTP_FORBIDDEN_403 = 403,
        HTTP_NOT_FOUND_404 = 404,
        HTTP_INTERNAL_ERROR_500 = 500

    ;



    public static function _inst(){
        $backtrace = debug_backtrace();
        $arrayColumn = array_column($backtrace, 'function');
        $key = array_search(__FUNCTION__, $arrayColumn);
        $__FILE__ = ($backtrace[$key]['file']);
        $f = "";
        if(String_Helper::_isStringNotEmpty($__FILE__)){
            $f = File::_getProjectAbsolutePath($__FILE__);
            $f = str_replace(".php", "", $f);
        }
        return new self($f);
    }


    protected $status, $message, $arrayDebug, $debug, $error;

    /**
     * RequestResponse constructor.
     *
     * @param string $__FILE__
     */
    public function __construct(string $__FILE__ = "")
    {
        $this->status = self::STATUS_INVALID;
        $this->message = "";
        $this->arrayDebug = [];
        $this->error = "";
        $this->debug = Config::_isDebug();
        if(String_Helper::_isStringNotEmpty($__FILE__)){
            $this->debug("FILE", $__FILE__);
        }
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function setStatus($status): self
    {
        if(is_integer($status) || is_bool($status)){
            $this->status = $status;
            if($status === self::STATUS_VALID){
                $this->clearMessage();
            }
        }
        return $this;
    }

    private function clearMessage() : self
    {
        return $this->setMessage("");
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return self
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param string $message
     * @return self
     */
    public function message(string $message) : self
    {
        return $this->setMessage($message);
    }

    public function debug(...$arg){
        $args  = func_get_args();
        if(sizeof($args) > 1){
            $varName = $args[0];
            if(String_Helper::_isStringNotEmpty($varName)){
                array_push($this->arrayDebug, [$varName => $args[1]]);
            }
        }else if(sizeof($args) === 1){
            array_push($this->arrayDebug, $args[0]);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     * @return Request
     */
    public function setDebug(bool $debug) : self
    {
        $this->debug = $debug;
        return $this;
    }



    /**
     * @return array
     */
    public function getArrayDebug(): array
    {
        return $this->arrayDebug;
    }


    /**
     *
     * @param Error $error
     *
     * @return Request
     */
    public function error(Error $error){
        $this->error = self::_errorToString($error);
        return $this;
    }


    /**
     *
     * @param Exception $exception
     *
     * @return Request
     */
    public function exception(Exception $exception){
        $this->error = self::_exceptionToString($exception);
        return $this;
    }

    /**
     * @param array $arrayDebug
     * @param array|bool $debug
     * @return Request
     */
    public function setArrayDebug(array $arrayDebug, bool $debug = true) : self
    {
        if($debug){
            $this->arrayDebug = $arrayDebug;
        }
        return $this;
    }

    public function _line(string $__LINE__){
        $key = array_search(__FUNCTION__, array_column(debug_backtrace(), 'function'));
        $__FILE__ = (debug_backtrace()[$key]['file']);
        $f = File::_getProjectAbsolutePath($__FILE__);
        $f = str_replace(".php", "", $f);
        return $this->debug("LOCATION", "$f at line : $__LINE__");
    }

    public function _file(){
        $key = array_search(__FUNCTION__, array_column(debug_backtrace(), 'function'));
        $__FILE__ = (debug_backtrace()[$key]['file']);
        if(String_Helper::_isStringNotEmpty($__FILE__)){
            $f = File::_getProjectAbsolutePath($__FILE__);
            $f = str_replace(".php", "", $f);
            return $this
                ->debug("FILE", $f)
                ;
        }
        return $this;
    }

    public function _endFile() : self
    {
        $key = array_search(__FUNCTION__, array_column(debug_backtrace(), 'function'));
        $__FILE__ = (debug_backtrace()[$key]['file']);
        if(String_Helper::_isStringNotEmpty($__FILE__)){
            $f = File::_getProjectAbsolutePath($__FILE__);
            $f = str_replace(".php", "", $f);
            return $this->debug("END FILE", $f);
        }
        return $this;
    }

    public function _function($function, $className = "") : self
    {
        $debugStr = "";
        if(String_Helper::_isStringNotEmpty($function) ){
            if(String_Helper::_isStringNotEmpty($className) && class_exists($className)){
                $reflection = new \ReflectionClass($className);
                $class = $reflection->getShortName();
                $debugStr .= "$class::";
            }
            $debugStr .= $function;
        }
        if(String_Helper::_isStringNotEmpty($debugStr)){
            $this->debug("FUNTION", $debugStr);

        }
        return $this;
    }



    public function display(){
        $obj = clone $this;
        if($obj->debug === false && boolval($obj->status) === true){
            unset($obj->debug);
            unset($obj->arrayDebug);
        }
        if( !String_Helper::_isStringNotEmpty($obj->error) ){
            unset($obj->error);
        }
        echo json_encode($obj, JSON_PRETTY_PRINT);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public static function _errorToString(\Error $error){
        $message = $error->getMessage();
        $file = $error->getFile();
        $file = File::_getProjectAbsolutePath($file);
        $file = str_replace(".php", "", $file);
        $line = $error->getLine();
        $str = "Error at line : $line in $file ($message)";

        return $str;
    }

    public static function _exceptionToString(Exception $exception){
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $file = File::_getProjectAbsolutePath($file);
        $file = str_replace(".php", "", $file);
        $line = $exception->getLine();
        $str = "Error at line : $line in $file ($message)";

        return $str;
    }
}