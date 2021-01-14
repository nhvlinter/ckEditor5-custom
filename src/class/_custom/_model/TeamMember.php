<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 03-10-19
 * Time: 00:52
 */

namespace salesteck\custom;

use Content\Image;
use salesteck\utils\File;

class TeamMember
{
    private const
        IMAGE_FOLDER = Image::IMAGE_FOLDER_PATH."team/";
    private $memberName, $memberPosition, $memberImage;

    /**
     * TeamMembers constructor.
     * @param string $memberName
     * @param string $memberPosition
     * @param string $memberImage
     */
    public function __construct(string $memberName, string $memberPosition, string $memberImage)
    {
        $this->memberName = $memberName;
        $this->memberPosition = $memberPosition;
        $this->memberImage = $memberImage;
    }

    /**
     * @return string
     */
    public function getMemberName(): string
    {
        return $this->memberName;
    }

    /**
     * @param string $memberName
     * @return $this
     */
    public function setMemberName(string $memberName)
    {
        $this->memberName = $memberName;
        return $this;
    }

    /**
     * @return string
     */
    public function getMemberPosition(): string
    {
        return $this->memberPosition;
    }

    /**
     * @param string $memberPosition
     * @return $this
     */
    public function setMemberPosition(string $memberPosition)
    {
        $this->memberPosition = $memberPosition;
        return $this;
    }

    /**
     * @return string
     */
    public function getMemberImage(): string
    {
        return $this->memberImage;
    }

    public function getAbsoluteImagePath(){

        return self::IMAGE_FOLDER.$this->getMemberImage();
    }

    /**
     * @return bool
     */
    public function imageFileExist() : bool
    {
        return File::_fileExist($this->getAbsoluteImagePath());
    }

    /**
     * @param string $memberImage
     * @return $this
     */
    public function setMemberImage(string $memberImage)
    {
        $this->memberImage = $memberImage;
        return $this;
    }




}