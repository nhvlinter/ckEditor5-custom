<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 09-03-20
 * Time: 22:59
 */
namespace salesteck\product;

use JsonSerializable;

class ProductCategory implements JsonSerializable
{
    protected $idCode, $name, $description, $_name, $_value;




    public static function _inst(array $row)
    {
        if(
            $row !== null && is_array($row) &&
            array_key_exists(ProductCategory_C::_col_id_code, $row) &&
            array_key_exists(ProductCategory_C::_col_name, $row) &&
            array_key_exists(ProductCategory_C::_col_description, $row)
        ){
            return new ProductCategory(
                $row[ProductCategory_C::_col_id_code],
                $row[ProductCategory_C::_col_name],
                $row[ProductCategory_C::_col_description]
            );
        }else{
            return null;
        }
    }

    /**
     * ProductCategory constructor.
     * @param string $idCode
     * @param string $name
     * @param string $description
     */
    public function __construct(string $idCode, string $name, string $description)
    {
        $this->idCode = $idCode;
        $this->name = $name;
        $this->description = $description;
        $this->_value = $idCode;
        $this->_name = $name;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
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