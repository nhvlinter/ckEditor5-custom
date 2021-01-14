<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 12-11-19
 * Time: 01:14
 */

namespace salesteck\_base;


class SocialMedia implements \JsonSerializable
{
    private $label, $icon, $link, $isEnable, $color;

    /**
     * SocialMedia constructor.
     * @param string $label
     * @param string $icon
     * @param string $link
     * @param string $isEnable
     * @param string $color
     */
    public function __construct(string $label, string $icon, string $link, string $isEnable, string $color)
    {
        $this->label = $label;
        $this->icon = $icon;
        $this->link = $link;
        $this->isEnable = boolval($isEnable);
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->isEnable;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
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