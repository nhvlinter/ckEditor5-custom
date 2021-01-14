<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 17-06-20
 * Time: 15:49
 */

namespace salesteck\DataCard;


use salesteck\utils\String_Helper;

class DataCardResponse implements \JsonSerializable
{
    private $data, $multiItems, $message, $options, $element, $error, $fieldErrors, $cancelled, $debug, $files, $upload, $status;
    public const
        S_OK = 1,
        S_SESSION_TIMEOUT = 0,
        S_FORM_ERROR = -1,
        S_UNKNOWN_ERROR = -2
    ;
    private const VALID_STATUS = [self::S_OK, self::S_SESSION_TIMEOUT, self::S_FORM_ERROR, self::S_UNKNOWN_ERROR];

    public static function _inst(array $data = []){
        return new self($data);
    }

    /**
     * DataCardResponse constructor.
     * @param array $data
     */
    private function __construct(array $data = [])
    {
        $this->data = $data;
        $this->multiItems = [];
        $this->options = [];
        $this->upload = [];
        $this->error = "";
        $this->message = "";
        $this->fieldErrors = [];
        $this->cancelled = [];
        $this->debug = [];
        $this->files = [];
        $this->element = [];
        $this->status = self::S_OK;
    }


    /**
     * @param array $multiItems
     * @return $this
     */
    private function setMultiItems(array $multiItems)
    {
        $this->multiItems = $multiItems;
        return $this;
    }


    /**
     * @return string
     */
    private function getError() : string
    {
        if(isset($this->error)){
            return $this->error;
        }
        return "";
    }

    /**
     * @param string $error
     * @return $this
     */
    private function setError(string $error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return array
     */
    private function getFieldErrors() : array
    {
        if(isset($this->fieldErrors)){
            return $this->fieldErrors;
        }
        return [];
    }

    /**
     * @param array $fieldErrors
     * @return $this
     */
    private function setFieldErrors(array $fieldErrors)
    {
        $this->fieldErrors = $fieldErrors;
        return $this;
    }

    /**
     * @return array
     */
    private function getCancelled() : array
    {
        if(isset($this->cancelled)){
            return $this->cancelled;
        }
        return [];
    }

    /**
     * @return array
     */
    public function getDebug(): array
    {
        return $this->debug;
    }

    /**
     * @param array $debug
     * @return $this
     */
    public function setDebug(array $debug)
    {
        $this->debug = $debug;
        return $this;
    }
    









    /**
     * @param array $data
     * @return $this
     */
    public function data(array $data){
        if(sizeof($data) > 0){
            $this->data = $data;
        }
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function options($options = null){
        if(gettype($options) === gettype([])){
            if(sizeof($options) > 0){
                $this->options = $options;
            }
        }
        return $this;
    }

    public function files($files = null){
        $this->files = $files;
        return $this;
    }


    /**
     * @param string $error
     * @return $this
     */
    public function error(string $error){
        return $this->setError($error);
    }

    /**
     * @param FieldError $fieldError
     * @return $this
     */
    private function addFieldError(FieldError $fieldError){
        if($fieldError !== null && $fieldError instanceof FieldError){
            $arrayFieldError = $this->getFieldErrors();
            array_push($arrayFieldError, $fieldError);
            $this->setFieldErrors($arrayFieldError);
        }
        return $this;
    }


    /**
     * @param string $fieldName
     * @param string $error
     * @return $this
     * @internal param FieldError $fieldError
     */
    public function fieldError(string $fieldName, string $error){
        $fieldError = new FieldError($fieldName, $error);
        return $this->addFieldError($fieldError);
    }


    public function multiItems(array $multiItems){
        if(sizeof($multiItems) > 0){
            $this->setMultiItems($multiItems);
        }
        return $this;
    }

    /**
     * @param array $arg
     *
     * @return $this
     * @internal param string $name
     * @internal param $var
     */
    public function debug(...$arg){
        $debug = $this->getDebug();

        $args  =func_get_args();
        if(sizeof($args) > 0){
            if(sizeof($args) === 1){
                array_push($debug, $args[0]);
            }else{
                if(String_Helper::_isStringNotEmpty($args[0])){
                    array_push($debug, [$args[0] => $args[1]]);
                }

            }
        }
//        if(String_Helper::_isStringNotEmpty($name)){
//            array_push($debug, [$name => $var]);
//        }else{
//
//            array_push($debug, $var);
//        }
        $this->setDebug($debug);
        return $this;
    }

    public function upload($uploadId){
        if($uploadId !== null){
            $this->upload['id'] = $uploadId;
        }
        return $this;
    }

    public function status(int $status){
        if(in_array($status, self::VALID_STATUS)){
            $this->status = $status;
            if($status === self::S_OK){
                $this->message("");
            }
        }else{
            $this->status = self::S_UNKNOWN_ERROR;
        }
        return $this;
    }

    public function message(string $message){
        if(is_string($message) && $message !== ""){
            $this->message = $message;
        }
        return $this;
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
        $response = $this;
        if($response->getError() === ""){
            unset($response->error);
        }
        if( sizeof($response->getFieldErrors() )=== 0 ){
            unset($response->fieldErrors);
        }
        if( sizeof($response->getCancelled() ) === 0 ){
            unset($response->cancelled);
        }
        if( sizeof($response->getDebug() ) === 0 ){
            unset($response->debug);
        }
        if( isset($response->upload) && sizeof($response->upload ) === 0 ){
            unset($response->upload);
        }
        if( isset($response->multiItems) &&  sizeof($response->multiItems ) === 0 ){
            unset($response->multiItems);
        }
        if( isset($response->files) &&  sizeof($response->files ) === 0 ){
            unset($response->files);
        }
        return get_object_vars($response);
    }
}