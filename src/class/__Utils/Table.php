<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 18-12-19
 * Time: 15:00
 */

namespace salesteck\utils;
class Table
{
    private const _desc = "desc", _value = "value";


    private $arrayRow = [];

    public static function _inst(){
        return new self();
    }

    /**
     * Table constructor.
     */
    private function __construct()
    {

    }

    /**
     * @return array
     */
    private function getArrayRow(): array
    {
        return $this->arrayRow;
    }

    /**
     * @param array $arrayRow
     * @return Table
     */
    private function setArrayRow(array $arrayRow) : self
    {
        $this->arrayRow = $arrayRow;
        return $this;
    }


    public function row(string $description, string $value){
        $arrayRow = $this->getArrayRow();
        array_push($arrayRow, [
            self::_desc => $description,
            self::_value => $value
        ]);

        return $this->setArrayRow($arrayRow);
    }




    public function display(){
        $arrayRow = $this->getArrayRow();
        $rowHtml = "";
        foreach ($arrayRow as $row){
            $rowHtml .= self::_rowToHtml($row);
        }
        $html =
            "<table class='table responsive table-bordered elevation' width='100%'>".
                "<tbody>".
                    $rowHtml.
                "</tbody>".
            "</table>";

        echo $html;
    }

    public static function _rowToHtml(array $row) : string
    {
        if( array_key_exists(self::_desc, $row) && array_key_exists(self::_value, $row)){
            $desc = $row[self::_desc];
            $value = $row[self::_value];
            if($desc !== "" && $value !== ""){
                return
                    "<tr>".
                    "<td>".
                    $desc.
                    "</td>".
                    "<td>".
                    $value.
                    "</td>".
                    "</tr>";
            }

        }
        return "";
    }

    public static function _arrayToHtml(array $row) : string
    {
        $html = "";
        $rowHtml = "";
        foreach ($row as $key => $value){
            $rowHtml .=
                "<tr>".
                    "<td>". $key. "</td>".
                    "<td>". $value. "</td>".
                "</tr>";
        }
        if($rowHtml !== ""){
            $html =
                "<table class='responsive table-bordered' width='100%'>".
                "<tbody>".
                $rowHtml.
                "</tbody>".
                "</table>";
        }
        return $html;
    }




}