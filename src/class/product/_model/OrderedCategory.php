<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 26-10-20
 * Time: 17:43
 */

namespace salesteck\product;


class OrderedCategory extends ProductCategory implements \JsonSerializable
{
    private $order ;



    public static function _inst(array $row)
    {
        if(
            $row !== null && is_array($row) &&
            array_key_exists(ProductCategory_C::_col_id_code, $row) &&
            array_key_exists(ProductCategory_C::_col_name, $row) &&
            array_key_exists(ProductCategory_C::_col_description, $row) &&
            array_key_exists(ProductCategory_C::_col_order, $row)
        ){
            return new self(
                $row[ProductCategory_C::_col_id_code],
                $row[ProductCategory_C::_col_name],
                $row[ProductCategory_C::_col_description],
                $row[ProductCategory_C::_col_order]
            );
        }else{
            return null;
        }
    }
    /**
     * OrderedCategory constructor.
     * @param string $idCode
     * @param string $name
     * @param string $description
     * @param string $order
     */
    public function __construct(string $idCode, string $name, string $description, string $order){

        parent::__construct($idCode, $name, $description);
        $this->order = intval($order);
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @param string $order
     */
    public function setOrder(string $order)
    {
        $this->order = $order;
    }





    public function jsonSerialize()
    {
        return get_object_vars($this);
    }


}