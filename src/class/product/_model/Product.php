<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 09-03-20
 * Time: 22:52
 */
namespace salesteck\product;



use salesteck\_interface\ArrayUnique;
use salesteck\_interface\JsonToClass;
use salesteck\Db\Db;
use salesteck\utils\Array_Helper;
use salesteck\utils\File;

class Product implements JsonToClass, ArrayUnique
{
    protected $idCode, $categoryIdCode, $price, $name, $description, $allergen, $imageWebPath, $productOptions;

    /**
     * Product constructor.
     * @param string $idCode
     * @param string $categoryIdCode
     * @param int $price
     * @param string $name
     * @param string $description
     * @param string $allergen
     * @param mixed $imageAbsolutePath
     * @param string $productOptions
     */
    public function __construct(
        string $idCode, string $categoryIdCode, int $price, string $name,
        string $description, string $allergen, $imageAbsolutePath, string $productOptions)
    {
        $this->idCode = $idCode;
        $this->categoryIdCode = $categoryIdCode;
        $this->price = intval($price);
        $this->name = $name;
        $this->description = $description;
        $this->allergen = explode(Db::ARRAY_DELIMITER, $allergen);
        $imageAbsolutePath = is_string($imageAbsolutePath) ?  $imageAbsolutePath : "";
        $image = "";
        if(File::_fileExist($imageAbsolutePath)){
            $image = $imageAbsolutePath;
        }
        $this->imageWebPath = $image;
        $this->productOptions = $productOptions;
    }

    public static function _inst($row) : ? self
    {

        if(
            $row !== null && is_array($row) &&
            array_key_exists(Product_C::_col_id_code, $row) &&
            array_key_exists(Product_C::_col_category_id, $row) &&
            array_key_exists(Product_C::_col_price, $row) &&
            array_key_exists(Product_C::_col_name, $row) &&
            array_key_exists(Product_C::_col_description, $row) &&
            array_key_exists(Product_C::_col_allergen, $row) &&
            array_key_exists(Product_C::_col_option, $row)
        ){
            $webPath = Array_Helper::_getArrayValue($row,Product_C::_col_web_path, "");
            return new self(
                $row[Product_C::_col_id_code],
                $row[Product_C::_col_category_id],
                $row[Product_C::_col_price],
                $row[Product_C::_col_name],
                $row[Product_C::_col_description],
                $row[Product_C::_col_allergen],
                $webPath,
                $row[Product_C::_col_option]
            );
        }else{
            return null;
        }
    }


    public function fromArray(array $array){
        foreach($array as $key => $value){
            $this->{$key} = $value;
        }
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
    private function getCategoryIdCode(): string
    {
        return $this->categoryIdCode;
    }

    public function getCategoryName(string $language) : string
    {
        $name = "";
        $category = ProductCategory_C::_getCategoryByIdCode($this->getCategoryIdCode(), $language);
        if($category instanceof ProductCategory){
            $name = $category->getName();
        }
        return $name;
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
    public function getPriceDec(): string
    {
        return number_format((float)$this->getPrice()/100, 2, '.', '');
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
     * @return array
     */
    public function getAllergen(): array
    {
        return $this->allergen;
    }

    /**
     * @return string
     */
    public function getImageWebPath()
    {
        return $this->imageWebPath;
    }

    /**
     * @param null|string $imageWebPath
     */
    public function setImageWebPath($imageWebPath)
    {
        $this->imageWebPath = $imageWebPath;
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

    public function __toString() : string
    {
        return json_encode($this->jsonSerialize());
    }

    public function jsonToClass(array $json)
    {
        foreach($json as $key => $value){
            $this->{$key} = $value;
        }
    }
}