<?php
namespace salesteck\order;

use salesteck\_interface\ArrayUnique;
use salesteck\_interface\JsonToClass;
use salesteck\_base\Language_C;
use salesteck\merchant\Merchant_C;
use salesteck\product\Product;
use salesteck\product\Product_C;
use salesteck\utils\String_Helper;


/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-05-20
 * Time: 14:16
 */

class OrderElement implements JsonToClass, ArrayUnique
{
    public static function _inst($row) : ? self
    {
        if(is_array($row)){
            if(
                is_array($row) &&
                array_key_exists(OrderElement_C::_col_id, $row) &&
                array_key_exists(OrderElement_C::_col_order_id_code, $row) &&
                array_key_exists(Merchant_C::_col_merchant_id_code, $row) &&
                array_key_exists(OrderElement_C::_col_product_id_code, $row) &&
                array_key_exists(OrderElement_C::_col_name, $row) &&
                array_key_exists(OrderElement_C::_col_price, $row) &&
                array_key_exists(OrderElement_C::_col_qty, $row) &&
                array_key_exists(OrderElement_C::_col_language, $row) &&
                array_key_exists(OrderElement_C::_col_option, $row) &&
                array_key_exists(OrderElement_C::_col_option_total, $row)
            ){
                return new self(
                    $row[OrderElement_C::_col_id],
                    $row[OrderElement_C::_col_order_id_code],
                    $row[Merchant_C::_col_merchant_id_code],
                    $row[OrderElement_C::_col_product_id_code],
                    $row[OrderElement_C::_col_name],
                    $row[OrderElement_C::_col_price],
                    $row[OrderElement_C::_col_qty],
                    $row[OrderElement_C::_col_option],
                    $row[OrderElement_C::_col_option_total],
                    $row[OrderElement_C::_col_language]
                );
            }

        }
        return null;
    }


    protected $id, $orderIdCode, $merchantIdCode, $productIdCode, $name, $price, $qty, $options, $optionsTotal, $language;

    /**
     * OrderElement constructor.
     *
     * @param int    $id
     * @param string $orderIdCode
     * @param string $merchantIdCode
     * @param string $productIdCode
     * @param string $name
     * @param int    $price
     * @param int    $qty
     * @param string $options
     * @param int    $optionsTotal
     * @param string $language
     */
    public function __construct(
        int $id, $orderIdCode, string $merchantIdCode, string $productIdCode, string $name,
        int $price, int $qty, string $options, int $optionsTotal, string $language
    )
    {
        $language = Language_C::_getValidLanguage($language);
        $product = Product_C::_getProductByIdCode($productIdCode, $language);
        $this->id = $id;
        $this->orderIdCode = $orderIdCode;
        $this->productIdCode = $productIdCode;
        $this->merchantIdCode = $merchantIdCode;
        $this->language = $language;

        if($product !== null && $product instanceof Product){
            $name = $product->getName();
        }
        $this->name = $name;
        $this->price = $price;
        $this->qty = $qty;
        $this->options = $options;
        $this->optionsTotal = $optionsTotal;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getOrderIdCode(): string
    {
        return $this->orderIdCode;
    }

    /**
     * @return string
     */
    public function getProductIdCode(): string
    {
        return $this->productIdCode;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getOptions(): string
    {
        return $this->options;
    }

    /**
     * @param string $options
     *
     * @return OrderElement
     */
    public function setOptions(string $options): OrderElement
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return int
     */
    public function getOptionsTotal(): int
    {
        return $this->optionsTotal;
    }

    /**
     * @param int $optionsTotal
     *
     * @return OrderElement
     */
    public function setOptionsTotal(int $optionsTotal): OrderElement
    {
        $this->optionsTotal = $optionsTotal;
        return $this;
    }

    public function getUnitPrice(){
        return $this->getPrice() + $this->getOptionsTotal();
    }


    public function getDisplayedName(){
        $name = $this->name;
        $options = $this->getDisplayedOption();
        return "$name $options";
    }

    public function getDisplayedOption(){
        $options = $this->options;
        $options = trim($options);
        if (String_Helper::_isStringNotEmpty($options)){
            $options = "+($options)";
        }
        return $options;
    }


    public function getOptionPriceString(){
        return self::_intToPrice($this->getOptionsTotal());
    }

    public function getPriceString(){
        return self::_intToPrice($this->getUnitPrice());
    }

    public function getTotal(){
        return $this->getUnitPrice() * $this->getQty();
    }

    public function getTotalString(){
        return self::_intToPrice($this->getTotal());
    }

    /**
     * @return int
     */
    public function getQty(): int
    {
        return $this->qty;
    }


    public function jsonToClass(array $json)
    {
        foreach($json as $key => $value){
            $this->{$key} = $value;
        }
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

    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }

    private static function _intToPrice(int $value, string $currency = "€"){
        return number_format((float)$value/100, 2, '.', '') . " $currency";
    }
}