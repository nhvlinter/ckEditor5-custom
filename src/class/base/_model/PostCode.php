<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 01-11-20
 * Time: 22:03
 */

namespace salesteck\_base;


class PostCode implements \JsonSerializable
{

    private $id, $postCode, $provinceCode, $regionCode, $name, $provinceName, $regionName, $language, $text;


    public static function _inst(array $row) : ? self
    {
        if(
            array_key_exists(PostCode_C::_col_post_code_id, $row) &&
            array_key_exists(PostCode_C::_col_post_code, $row) &&
            array_key_exists(PostCode_C::_col_province_code, $row) &&
            array_key_exists(PostCode_C::_col_region_code, $row) &&
            array_key_exists(PostCode_C::_col_name, $row) &&
            array_key_exists(PostCode_C::_col_province_name, $row) &&
            array_key_exists(PostCode_C::_col_region_name, $row) &&
            array_key_exists(PostCode_C::_col_language, $row)
        ){
            return new self(
                $row[PostCode_C::_col_post_code_id],
                $row[PostCode_C::_col_post_code],
                $row[PostCode_C::_col_province_code],
                $row[PostCode_C::_col_region_code],
                $row[PostCode_C::_col_name],
                $row[PostCode_C::_col_province_name],
                $row[PostCode_C::_col_region_name],
                $row[PostCode_C::_col_language]
            );
        }
        return null;
    }
    /**
     * PostCode constructor.
     * @param $id
     * @param $postCode
     * @param $provinceCode
     * @param $regionCode
     * @param $name
     * @param $provinceName
     * @param $regionName
     * @param $language
     */
    private function __construct($id, $postCode, $provinceCode, $regionCode, $name, $provinceName, $regionName, $language)
    {
        $this->id = $id;
        $this->postCode = $postCode;
        $this->provinceCode = $provinceCode;
        $this->regionCode = $regionCode;
        $this->name = $name;
        $this->provinceName = $provinceName;
        $this->regionName = $regionName;
        $this->language = $language;
        $this->text = "$name - ($postCode)";
    }




    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @param mixed $postCode
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;
    }

    /**
     * @return mixed
     */
    public function getProvinceCode()
    {
        return $this->provinceCode;
    }

    /**
     * @param mixed $provinceCode
     */
    public function setProvinceCode($provinceCode)
    {
        $this->provinceCode = $provinceCode;
    }

    /**
     * @return mixed
     */
    public function getRegionCode()
    {
        return $this->regionCode;
    }

    /**
     * @param mixed $regionCode
     */
    public function setRegionCode($regionCode)
    {
        $this->regionCode = $regionCode;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getProvinceName()
    {
        return $this->provinceName;
    }

    /**
     * @param mixed $provinceName
     */
    public function setProvinceName($provinceName)
    {
        $this->provinceName = $provinceName;
    }

    /**
     * @return mixed
     */
    public function getRegionName()
    {
        return $this->regionName;
    }

    /**
     * @param mixed $regionName
     */
    public function setRegionName($regionName)
    {
        $this->regionName = $regionName;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text)
    {
        $this->text = $text;
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