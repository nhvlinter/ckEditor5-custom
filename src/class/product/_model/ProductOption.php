<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 21-11-20
 * Time: 18:11
 */

namespace salesteck\product;
use salesteck\_interface\ArrayUnique;


/**
 * Class ProductOption
 * @package salesteck\product
 */
class ProductOption implements \JsonSerializable, ArrayUnique
{

    public static function _inst($row){

        if(
            is_array($row) &&
            array_key_exists(ProductOption_C::_col_id_code, $row) &&
            array_key_exists(ProductOption_C::_col_category_id, $row) &&
            array_key_exists(ProductOption_C::_col_price, $row)&&

            array_key_exists(ProductOption_C::_col_language, $row) &&
            array_key_exists(ProductOption_C::_col_name, $row) &&
            array_key_exists(ProductOption_C::_col_description, $row)
        ){
            return new self(
                $row[ProductOption_C::_col_id_code], $row[ProductOption_C::_col_category_id], $row[ProductOption_C::_col_price],
                $row[ProductOption_C::_col_language], $row[ProductOption_C::_col_name], $row[ProductOption_C::_col_description]

            );
        }
        return null;
    }

    /**
     * @var string $idCode
     *
     * @var string $category
     *
     * @var integer $price
     *
     * @var string $language
     *
     * @var string $name
     *
     * @var string $description
     */
    protected $idCode, $category, $price, $language, $name, $description;

    /**
     * ProductOption constructor.
     *
     * @param string $idCode
     * @param string $category
     * @param        $price
     * @param string $language
     * @param string $name
     * @param string $description
     */
    public function __construct(string $idCode, string $category, $price, string $language, string $name, string $description)
    {
        $this->idCode = $idCode;
        $this->category = $category;
        $this->price = abs(intval($price));
        $this->language = $language;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getIdCode()
    {
        return $this->idCode;
    }

    /**
     * @param string $idCode
     *
     * @return $this
     */
    public function setIdCode( string $idCode) : self
    {
        $this->idCode = $idCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory() : string
    {
        return $this->category;
    }

    /**
     * @param string $category
     *
     * @return $this
     */
    public function setCategory(string $category) : self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPrice() : int
    {
        return $this->price;
    }

    /**
     * @param int $price
     *
     * @return $this
     */
    public function setPrice(int $price) : self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage() : string
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return ProductOption
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return ProductOption
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }




    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * Specify data which should be serialized to JSON
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}