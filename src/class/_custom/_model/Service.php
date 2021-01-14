<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 17-10-19
 * Time: 08:32
 */

namespace salesteck\custom;



class Service
{
    private $id, $idCode, $language, $label, $title, $description;

    /**
     * Service constructor.
     * @param string $id
     * @param string $idCode
     * @param string $language
     * @param string $label
     * @param string $title
     * @param string $description
     */
    public function __construct(string $id, string $idCode, string $language, string $label, string $title, string $description)
    {
        $this->id = $id;
        $this->idCode = $idCode;
        $this->language = $language;
        $this->label = $label;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
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
     */
    public function setIdCode(string $idCode)
    {
        $this->idCode = $idCode;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
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