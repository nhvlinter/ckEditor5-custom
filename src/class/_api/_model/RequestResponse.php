<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 25-10-20
 * Time: 19:40
 */
namespace salesteck\api;

use JsonSerializable;
use salesteck\utils\File;
use salesteck\utils\String_Helper;

class RequestResponse extends Request implements JsonSerializable
{



    public static function _inst($__FILE__ = ""){
        $backtrace = debug_backtrace();
        $arrayColumn = array_column($backtrace, 'function');
        $key = array_search(__FUNCTION__, $arrayColumn);
        if( !String_Helper::_isStringNotEmpty($__FILE__)){
            $__FILE__ = ($backtrace[$key]['file']);
        }
        $f = "";
        if(String_Helper::_isStringNotEmpty($__FILE__)){
            $f = File::_getProjectAbsolutePath($__FILE__);
            $f = str_replace(".php", "", $f);
        }
        return new self($f);
    }



    protected $data, $options;

    /**
     * RequestResponse constructor.
     *
     * @param string $__FILE__
     */
    public function __construct(string $__FILE__ = "")
    {
        parent::__construct($__FILE__);
        $this->data = [];
        $this->options = [];
    }

//    /**
//     * @return mixed
//     */
//    public function getStatus()
//    {
//        return $this->status;
//    }
//
//    /**
//     * @param bool $status
//     * @return $this
//     */
//    public function setStatus($status): self
//    {
//        if(is_integer($status) || is_bool($status)){
//            $this->status = $status;
//            if($status === self::STATUS_VALID){
//                $this->clearMessage();
//            }
//        }
//        return $this;
//    }
//
//    private function clearMessage() : self
//    {
//        return $this->setMessage("");
//    }
//
//    /**
//     * @return string
//     */
//    public function getMessage() : string
//    {
//        return $this->message;
//    }
//
//    /**
//     * @param string $message
//     * @return $this
//     */
//    public function setMessage(string $message): self
//    {
//        $this->message = $message;
//        return $this;
//    }
//
//    /**
//     * @param string $message
//     * @return $this
//     */
//    public function message(string $message): self
//    {
//        return $this->setMessage($message);
//    }
//
//    public function debug(...$arg){
//        $args  = func_get_args();
//        if(sizeof($args) > 1){
//            $varName = $args[0];
//            if(String_Helper::_isStringNotEmpty($varName)){
//                array_push($this->arrayDebug, [$varName => $args[1]]);
//            }
//        }else if(sizeof($args) === 1){
//            array_push($this->arrayDebug, $args[0]);
//        }
//        return $this;
//    }
//
//    /**
//     * @return bool
//     */
//    public function isDebug(): bool
//    {
//        return $this->debug;
//    }
//
//    /**
//     * @param bool $debug
//     * @return RequestResponse
//     */
//    public function setDebug(bool $debug) : self
//    {
//        $this->debug = $debug;
//        return $this;
//    }
//
//
//
//    /**
//     * @return array
//     */
//    public function getArrayDebug(): array
//    {
//        return $this->arrayDebug;
//    }
//
//    /**
//     * @param array $arrayDebug
//     * @param array|bool $debug
//     * @return RequestResponse
//     */
//    public function setArrayDebug(array $arrayDebug, bool $debug = true) : self
//    {
//        if($debug){
//            $this->arrayDebug = $arrayDebug;
//        }
//        return $this;
//    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData($data) : RequestResponse
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options) : RequestResponse
    {
        $this->options = $options;
        return $this;
    }



    public function display(){
        $obj = clone $this;
        if($obj->debug === false && boolval($obj->status) === true){
            unset($obj->debug);
            unset($obj->arrayDebug);
        }
        if(sizeof($obj->options) === 0){
            unset($obj->options);
        }
        if( !String_Helper::_isStringNotEmpty($obj->error) ){
            unset($obj->error);
        }
        echo json_encode($obj, JSON_PRETTY_PRINT);
    }



    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}