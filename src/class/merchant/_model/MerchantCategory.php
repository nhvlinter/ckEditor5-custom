<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 16-11-20
 * Time: 22:57
 */

namespace salesteck\merchant;


class MerchantCategory implements \JsonSerializable
{

    public static function _inst($row) : ? self
    {
        if(
            is_array($row) &&
            array_key_exists(MerchantCategory_C::_col_id_code, $row) &&
            array_key_exists(MerchantCategory_C::_col_language, $row) &&
            array_key_exists(MerchantCategory_C::_col_name, $row) &&
            array_key_exists(MerchantCategory_C::_col_description, $row) &&
            array_key_exists(MerchantCategory_C::_col_category_parent, $row)&&
            array_key_exists(MerchantCategory_C::_col_tree, $row)
        ){
            return new self(
                $row[MerchantCategory_C::_col_id_code],
                $row[MerchantCategory_C::_col_language],
                $row[MerchantCategory_C::_col_name],
                $row[MerchantCategory_C::_col_description],
                $row[MerchantCategory_C::_col_category_parent],
                $row[MerchantCategory_C::_col_tree]
            );
        }
        return null;
    }

    private $idCode, $language, $name, $description, $categoryParent, $categoryTree;

    /**
     * MerchantCategory constructor.
     * @param string $idCode
     * @param string $language
     * @param string $name
     * @param string $description
     * @param string $categoryParent
     * @param string $categoryTree
     */
    public function __construct(string $idCode, string $language, string $name, string $description, string $categoryParent, string $categoryTree)
    {
        $this->idCode = $idCode;
        $this->language = $language;
        $this->name = $name;
        $this->description = $description;
        $this->categoryParent = $categoryParent;
        $this->categoryTree = $categoryTree;
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
    public function setIdCode(string $idCode) : self
    {
        $this->idCode = $idCode;
        return $this;
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
     * @return $this
     */
    public function setLanguage(string $language) : self
    {
        $this->language = $language;
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
    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
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
     * @return $this
     */
    public function setDescription(string $description) : self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategoryParent(): string
    {
        return $this->categoryParent;
    }

    /**
     * @param string $categoryParent
     * @return $this
     */
    public function setCategoryParent(string $categoryParent) : self
    {
        $this->categoryParent = $categoryParent;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategoryTree(): string
    {
        return $this->categoryTree;
    }

    /**
     * @param string $categoryTree
     * @return MerchantCategory
     */
    public function setCategoryTree(string $categoryTree) : self
    {
        $this->categoryTree = $categoryTree;
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