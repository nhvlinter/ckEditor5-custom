<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 06-08-19
 * Time: 02:00
 */

namespace salesteck\_base;
use salesteck\Db\Db;


/**
 * Class Language
 * @package salesteck
 */
class Language implements \JsonSerializable
{

    public static function _inst($row) : ? self
    {
        if(
            is_array($row) &&
            array_key_exists(Db::_col_language, $row) &&
            array_key_exists(Db::_col_name, $row) &&
            array_key_exists(Db::_col_icon, $row) &&
            array_key_exists(Db::_col_is_enable, $row)
        ){
            return new Language(
                $row[Db::_col_language],
                $row[Db::_col_name],
                $row[Db::_col_icon],
                $row[Db::_col_is_enable]
            );
        }
        return null;
    }

    private
        $idCode,
        $name,
        $icon,
        $enable
    ;

    /**
     * Language constructor.
     * @param string $idCode
     * @param string $name
     * @param string $icon
     * @param string $enable
     */
    public function __construct(string $idCode, string $name, string $icon, string $enable)
    {
        $this->idCode = $idCode;
        $this->name = $name;
        $this->icon = $icon;
        $this->enable = boolval($enable);
    }

    /**
     * @return string
     */
    public function getIdCode(): string
    {
        return $this->idCode;
    }

    /**
     * @param string $idCode
     * @return $this
     */
    public function setIdCode(string $idCode)
    {
        $this->idCode = $idCode;
        return $this;
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
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon(string $icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnable(): string
    {
        return $this->enable;
    }

    /**
     * @param bool $enable
     * @return $this
     */
    public function setEnable(bool $enable)
    {
        $this->enable = strval($enable);
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