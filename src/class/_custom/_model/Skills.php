<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 01-10-19
 * Time: 17:40
 */

namespace salesteck\custom;
use Content\Image;
use JsonSerializable;
use salesteck\utils\Debug;
use salesteck\utils\File;

class Skills implements JsonSerializable
{

    private const
        IMAGE_FOLDER = Image::IMAGE_FOLDER_PATH."skills/"
    ;
    private $name, $imgUrl, $enable;

    /**
     * Skills constructor.
     * @param string $name
     * @param string $imgUrl
     * @param bool $enable
     */
    public function __construct(string $name, string $imgUrl, bool $enable = true)
    {
        $this->name = $name;
        $this->imgUrl = $imgUrl;
        $this->enable = $enable;
        Debug::_exposeVariable([__CLASS__=>$this]);
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
    public function getImgUrl() :string
    {
        return $this->imgUrl;
    }

    /**
     * @return string
     */
    public function getAbsoluteImagePath() :string
    {
        return $this->getImgUrl();
    }

    /**
     * @return bool
     */
    public function imageFileExist() : bool
    {
        return File::_fileExist($this->getAbsoluteImagePath());
    }

    /**
     * @return bool
     */
    public function isEnable() : bool
    {
        return $this->enable;
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