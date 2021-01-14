<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 17-10-19
 * Time: 06:45
 */

namespace salesteck\custom;

class Country
{


    private $idCode, $languageCode, $name, $phonePrefix;

    /**
     * Country constructor.
     * @param string $idCode
     * @param string $languageCode
     * @param string $name
     * @param string $phonePrefix
     */
    public function __construct(string $idCode, string $languageCode, string $name, string $phonePrefix)
    {
        $this->idCode = $idCode;
        $this->languageCode = $languageCode;
        $this->name = $name;
        $this->phonePrefix = $phonePrefix;
    }

    /**
     * @return string
     */
    public function getIdCode() : string
    {
        return $this->idCode;
    }

    /**
     * @return string
     */
    public function getLanguageCode() : string
    {
        return $this->languageCode;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPhonePrefix() : string
    {
        return $this->phonePrefix;
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