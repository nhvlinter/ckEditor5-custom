<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 20-12-20
 * Time: 17:00
 */

namespace salesteck\Db;


use salesteck\utils\String_Helper;

class SqlColumn implements \JsonSerializable
{
    /**
     * @var string $tableName
     */
    private $tableName;
    /**
     * @var string $columnName
     */
    private $columnName;
    /**
     * @var string $columnAlias
     */
    private $columnAlias;


    public static function _inst($tableName, $columnName, $columnAlias){
        if(
            String_Helper::_isStringNotEmpty($tableName) &&
            String_Helper::_isStringNotEmpty($columnName)
        ){
            $columnAlias = String_Helper::_isStringNotEmpty($columnAlias) ? $columnAlias : $columnName;
            return new self($tableName, $columnName, $columnAlias);
        }
        return null;
    }

    /**
     * SqlColumn constructor.
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $columnAlias
     */
    private function __construct(string $tableName, string $columnName, string $columnAlias)
    {
        $this->tableName = $tableName;
        $this->columnName = $columnName;
        $this->columnAlias = $columnAlias;
    }


    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     *
     * @return SqlColumn
     */
    public function setTableName(string $tableName): SqlColumn
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->columnName;
    }

    /**
     * @param string $columnName
     *
     * @return SqlColumn
     */
    public function setColumnName(string $columnName): SqlColumn
    {
        $this->columnName = $columnName;
        return $this;
    }

    /**
     * @return string
     */
    public function getColumnAlias(): string
    {
        return $this->columnAlias;
    }

    /**
     * @param string $columnAlias
     *
     * @return SqlColumn
     */
    public function setColumnAlias(string $columnAlias): SqlColumn
    {
        $this->columnAlias = $columnAlias;
        return $this;
    }

    public function getColumnQuery (){

        $tableName = $this->getTableName();
        $columnName = $this->getColumnName();
        $columnAlias = $this->getColumnAlias();
        $columnQuery =  "$tableName.$columnName" ;
        if(String_Helper::_isStringNotEmpty($columnAlias) && $columnAlias !== $columnName){
            $columnQuery .= " AS $columnAlias";
        }

        return $columnQuery;
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