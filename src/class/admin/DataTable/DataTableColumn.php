<?php

namespace salesteck\DataTable;
use JsonSerializable;
use salesteck\admin\AdminI18_C;
use salesteck\Db\Db;


/**
 * Created by PhpStorm.
 * User: Son
 * Date: 25-11-19
 * Time: 16:21
 */

class DataTableColumn implements JsonSerializable
{
    private const data = "data-";

    public const
        orderAsc = "asc",
        orderDesc = "desc"
    ;

    public const
        attrClassName =     self::data."className",
        attrOrderDataType = self::data."orderDataType",
        attrCreate =        self::data."create",
        attrDefContent =    self::data."content",
        attrDefValue =      self::data."def",
        attrEdit =          self::data."edit",
        attrAdd =          self::data."add",
        attrOptions =       self::data."options",
        attrOrderable =     self::data."orderable",
        attrOrderSequence = self::data."orderSequence",
        attrSearchable =    self::data."searchable",
        attrImageSrc =      self::data."imagesrc",
        attrType =          self::data."type",
        attrVisible =       self::data."visible",
        attrWidth =         self::data."width"
    ;

    private const arrayAttr = [
        self::attrClassName,
        self::attrOrderDataType,
        self::attrCreate,
        self::attrDefValue,
        self::attrEdit,
        self::attrOptions,
        self::attrOrderable,
        self::attrOrderSequence,
        self::attrSearchable,
        self::attrImageSrc,
        self::attrType,
        self::attrVisible,
        self::attrWidth
    ];

    public const
        type_boolean = "boolean",
        type_checkBox = "checkbox",
        type_color = "color",
        type_date = "date",
        type_datetime = "datetime",
        type_daterange = "daterange",
        type_email = "email",
        type_flag = "flag",
        type_float = "float",
        type_icon = "icon",
        type_image = "image",
        type_integer = "integer",
        type_password = "password",
        type_price = "price",
        type_select = "select",
        type_text = "text",
        type_textarea = "textarea",
        type_time = "time",
        type_time_step = "timestep",
        type_upload = "upload",
        type_upload_many = "uploadMany"
    ;

    public const arrayType = [
        self::type_boolean,
        self::type_checkBox,
        self::type_color,
        self::type_date,
        self::type_daterange,
        self::type_datetime,
        self::type_email,
        self::type_flag,
        self::type_float,
        self::type_icon,
        self::type_image,
        self::type_integer,
        self::type_password,
        self::type_price,
        self::type_select,
        self::type_text,
        self::type_textarea,
        self::type_time,
        self::type_time_step,
        self::type_upload,
        self::type_upload_many
    ];

    private $columnName, $title, $class, $attributes;

    /**
     * DataTableColumn constructor.
     * @param string $columnName
     * @param string $title
     * @param string $class
     * @param array $attributes
     */
    private function __construct(string $columnName, string $title = "", string $class = "", array $attributes = [])
    {
        $this->columnName = $columnName;
        $this->title = $title;
        $this->class = $class;
        $this->attributes = [];
        $this->add(true)->edit(true)->visible(true)->searchable(true)->orderable(true);
        foreach ($attributes as $key => $value){
            $this->addAttribute($key, $value);
        }
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes) : self
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return string | null
     */
    public function getColumnName(): ? string
    {
        return $this->columnName;
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
     * @return $this
     */
    public function setTitle(string $title) :self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function setClass(string $class) : self
    {
        $this->class = $class;
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


    /**
     * @param string $attrName
     * @param $attrValue
     * @return $this
     */
    public function addAttribute(string $attrName, $attrValue) : self
    {
        $attributes = $this->getAttributes();
        if(in_array($attrName, self::arrayAttr)){
            $attributes[$attrName] = $attrValue;
            if($attrName === self::attrType && $attrValue === gettype(true)){
                $attributes[self::attrSearchable] = false;
            }
        }
        return $this->setAttributes($attributes);
    }


    public function add(bool $add) : self
    {
        return $this->addAttribute(self::attrCreate, $add);
    }

    public function edit(bool $editable) : self
    {
        return $this->addAttribute(self::attrEdit, $editable);
    }

    public function visible(bool $visible) : self
    {
        return $this->addAttribute(self::attrVisible, $visible);
    }

    public function orderable(bool $orderable) : self
    {
        return $this->addAttribute(self::attrOrderable, $orderable);
    }

    public function searchable(bool $searchable) : self
    {
        return $this->addAttribute(self::attrSearchable, $searchable);
    }



    public function type(string $type, $options = null, $defValue = "") : self
    {
        if(in_array($type, self::arrayType)){
            if($type === gettype(true)){
                $this->searchable(false);
            }
            if($options !== null){
                $this->options($options);
            }
            if ($defValue !== "" && gettype($defValue) === gettype("")){

                $this->defValue($defValue);
            }
            return $this->addAttribute(self::attrType, $type);
        }
        return $this;
    }

    private function options($options) : self
    {
        return $this->addAttribute(self::attrOptions, $options);
    }

    private function defValue(string $defValue) : self
    {
        return $this->addAttribute(self::attrDefValue, $defValue);
    }

    public function defContent(string $defContent) : self
    {
        return $this->addAttribute(self::attrDefContent, $defContent);
    }

    public function addClass(string $class) : self
    {
        $className = $this->getClass();
        $className = $className !== "" ? "$className $class" : $class;
        return $this->setClass($className);
    }



    public function getHtml(array $i18) : string
    {
        $html = "";
        $data = strval($this->getColumnName());
        $title = strval($this->getTitle());
        $class = ($this->getClass());

        if($title !== ""){
            $title = htmlspecialchars(AdminI18_C::_getValueFromKey($title, $i18), ENT_QUOTES);
            $title = ucfirst($title);
            $title = " data-title='$title'";
        }
        $class = $class !== "" ? " data-classname='$class'" : "";
        $attributes = $this->getAttributesString();


        if($data !== ""){
            $html = "<th
                        style='width=\"100%\"'
                        data-column='$data'
                        $title 
                        $class 
                        $attributes
                    ></th>";
        }


        return $html;
    }

    private function getAttributesString() : string
    {
        $attributes = $this->getAttributes();
        $attributesString = " ";
        foreach ($attributes as $key => $value){
            if(in_array($key, self::arrayAttr)){
                $attrValueString = self::_attrValueToString($value);
                $attributesString .= ("$key='$attrValueString' ");
            }
        }
        return $attributesString;

    }

    public static function _inst(string $columnName, string $title = "", string $class = "", array $attributes = []){
        return new self($columnName, $title, $class, $attributes);
    }

    private static function _attrValueToString($attr) : string
    {
        switch (gettype($attr)){
            case gettype(true) :
                return $attr ? "true" : "false";
            case gettype([]) :
                return str_replace("'", "\'", json_encode($attr))/*json_encode($attr)*/;
            default :
                return $attr;
        }
    }


    public static function _getColumnType(string $_col){
        switch ($_col){
            case Db::_col_is_enable :
                return self::type_boolean;
            case Db::_col_is_default :
                return self::type_boolean;
            case Db::_col_is_display :
                return self::type_boolean;
            case Db::_col_is_editable :
                return self::type_boolean;
            case Db::_col_password :
                return self::type_password;
            case Db::_col_color :
                return self::type_color;
            case Db::_col_description :
                return self::type_textarea;
            case Db::_col_keywords :
                return self::type_textarea;


            case Db::_col_create_date :
                return self::type_date;
            case Db::_col_last_modified :
                return self::type_date;




            default :
                return "";
        }
    }
}