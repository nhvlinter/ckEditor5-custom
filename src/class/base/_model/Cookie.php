<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 20-11-20
 * Time: 01:32
 */

namespace salesteck\_base;
use salesteck\_interface\JsonToClass;
use salesteck\utils\Json;


/**
 * Class Cookie
 * @package salesteck\base
 */
class Cookie extends Json implements \JsonSerializable, JsonToClass
{
    private static function _getIpAddress():string
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    private $value, $ipAddress;

    /**
     * Cookie constructor.
     *
     * @param $value
     * @param $path
     */
    public function __construct($value, $path)
    {
        $this->value = $value;
        $this->ipAddress = self::_getIpAddress();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return Cookie
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     *
     * @return Cookie
     */
    public function setIpAddress(string $ipAddress): Cookie
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }



    public function __toString() : string
    {
        return json_encode($this->jsonSerialize());
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

    /**
     * @param array $json
     */
    public function jsonToClass(array $json)
    {
        foreach($json as $key => $value){
            $this->{$key} = $value;
        }
    }
}