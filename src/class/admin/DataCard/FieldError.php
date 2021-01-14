<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 17-06-20
 * Time: 15:58
 */

namespace salesteck\DataCard;


class FieldError implements \JsonSerializable
{

    private $name, $status;

    /**
     * FieldErrors constructor.
     * @param string $name
     * @param string $status
     */
    public function __construct(string $name, string $status)
    {
        $this->name = $name;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
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
        return get_object_vars($this);
    }
}