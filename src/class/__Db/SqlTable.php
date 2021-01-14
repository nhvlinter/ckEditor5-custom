<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 31-10-19
 * Time: 00:21
 */

namespace salesteck\Db;


use salesteck\utils\String_Helper;

class SqlTable extends Db implements \JsonSerializable
{

    public static function _inst(string $tableName, $tableAlias = "", array $column = []){
        if(String_Helper::_isStringNotEmpty($tableName)){
            return new self($tableName, $tableAlias, $column);
        }
        return null;
    }

    private $tableName, $tableAlias, $column;

    /**
     * SqlTable constructor.
     *
     * @param string $tableName
     * @param string $tableAlias
     * @param array  $column
     */
    private function __construct(string $tableName, $tableAlias = "", array $column = [])
    {
        $this->tableName = $tableName;
//        $tableAlias = String_Helper::_isStringNotEmpty($tableAlias) ? $tableAlias : $tableName;
        $this->tableAlias = $tableAlias;
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getTableName() : string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getTableAlias() : string
    {
        return $this->tableAlias;
    }

    /**
     * @param mixed $tableName
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * @return array
     */
    public function getColumn() : array
    {
        return $this->column;
    }

    /**
     * @param mixed $column
     * @return $this
     */
    private function setColumn(array $column)
    {
        $this->column = $column;
        return $this;
    }


    public function addColumn(array $column, $tableName = "", $tableAlias = ""){
        if(is_array($this->column)){
            $arrayColumn = [];
            foreach ($column as $colAlias => $colName){
                if(String_Helper::_isStringNotEmpty($colName)){
                    $tableName = String_Helper::_isStringNotEmpty($tableName) ?  $tableName : $this->getTableName();
                    $tableAlias = String_Helper::_isStringNotEmpty($tableAlias) ?  $tableAlias : $this->getTableAlias();
                    if(String_Helper::_isStringNotEmpty($tableAlias)){
                        $tableName = $tableAlias;
                    }

                    $sqlCol = SqlColumn::_inst($tableName, $colName, $colAlias);
                    array_push($arrayColumn, $sqlCol);
                }
            }
            $this->setColumn(array_merge($this->column, $arrayColumn));
        }
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



    public static function _bindColumnName(string $tableName, array $arrayColumn){
        $bindColumn = [];
        foreach ($arrayColumn as $column){
            array_push($bindColumn, "$tableName.$column");
        }
        return $bindColumn;
    }

    public static function _bindColumnString(string $tableName, array $arrayColumn) : string
    {
        $columnString = " * ";
        $arrayColumnTable = self::_bindColumnName($tableName, $arrayColumn);
        if(sizeof($arrayColumnTable)>0){
            $columnString = implode(", ", $arrayColumnTable);
        }
        return $columnString;
    }
}