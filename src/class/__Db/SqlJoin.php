<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 31-10-19
 * Time: 01:20
 */

namespace salesteck\Db;



use salesteck\config\Config;

/**
 * Class SqlJoin
 * @package salesteck\Db
 */
class SqlJoin extends Db implements \JsonSerializable
{
    public const
        INNER = "INNER",
        LEFT = "LEFT",
        RIGHT = "RIGHT",
        FULL = "FULL OUTER"
    ;

    public static function _inst(string $table1, string $column1, string $table2, string $column2, string $joinOperator, array $columns = []){
        return new self($table1, $column1, $table2, $column2, $joinOperator, $columns);
    }

    private const AVAILABLE_JOIN = [self::INNER, self::FULL, self::LEFT, self::RIGHT];

    private $table1, $table2, $joinType, $column1, $column2, $columns = [];

    /**
     * SqlJoin constructor.
     * @param string $table1
     * @param string $column1
     * @param string $table2
     * @param string $column2
     * @param string $joinOperator
     * @param array $columns
     */
    public function __construct(string $table1, string $column1, string $table2, string $column2, string $joinOperator, array $columns = [])
    {
        $this->table2 = $table2;
        $this->joinType = $joinOperator;
        $this->column1 = $column1;
        $this->column2 = $column2;
        $this->table1 = $table1;
        $this->columns = $columns;
    }

    /**
     * @return string
     */
    public function getTable1(): string
    {
        return $this->table1;
    }

    /**
     * @param string $table1
     */
    public function setTable1(string $table1)
    {
        $this->table1 = $table1;
    }


    /**
     * @return string
     */
    public function getTable2(): string
    {
        return $this->table2;
    }

    /**
     * @param string $table2
     * @return $this
     */
    public function setTable2(string $table2) : self
    {
        $this->table2 = $table2;
        return $this;
    }

    /**
     * @return string
     */
    public function getJoinType(): string
    {
        return $this->joinType;
    }

    /**
     * @param string $joinType
     * @return $this
     */
    public function setJoinType(string $joinType) : self
    {
        $this->joinType = $joinType;
        return $this;
    }

    /**
     * @return string
     */
    public function getColumn1(): string
    {
        return $this->column1;
    }

    /**
     * @param string $column1
     * @return $this
     */
    public function setColumn1(string $column1) : self
    {
        $this->column1 = $column1;
        return $this;
    }

    /**
     * @return string
     */
    public function getColumn2(): string
    {
        return $this->column2;
    }

    /**
     * @param string $column2
     */
    public function setColumn2(string $column2)
    {
        $this->column2 = $column2;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
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
        $obj = (object) $this;
        $obj->queryString = $this->getJoinString();
        return get_object_vars($obj);
    }

    public function getJoinString(){
        $sqlQuery = "";
        $table1 = $this->getTable1();
        $table2 = $this->getTable2();
        $joinOperator = $this->getJoinType();
        $column1 = $this->getColumn1();
        $column2 = $this->getColumn2();
        if($table1 !== "" && $table2 !== "" && $column1 !== "" && $column2 !== "" && $joinOperator !== ""){
            $dbName = Config::_getDbName();
            $sqlQuery = " $joinOperator JOIN $dbName.$table2 ON $table1.$column1 = $table2.$column2";
        }
        return $sqlQuery;
    }

    public function getColumnString(){
        $columnString = "";
        if(sizeof($this->columns) > 0){
            $columnString = SqlTable::_bindColumnString($this->getTable2(), $this->columns);
            $columnString = ", $columnString";
        }
        return $columnString;
    }





    public static function _isJoinValid(string $joinType) : bool
    {
        return in_array($joinType, self::AVAILABLE_JOIN);
    }

    public static function _isColumnValid(string $column, array $arrayColumn){
        return in_array($column, $arrayColumn);
    }


}