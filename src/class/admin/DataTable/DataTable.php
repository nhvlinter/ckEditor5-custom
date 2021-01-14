<?php

namespace salesteck\DataTable;

use JsonSerializable;
use salesteck\admin\AdminI18_C;

/**
 * Created by PhpStorm.
 * User: Son
 * Date: 25-11-19
 * Time: 16:17
 */

class DataTable implements JsonSerializable
{
    public const CONFIG_FILE = "/vendor/datatables.net/editor-php/DataTables.php";

    private const
        _class = "table table-bordered  shadow",
        _data = "data-"
    ;

    private const editDef = 0, editInline = 1, editBubble = 2, editType = [self::editDef, self::editInline, self::editBubble];


    public const
        attrAdd =           self::_data."create",
        attrEdit =          self::_data."edit",
        attrEditType =      self::_data."edittype",
        attrRemove =        self::_data."remove",
        attrPrint =         self::_data."print",
        attrImageSrc =      self::_data."imagesrc",
        attrExcel =         self::_data."excel",
        attrPdf =           self::_data."pdf",
        attrOrderable =     self::_data."orderable",
        attrSearchable =    self::_data."searchable",
        attrFilterHead =    self::_data."filterHead",
        attrRowReOrder =    self::_data."rowreorder",
        attrControlColumn =    self::_data."controlcolumn",
        attrWidth =    self::_data."width",
        _index_language = "_lang"
    ;

    private const validAttr = [
        self::attrAdd,
        self::attrEdit,
        self::attrRemove,
        self::attrPrint,
        self::attrImageSrc,
        self::attrExcel,
        self::attrPdf,
        self::attrSearchable,
        self::attrOrderable,
        self::attrFilterHead,
        self::attrRowReOrder,
        self::attrControlColumn
    ];

    /**
     * @var string $id
     * @var string $table
     * @var string $language
     * @var string $title
     * @var bool $editType
     * @var string $idSrc
     * @var string $class
     * @var array $arrayColumn
     * @var array $arrayAttributes
     */
    private $id, $table, $language, $title, $editType, $idSrc, $rowReorder, $class, $arrayColumn, $arrayAttributes;

    /**
     * DataTable constructor.
     * @param string $table
     * @param string $language
     * @param string $title
     * @param string $idSrc
     * @param string $class
     */
    public function __construct(string $table = "", string $language = "", string $title = "", string $idSrc = "", string $class = "")
    {
        $this->id = "dataTable";
        $this->title = $title;
        $this->language = $language;
        $this->table = $table;
        $this->idSrc = $idSrc;
        $this->arrayColumn = [];
        $this->arrayAttributes = [];
        $this
            ->create(true)
            ->edit(true)
            ->remove(true)
            ->orderable(true)
            ->print(false)
            ->excel(false)
            ->pdf(false)
            ->filterHead(false)
            ->imageSrc("")
            ->setEditType(self::editDef)
            ->setClass(self::_class . $class)
            ->filterHead(true)
            ->controlColumn(true)
        ;
    }

