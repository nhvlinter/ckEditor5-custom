<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 24-10-19
 * Time: 15:08
 */

namespace Content;


class Image
{
    public const
        IMAGE_FOLDER_PATH = "/upload/image/",
        LOGO_PATH = self::IMAGE_FOLDER_PATH."logo-light.png"
    ;


    private $imagePath, $imageDescription;

    /**
     * Image constructor.
     * @param $imagePath
     * @param $imageDescription
     */
    public function __construct($imagePath, $imageDescription)
    {
        $this->imagePath = $imagePath;
        $this->imageDescription = $imageDescription;
    }

    /**
     * @return mixed
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * @param mixed $imagePath
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;
    }

    /**
     * @return mixed
     */
    public function getImageDescription()
    {
        return $this->imageDescription;
    }

    /**
     * @param mixed $imageDescription
     */
    public function setImageDescription($imageDescription)
    {
        $this->imageDescription = $imageDescription;
    }





}