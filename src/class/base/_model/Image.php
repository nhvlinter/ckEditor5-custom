<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 21-04-20
 * Time: 17:11
 */

namespace salesteck\_base;

class Image implements \JsonSerializable
{
    private $idCode, $title, $description, $absolutePath;

    /**
     * Image constructor.
     * @param string $idCode
     * @param string $title
     * @param string $description
     * @param string $absolutePath
     */
    public function __construct(string $idCode, string $title, string $description, string $absolutePath)
    {
        $this->idCode = $idCode;
        $this->title = $title;
        $this->description = $description;
        $this->absolutePath = $absolutePath;
    }

    /**
     * @return string
     */
    public function getIdCode(): string
    {
        return $this->idCode;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getAbsolutePath(): string
    {
        return $this->absolutePath;
    }

    public function printTitle(){
        echo htmlspecialchars($this->getTitle());
    }

    public function printDescription(){
        echo htmlspecialchars($this->getDescription());
    }

    public function printAbsolutePath(){
        echo htmlspecialchars($this->getAbsolutePath());
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