    /**
     * @return string
     */
    private function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    private function setId(string $id) : self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    private function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     * @return $this
     */
    private function setTable(string $table) : self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return string
     */
    private function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return $this
     */
    private function setLanguage(string $language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return string
     */
    private function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    private function setTitle(string $title) : self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    private function getIdSrc(): string
    {
        return $this->idSrc;
    }

    /**
     * @param string $idSrc
     * @return $this
     */
    private function setIdSrc(string $idSrc) : self
    {
        $this->idSrc = $idSrc;
        return $this;
    }

    /**
     * @return int
     */
    public function getEditType(): int
    {
        return $this->editType;
    }

    /**
     * @param int $editType
     * @return $this
     */
    public function setEditType(int $editType) : self
    {
        if(in_array($editType, self::editType)){
            $this->editType = $editType;
        }
        return $this;
    }



    /**
     * @return string
     */
    private function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return $this
     */
    private function setClass(string $class) : self
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return array
     */
    private function getArrayColumn(): array
    {
        return $this->arrayColumn;
    }

    /**
     * @param array $arrayColumn
     * @return $this
     */
    private function setArrayColumn(array $arrayColumn) : self
    {
        $this->arrayColumn = $arrayColumn;
        return $this;
    }

    /**
     * @return array
     */
    private function getAttributes(): array
    {
        return $this->arrayAttributes;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    private function setAttributes(array $attributes) : self
    {
        $this->arrayAttributes = $attributes;
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
     * @param string $id
     * @return DataTable
     */
    public function id(string $id) : self
    {
        return $this->setId($id);
    }

    /**
     * @param string $table
     * @return DataTable
     */
    public function table(string $table) : self
    {
        return $this->setTable($table);
    }

    /**
     * @param string $title
     * @return DataTable
     */
    public function title(string $title) : self
    {
        return $this->setTitle($title);
    }

    /**
     * @param string $lang
     * @return DataTable
     */
    public function lang(string $lang) : self
    {
        return $this->setLanguage($lang);
    }

    /**
     * @param string $class
     * @return DataTable
     */
    public function class(string $class) : self
    {
        $actualClass = $this->getClass();
        return $this->setClass($actualClass . " $class");
    }

    /**
     * @param string $idSrc
     * @return DataTable
     */
    public function idSrc(string $idSrc) : self
    {
        return $this->setIdSrc($idSrc);
    }





    /**
     * @param string $attrName
     * @param $attrValue
     * @return $this
     */
    private function addAttribute(string $attrName, $attrValue) : self
    {
        $attributes = $this->getAttributes();
        if(in_array($attrName, self::validAttr)){
            $attributes[$attrName] = $attrValue;
        }
        return $this->setAttributes($attributes);
    }


    /**
     * @param bool $create
     * @return DataTable
     */
    public function create(bool $create) : self
    {
        return $this->addAttribute(self::attrAdd, $create);
    }

    /**
     * @param bool $editable
     * @return DataTable
     */
    public function edit(bool $editable) : self
    {
        return $this->addAttribute(self::attrEdit, $editable);
    }

    /**
     * @param bool $remove
     * @return DataTable
     */
    public function remove(bool $remove) : self
    {
        return $this->addAttribute(self::attrRemove, $remove);
    }

    /**
     * @param bool $print
     * @return DataTable
     */
    public function print(bool $print) : self
    {
        return $this->addAttribute(self::attrPrint, $print);
    }

    /**
     * @param bool $pdf
     * @return DataTable
     */
    public function pdf(bool $pdf) : self
    {
        return $this->addAttribute(self::attrPdf, $pdf);
    }

    /**
     * @param bool $excel
     * @return DataTable
     */
    public function excel(bool $excel) : self
    {
        return $this->addAttribute(self::attrExcel, $excel);
    }

    /**
     * @param bool $orderable
     * @return DataTable
     */
    public function orderable(bool $orderable) : self
    {
        return $this->addAttribute(self::attrOrderable, $orderable);
    }

    /**
     * @param bool $searchable
     * @return DataTable
     */
    public function searchable(bool $searchable) : self
    {
        return $this->addAttribute(self::attrSearchable, $searchable);
    }

    /**
     * @return DataTable
     */
    public function inlineEdit() : self
    {
        return $this->setEditType(self::editInline);
    }

    /**
     * @return DataTable
     */
    public function bubbleEdit() : self
    {
        return $this->setEditType(self::editBubble);
    }

    /**
     * @return DataTable
     */
    public function defaultEdit() : self
    {
        return $this->setEditType(self::editDef);
    }

    /**
     * @param bool $filterHead
     * @return DataTable
     */
    public function filterHead(bool $filterHead) : self
    {
        return $this->addAttribute(self::attrFilterHead, $filterHead);
    }

    /**
     * @param string $imageSrc
     * @return DataTable
     */
    public function imageSrc(string $imageSrc) : self
    {
        return $this->addAttribute(self::attrImageSrc, $imageSrc);
    }

    /**
     * @param bool $controlColumn
     * @return DataTable
     */
    public function controlColumn(bool $controlColumn = true){
        return $this->addAttribute(self::attrControlColumn, $controlColumn);
    }


    /**
     * @param DataTableColumn $column
     * @return DataTable
     */
    private function addColumn(DataTableColumn $column){
        $arrayColumn = $this->getArrayColumn();
        if(sizeof($arrayColumn)>0){
            $found = false;
            foreach ($arrayColumn as $columnElement){
                if( $column !== null && $column instanceof DataTableColumn && $columnElement !== null && $columnElement instanceof DataTableColumn){
                    if ($column->getColumnName() === $columnElement->getColumnName() && $column->getColumnName() !== gettype(null)){
                        $columnElement
                            ->setTitle( $column->getTitle() )
                            ->setAttributes($column->getAttributes() )
                            ->setClass($column->getClass())
                        ;
                        $found = true;
                    }
                }
            }
            if(!$found){
                array_push($arrayColumn, $column);
            }
        }else{
            array_push($arrayColumn, $column);
        }
        return $this->setArrayColumn($arrayColumn);

    }

    /**
     * @return DataTable
     */
    public function detailsColumn(){
        $column = DataTableColumn::_inst( gettype(null), "");
        $column
            ->add(false)
            ->edit(false)
            ->searchable(false)
            ->orderable(false)
            ->visible(true)
            ->addClass("details")
            ->defContent('<i class="far fa-edit edit text-confirm text-sm"></i><i class="far fa-trash-alt remove text-confirm text-sm"></i>')
        ;

        return $this->addColumn($column);
    }

    /**
     * @param string $columnData
     * @param string $title
     * @return DataTable
     * @internal param bool $controlColumn
     */
    public function rowReOrder(string $columnData = "", string $title = ""){
        $this->rowReorder = $columnData;
        $column = DataTableColumn::_inst( $columnData, $title);
        $column
            ->add(false)
            ->edit(false)
            ->searchable(false)
            ->orderable(true)
            ->visible(true)
            ->addClass("reorder")
            ->addAttribute(DataTableColumn::attrWidth, "50px");
        ;

        return $this->addColumn($column);
    }

    /**
     * @param $columnName
     * @param string $title
     * @param bool $edit
     * @param array $attributes
     * @return DataTable
     */
    public function column($columnName, string $title, bool $edit = true, array $attributes = []) : self
    {
        $column = DataTableColumn::_inst($columnName, $title);
        $column
            ->edit($edit)
        ;

        foreach ($attributes as $key => $value){
            $column->addAttribute($key, $value);
        }

        return $this->addColumn($column);
    }

    /**
     * @param array $arrayColumn
     * @return $this
     */
    public function columns(array $arrayColumn){
        foreach ($arrayColumn as $column){
            if($column instanceof DataTableColumn){
                $this->addColumn($column);
            }
        }
        return $this;
    }


    /**
     * @param array $i18
     * @return string
     */
    private function getDataTableHtml(array $i18) : string
    {
        $html = "";
        $table = $this->getTable();
        $tableId = $this->getId();
        $language = $this->getLanguage();
        $title = $this->getTitle();
        $idSrc = $this->getIdSrc();
        $editType = $this->getEditType();
        $class = $this->getClass();
        $rowReorder = $this->rowReorder;

        $idSrc = $idSrc !== "" ? " data-idSrc='$idSrc'" : "";
        $editType =  " data-edittype='$editType'";
        $class = $class !== "" ? " class='$class'" : "";
        $language = $language !== "" ? " data-lang='$language'" : "";
        $rowReorder = $rowReorder !== "" ? " ".self::attrRowReOrder."='$rowReorder'" : "";
        if($title !== ""){
            $title = htmlspecialchars(AdminI18_C::_getValueFromKey($title, $i18), ENT_QUOTES);
            $title = ucfirst($title);
            $title = (" data-title='$title'") ;
        }
        $columnHtml = $this->getColumnHtml($i18);
        $attributes = $this->getAttributesString();

        if($table !== "" && $tableId !== "" && $columnHtml !== ""){
            $html = "<div class='dt-btn-container m-b-10'></div>".
                    "<table
                        style='width: 100%'
                        id='$tableId'
                        data-table='$table'
                        $language
                        $title
                        $rowReorder
                        $editType
                        $idSrc
                        $attributes
                        $class>".
                        "<thead>".
                            "<tr>".
                                $columnHtml.
                            "</tr>".
                        "</thead>".
                    "</table>";
        }
        return $html;
    }

    /**
     * @param array $i18
     * @return string
     */
    private function getColumnHtml(array $i18) : string
    {
        $html = "";
        $arrayColumn = $this->getArrayColumn();
        foreach ($arrayColumn as $column){
            if ($column !== null && $column instanceof DataTableColumn){
                $html .= $column->getHtml($i18);
            }
        }


        return $html;
    }

    /**
     * @param array $i18
     * @return DataTable
     */
    public function printTable(array $i18) : self
    {
        echo $this->getDataTableHtml($i18);
        return $this;
    }


    /**
     * @return string
     */
    private function getAttributesString() : string
    {
        $attributes = $this->getAttributes();
        $attributesString = " ";
        foreach ($attributes as $key => $value){
            if(in_array($key, self::validAttr)){
                $attrValueString = self::_attrValueToString($value);
                $attributesString .= ("$key='$attrValueString' ");
            }
        }
        return $attributesString;

    }

    /**
     * @param $attr
     * @return string
     */
    private static function _attrValueToString($attr) : string
    {
        switch (gettype($attr)){
            case gettype(true) :
                return $attr ? "true" : "false";
            case gettype([]) :
                return json_encode($attr);
            default :
                return $attr;
        }
    }
}