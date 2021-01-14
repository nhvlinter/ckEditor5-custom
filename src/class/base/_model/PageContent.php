<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 14-11-19
 * Time: 16:58
 */

namespace salesteck\_base;


class PageContent implements \JsonSerializable
{
    private $id, $fileAbsolutePath, $pageIdCode;

    /**
     * PageContent constructor.
     * @param string $id
     * @param string $fileAbsolutePath
     * @param string $pageIdCode
     */
    public function __construct(string $id, string $fileAbsolutePath, string $pageIdCode)
    {
        $this->id = $id;
        $this->fileAbsolutePath = $fileAbsolutePath;
        $this->pageIdCode = $pageIdCode;
    }

    /**
     * @return mixed
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFileAbsolutePath() : string
    {
        return $this->fileAbsolutePath;
    }

    /**
     * @return mixed
     */
    public function getPageIdCode() : string
    {
        return $this->pageIdCode;
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