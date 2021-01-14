<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 21-11-20
 * Time: 18:35
 */

namespace salesteck\product;


use salesteck\_interface\ArrayUnique;


/**
 * Class ProductOptionCategory
 * @package salesteck\product
 */
class ProductOptionCategory implements \JsonSerializable, ArrayUnique
{

    public static function _inst($row) : ? self
    {

        if(
            is_array($row) &&
            array_key_exists(ProductOptionCategory_C::_col_id_code, $row) &&
            array_key_exists(ProductOptionCategory_C::_col_language, $row) &&
            array_key_exists(ProductOptionCategory_C::_col_name, $row) &&
            array_key_exists(ProductOptionCategory_C::_col_description, $row) &&
            array_key_exists(ProductOptionCategory_C::_col_is_multiple, $row) &&
            array_key_exists(ProductOptionCategory_C::_col_max_qty, $row)
        ){
            return new self(
                $row[ProductOptionCategory_C::_col_id_code], $row[ProductOptionCategory_C::_col_language],
                $row[ProductOptionCategory_C::_col_name], $row[ProductOptionCategory_C::_col_description],
                $row[ProductOptionCategory_C::_col_is_multiple], $row[ProductOptionCategory_C::_col_max_qty]
            );
        }
        return null;
    }

    /**
     * @var string $idCode
     *
     * @var string $language
     *
     * @var string $name
     *
     * @var string $description
     *
     * @var bool $isMultiple
     *
     * @var int $maxQty
     *
     * @var array $options
     */
    private $idCode, $language, $name, $description, $isMultiple, $maxQty, $options;

    /**
     * ProductOptionCategory constructor.
     *
     * @param string $idCode
     * @param string $language
     * @param string $name
     * @param string $description
     * @param        $isMultiple
     * @param        $maxQty
     * @param array  $options
     */
    public function __construct(
        string $idCode, string $language, string $name, string $description, $isMultiple, $maxQty, array $options = []
    )
    {
        $this->idCode = $idCode;
        $this->language = $language;
        $this->name = $name;
        $this->description = $description;
        $this->isMultiple = boolval($isMultiple);
        $this->maxQty = intval($maxQty);
        $this->options = $options;
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
     * @return ProductOptionCategory
     */
    public function setIdCode(string $idCode) : self
    {
        $this->idCode = $idCode;
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
     * @return ProductOptionCategory
     */
    public function setName(string $name) : self
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
     * @return ProductOptionCategory
     */
    public function setDescription(string $description) : self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return ProductOptionCategory
     */
    public function setOptions(array $options) : self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isMultiple() : bool
    {
        return $this->isMultiple;
    }

    /**
     * @param mixed $isMultiple
     *
     * @return ProductOptionCategory
     */
    public function setMultiple(bool $isMultiple)
    {
        $this->isMultiple = $isMultiple;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxQty()
    {
        return $this->maxQty;
    }

    /**
     * @param mixed $maxQty
     *
     * @return ProductOptionCategory
     */
    public function setMaxQty($maxQty)
    {
        $this->maxQty = $maxQty;
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