<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 02-11-20
 * Time: 14:46
 */

namespace salesteck\api;
use salesteck\utils\File;
use salesteck\utils\String_Helper;


/**
 * Class RedirectResponse
 * @package salesteck\api
 */
class RedirectResponse extends RequestResponse
{
    protected $redirect;

    public static function _inst($__FILE__ = ""){
        $backtrace = debug_backtrace();
        $arrayColumn = array_column($backtrace, 'function');
        $key = array_search(__FUNCTION__, $arrayColumn);
        $__FILE__ = ($backtrace[$key]['file']);
        $f = "";
        if(String_Helper::_isStringNotEmpty($__FILE__)){
            $f = File::_getProjectAbsolutePath($__FILE__);
        }
        return new self($f);
    }


    /**
     * OrderResponse constructor.
     *
     * @param string $__FILE__
     */
    public function __construct(string $__FILE__ = "")
    {
        parent::__construct($__FILE__);
        $this->redirect = "";
    }

    /**
     * @return mixed
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @param mixed $redirect
     */
    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
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