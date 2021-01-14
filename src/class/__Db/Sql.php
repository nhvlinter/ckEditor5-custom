<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 31-10-19
 * Time: 00:21
 */

namespace salesteck\Db;

use PDO;
use salesteck\config\Config;
use salesteck\utils\Debug;
use salesteck\utils\String_Helper;
use salesteck\utils\Util;

/**
 * Class Sql
 *
 * This class make database's query easy
 *
 * Feature's list :
 *
 * - join : left, right, inner, full.
 * - condition's group.
 * - conditions : =, !=, <, <=, >=, like.
 * - order.
 * - limit
 * - offset
 * - group by
 *
 * @see \salesteck\Db\SqlJoin
 * @see \salesteck\Db\SqlConditionGroup
 * @see \salesteck\Db\SqlCondition
 * @see \salesteck\Db\SqlOrder
 *
 *
 * @package salesteck\Db
 * @example "Sql" <b>Create Sql Object</b>
 *          <pre>
 *          $sql = Sql::_inst(string : "_table_name"|null);
 *          </pre>
 * @example "Sql" <b>Set the id column index</b>
 *          <pre>
 *          $sql->idColumn(string : "_column_id");
 *          </pre>
 * @example "Sql" <b>Add the wanted column</b>
 *          <pre>
 *          $sql->column(array :["_column_1", "_column_2", "_column_3"]);
 *          </pre>
 * @example "Sql"  <b>Join</b>
 *          <pre>
 *          $sqlJoin = new \salesteck\Db\SqlJoin();
 *          $sql
 *              ->addJoin($sqlJoin)
 *              ->join($sqlJoin)
 *              ->leftJoin($sqlJoin)
 *              ->rightJoin($sqlJoin)
 *              ->innerJoin($sqlJoin)
 *              ->fullJoin($sqlJoin)
 *          ;
 *          </pre>
 * @example "Sql" <b>Add Condition</b>
 *          <pre>
 *          $sql->column(array :["_column_1", "_column_2", "_column_3"]);
 *          </pre>
 *
 */
class Sql implements \JsonSerializable
{



/******** START PRIVATE PART ********/

    /**
     * @internal
     */
    private const DISTINCT = "DISTINCT";


    /**
     * @var null $table
     * @internal
     */
    private $table;

    /**
     * @var [] $tableAliases
     */
    private $tableAliases;
    /**
     * @var array $arrayCondition
     * @internal
     */
    private $arrayCondition;
    /**
     * @var array $arrayConditionGroup
     * @internal
     */
    private $arrayConditionGroup;
    /**
     * @var array $arrayOrder
     * @internal
     */
    private $arrayOrder;
    /**
     * @var int $limit
     * @internal
     */
    private $limit;
    /**
     * @var int
     * @internal
     */
    private $offSet;
    /**
     * @var array
     * @internal
     */
    private $arrayTableJoin;
    /**
     * @var array
     * @internal
     */
    private $arrayResult;
    /**
     * @var int
     * @internal
     */
    private $rowCount;
    /**
     * @var string
     * @internal
     */
    private $idIndexColumn;
    /**
     * @var array
     * @internal
     */
    private $error;
    /**
     * @var string
     * @internal
     */
    private $queryString;
    /**
     * @var bool
     * @internal
     */
    private $status;
    /**
     * @var array
     * @internal
     */
    private $arrayGroupBy;

    /**
     * @param null   $tableName
     *
     * @param string $tableAlias=""
     *
     * @param array  $column=[]
     *
     * @return \salesteck\Db\Sql
     * @example "_inst()"
     *          <pre>
     *          $sql = Sql::_inst("table_name");
     *          </pre>
     */
    public static function _inst($tableName = null, $tableAlias = "", array $column = []) : self
    {
        return new self($tableName, $tableAlias, $column);
    }

    /**
     * This function is the main constructor of the class
     *
     * Sql constructor.
     *
     * @param null|string $tableName
     * @param string      $tableAlias =""
     * @param array       $column=[]
     *
     * @internal
     */
    private function __construct($tableName = null, $tableAlias = "", array $column = [])
    {
        $this->table = null;
        $this->tableAliases = [];
        if(String_Helper::_isStringNotEmpty($tableName)){
            $this->table($tableName, $tableAlias, $column);
        }
        $this->arrayCondition = [];
        $this->arrayConditionGroup = [];
        $this->arrayOrder = [];
        $this->limit = -1;
        $this->offSet = -1;
        $this->arrayTableJoin = [];
        $this->arrayResult = [];
        $this->rowCount = 0;
        $this->idIndexColumn = "";
        $this->error = [];
        $this->queryString = "";
        $this->status = false;
        $this->arrayGroupBy = [];
    }

    /**
     * @return SqlTable|null
     * @internal
     */
    private function getTable() : ? SqlTable
    {
        return $this->table;
    }

    /**
     * @param SqlTable $table
     * @return $this
     * @internal
     */
    private function setTable(SqlTable $table) : self
    {
        $this->table = $table;
        return $this;
    }


    /**
     * @return array
     * @internal
     */
    private function getArrayTableJoin(): array
    {
        return $this->arrayTableJoin;
    }

    /**
     * @return array
     * @internal
     */
    private function getArrayResult(): array
    {
        return $this->arrayResult;
    }

    /**
     * @param array $arrayResult
     * @return $this
     * @internal
     */
    private function setArrayResult(array $arrayResult) : self
    {
        $this->arrayResult = $arrayResult;
        $this->setRowCount(sizeof($arrayResult));
        return $this;
    }

    /**
     * @param int $rowCount
     * @return $this
     * @internal
     */
    private function setRowCount(int $rowCount) : self
    {
        if($rowCount >-1){
            $this->rowCount = $rowCount;
        }
        return $this;
    }

    /**
     * @param array $error
     * @return $this
     * @internal
     */
    private function setError(array $error): self
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @param mixed $queryString
     * @internal
     */
    private function setQueryString($queryString)
    {
        $this->queryString = $queryString;
    }



    /**
     * @internal
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

/* END OBJECT PART */






/******** START TABLE PART ********/

    /**
     * set table name and columns for query
     *
     * @param string $tableName
     * @param string $tableAlias=""
     * @param array  $column optional
     *
     * @return \salesteck\Db\Sql
     */
    public function table(string $tableName, $tableAlias = "", array $column = []) : self
    {
        $table = SqlTable::_inst($tableName, $tableAlias, $column);
        if(String_Helper::_isStringNotEmpty($tableAlias)){
            array_push($this->tableAliases, $tableAlias);
        }
        $this->setTable($table);
        return $this;
    }


    /**
     * get table name
     * @return string
     * @internal
     */
    private function getTableName() : string
    {
        $tableName = "";
        $thisTable = $this->getTable();
        if($thisTable !== null && $thisTable instanceof SqlTable){
            $tableName = $thisTable->getTableName();
        }
        return $tableName;
    }


    /**
     * TODO
     * get table name
     * @return string
     * @internal
     */
    private function getTableAlias() : string
    {
        $tableAlias = "";
        $thisTable = $this->getTable();
        if($thisTable !== null && $thisTable instanceof SqlTable){
            $tableAlias = $thisTable->getTableAlias();
        }
        return $tableAlias;
    }

/* END TABLE PART */


/******** START ERROR PART ********/

    /**
     * get error string from array
     * @return string
     */
    public function error() : string
    {
        $arrayDebug = [];
        $errors = $this->error;
        $errorString =  implode(", \n" , $errors);
        $arrayDebug["errors"] = $errors;
        $arrayDebug["errorString"] = $errorString;
        Debug::_exposeVariableHtml($arrayDebug);
        return $errorString;
    }

/* END ERROR PART */


/******** START COLUMN PART ********/

    /**
     * set the table's index column
     * @param string $idColumn
     *
     * @return $this
     */
    public function idColumn(string $idColumn) : self
    {
        if(String_Helper::_isStringNotEmpty($idColumn)){
            $this->idIndexColumn = $idColumn;
        }
        return $this;
    }

    /**
     * all columns for the query
     * @param array $column
     * @param null  $tableName
     *
     * @return $this
     */
    public function column(array $column, $tableName = null) : self
    {
        $thisTable = $this->getTable();
        if($thisTable !== null && $thisTable instanceof SqlTable && Util::_isArrayOfString($column)){
            if( !String_Helper::_isStringNotEmpty($tableName) ){
                $tableName = $thisTable->getTableName();
                $tableAlias = $thisTable->getTableAlias();
                if(String_Helper::_isStringNotEmpty($tableAlias)){
                    $tableName = $tableAlias;
                }
            }

//            $column = SqlTable::_bindColumnName($tableName, $column);
            $thisTable->addColumn($column, $tableName);
            $this->setTable($thisTable);
        }
        return $this;
    }

    /**
     * all columns for the query
     * @param array $column
     * @param null  $tableName
     *
     * @return $this
     */
    public function columnAlias(array $column, $tableName = null) : self
    {
        $thisTable = $this->getTable();
        if($thisTable instanceof SqlTable && Util::_isArrayOfString($column)){
            if( !String_Helper::_isStringNotEmpty($tableName) ){
                $tableName = $thisTable->getTableName();
                $tableAlias = $thisTable->getTableAlias();
                if(String_Helper::_isStringNotEmpty($tableAlias)){
                    $tableName = $tableAlias;
                }
            }

//            $arrayColumnTable = SqlTable::_bindColumnName($tableName, $column);
            $thisTable->addColumn($column);
            $this->setTable($thisTable);
        }
        return $this;
    }

    /**
     * get a string of all the columns
     * @return string
     * @internal
     */
    private function getColumnString(){
        $columnString = "";
        $thisTable = $this->getTable();
        if($thisTable !== null && $thisTable instanceof SqlTable){
            $columnString = self::_columnToString($thisTable->getColumn());
//            $columnString = SqlTable::_bindColumnString($thisTable->getTableName(), $thisTable->getColumn());
            $columnString .= $this->getJoinColumnString();
        }
        return $columnString;
    }

    /**
     * get a string of all the join's column
     * @return string
     * @internal
     */
    private function getJoinColumnString (){
        $columnString = "";
        foreach ($this->getArrayTableJoin() as $join){
            if($join instanceof SqlJoin){
                $columnString .= $join->getColumnString();
            }
        }
        return $columnString;

    }

/* END COLUMN PART */



/******** START ROW PART ********/

    /**
     * reinitialize the result
     * @return $this
     * @internal
     */
    private function reinitializeResult()
    : self
    {
        $this->setArrayResult([]);
        $this->setError([]);
        return $this;
    }

/* END TABLE PART */


/******** START CONDITION PART ********/

    /**
     * add some condition to the query
     * @param \salesteck\Db\SqlCondition $sqlCondition
     *
     * @return $this
     */
    public function addCondition(SqlCondition $sqlCondition)
    : self
    {
        if($sqlCondition !== null && $sqlCondition instanceof SqlCondition){
            if(sizeof($this->arrayCondition)===0){
                array_push($this->arrayCondition, $sqlCondition);
            }else{
                $conditionExist = false;
                foreach ($this->arrayCondition as $condition){
                    if($condition !== null && $condition instanceof SqlCondition){
                        $compare = SqlCondition::_compare($condition, $sqlCondition);
                        if($compare){
                            $conditionExist = true;
                        }
                    }
                }
                if(!$conditionExist){
                    array_push($this->arrayCondition, $sqlCondition);
                }
            }
        }
        return $this;
    }

    /**
     * add some group of condition to the query
     * @param $sqlConditionGroup
     *
     * @return $this
     */
    public function addConditionGroup($sqlConditionGroup)
    : self
    {
        if($sqlConditionGroup !== null && $sqlConditionGroup instanceof SqlConditionGroup){
            if(sizeof($this->arrayConditionGroup)===0){
                array_push($this->arrayConditionGroup, $sqlConditionGroup);
            }else{
                $conditionExist = false;
                foreach ($this->arrayConditionGroup as $conditionGroup){
                    if($conditionGroup !== null && $conditionGroup instanceof SqlConditionGroup){
                        $compare = SqlConditionGroup::_compare($conditionGroup, $sqlConditionGroup);
                        if($compare){
                            $conditionExist = true;
                        }
                    }
                }
                if(!$conditionExist){
                    array_push($this->arrayConditionGroup, $sqlConditionGroup);
                }
            }
        }
        return $this;
    }

    /**
     * add a "LIKE" statement to the query
     * @param string $tableName
     * @param string $columnName
     * @param mixed  $value
     * @param string $and_or
     *
     * @return $this
     * @internal
     */
    private function like(string $tableName, string $columnName, $value, string $and_or = SqlCondition::_AND)
    : self
    {
        $thisTable = $this->getTable();
        if(
            $thisTable !== null &&
            $thisTable instanceof SqlTable
        ){
            $value = addslashes($value);
            $sqlCondition = new SqlCondition($tableName, $columnName, SqlCondition::LIKE, "'$value'", $and_or);
            $this->addCondition($sqlCondition);
        }
        return $this;
    }


    /**
     * add a statement for the query that a column start with "$value%"
     * @param string $tableName
     * @param string $columnName
     * @param        $value
     * @param string $and_or
     *
     * @return $this
     */
    public function startWith(string $tableName, string $columnName, $value, string $and_or = SqlCondition::_AND)
    : self
    {
        return $this->like($tableName, $columnName, $value.SqlCondition::LIKE_PATTERN, $and_or);
    }

    /**
     * add a statement for the query that a column end with "%$value"
     * @param string $tableName
     * @param string $columnName
     * @param        $value
     * @param string $and_or
     *
     * @return $this
     */
    public function endWith(string $tableName, string $columnName, $value, string $and_or = SqlCondition::_AND)
    : self
    {
        return $this->like($tableName,$columnName, SqlCondition::LIKE_PATTERN.$value, $and_or);
    }

    /**
     * add a statement for the query that a column contain "%$value*"
     * @param string $tableName
     * @param string $columnName
     * @param        $value
     * @param string $and_or
     *
     * @return $this
     */
    public function contain(string $tableName, string $columnName, $value, string $and_or = SqlCondition::_AND)
    : self
    {
        return $this->like($tableName, $columnName, SqlCondition::LIKE_PATTERN.$value.SqlCondition::LIKE_PATTERN, $and_or);
    }

    /**
     * add a "NOT LIKE" statement to the query
     * @param string $tableName
     * @param string $columnName
     * @param        $value
     * @param string $and_or
     *
     * @return $this
     * @internal
     */
    private function notLike(string $tableName, string $columnName, $value, string $and_or = SqlCondition::_AND)
    : self
    {
        $thisTable = $this->getTable();
        if(
            $thisTable !== null &&
            $thisTable instanceof SqlTable
        ){
            $value = addslashes($value);
            $sqlCondition = new SqlCondition($tableName, $columnName, SqlCondition::NOT_LIKE, "'$value'", $and_or);
            $this->addCondition($sqlCondition);
        }
        return $this;
    }

    /**
     * add a statement for the query that a column doesn't contain "%$value%*"
     * @param string $tableName
     * @param string $columnName
     * @param        $value
     * @param string $and_or
     *
     * @return $this
     */
    public function notContain(string $tableName, string $columnName, $value, string $and_or = SqlCondition::_AND)
    : self
    {
        return $this->notLike($tableName, $columnName, SqlCondition::LIKE_PATTERN.$value.SqlCondition::LIKE_PATTERN, $and_or);
    }

    /**
     * add a statement for the query that a column is equal to "$value"
     *
     * @param string $tableName
     * @param string $columnName
     * @param        $value
     * @param string $and_or
     *
     * @param bool   $caseSensitive
     *
     * @return \salesteck\Db\Sql
     */
    public function equal(string $tableName, string $columnName, $value, string $and_or = SqlCondition::_AND, bool $caseSensitive = false)
    : self
    {
        return $this->equalOrEqual($tableName, [$columnName => $value], $and_or, $caseSensitive);
    }

    public function equalTrue(string $tableName, string $columnName, string $and_or = SqlCondition::_AND, bool $caseSensitive = false)
    : self
    {
        return $this->equal($tableName, $columnName, intval(true), $and_or, $caseSensitive);
    }

    public function equalFalse(string $tableName, string $columnName, string $and_or = SqlCondition::_AND, bool $caseSensitive = false)
    : self
    {
        return $this->equal($tableName, $columnName, intval(false), $and_or, $caseSensitive);
    }

    /**
     * add a statement for the query that a many column is equal to many "$value"
     *
     * @param string $tableName
     * @param array  $arrayColumnValue
     * @param string $and_or
     *
     * @param bool   $caseSensitive
     *
     * @return \salesteck\Db\Sql
     */
    public function equalOrEqual(string $tableName, array $arrayColumnValue,string $and_or = SqlCondition::_AND, bool $caseSensitive = false)
    : self
    {
        $thisTable = $this->getTable();
        if(
            $thisTable !== null &&
            $thisTable instanceof SqlTable
        ){
            foreach ($arrayColumnValue as $column => $value){
                $value = addslashes($value);
                $sqlCondition = new SqlCondition($tableName, $column, SqlCondition::EQUAL, "'$value'", $and_or, $caseSensitive);
                $this->addCondition($sqlCondition);
            }
        }
        return $this;
    }

    /**
     * add a statement for the query that a column is different to "$value"
     * @param string $tableName
     * @param string $columnName
     * @param        $value
     * @param string $and_or
     *
     * @return $this
     */
    public function different(string $tableName, string $columnName, $value, string $and_or = SqlCondition::_AND) : self
    {
        $thisTable = $this->getTable();
        if(
            $thisTable !== null &&
            $thisTable instanceof SqlTable
        ){
            $value = addslashes($value);
            $sqlCondition = new SqlCondition($tableName, $columnName, SqlCondition::DIFFERENT, "'$value'", $and_or);
            $this->addCondition($sqlCondition);
        }
        return $this;
    }

    /**
     * add a statement for the query that a column is not empty "''"
     * @param string $tableName
     * @param string $columnName
     * @param string $and_or
     *
     * @return $this
     */
    public function notEmpty(string $tableName, string $columnName, string $and_or = SqlCondition::_AND) : self
    {
        return $this->different($tableName, $columnName, "", $and_or);
    }

    /**
     * add a statement for the query that a column is greater than "$value"
     * @param string $tableName
     * @param string $columnName
     * @param        $value
     * @param string $and_or
     *
     * @return $this
     */
    public function greater(string $tableName, string $columnName, $value, string $and_or = SqlCondition::_AND) : self
    {
        $thisTable = $this->getTable();
        if(
            $thisTable !== null &&
            $thisTable instanceof SqlTable
        ){
            $value = addslashes($value);
            $sqlCondition = new SqlCondition($tableName, $columnName, SqlCondition::GREATER, "'$value'", $and_or);
            $this->addCondition($sqlCondition);
        }
        return $this;
    }

    /**
     * add a statement for the query that a column is lower than "$value"
     * @param string $tableName
     * @param string $columnName
     * @param        $value
     * @param string $and_or
     *
     * @return $this
     */
    public function lower(string $tableName, string $columnName, $value, string $and_or = SqlCondition::_AND) : self
    {
        $thisTable = $this->getTable();
        if(
            $thisTable !== null &&
            $thisTable instanceof SqlTable
        ){
            $value = addslashes($value);
            $sqlCondition = new SqlCondition($tableName, $columnName, SqlCondition::LESS, "'$value'", $and_or);
            $this->addCondition($sqlCondition);
        }
        return $this;
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @param array  $arrayValue
     * @param string $and_or
     *
     * @return $this
     */
    public function in(string $tableName, string $columnName, array $arrayValue, string $and_or = SqlCondition::_AND) : self
    {
        $thisTable = $this->getTable();
        if(
            $thisTable !== null &&
            $thisTable instanceof SqlTable
        ){
            $valueArray = [];
            foreach ($arrayValue as $value){
                $value = addslashes($value);
                array_push($valueArray, "'$value'");
            }
            $valueString = implode(", ", $valueArray);
            $sqlCondition = new SqlCondition($tableName, $columnName, SqlCondition::IN, "($valueString)", $and_or);
            $this->addCondition($sqlCondition);
        }
        return $this;

    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @param        $firstValue
     * @param        $secondValue
     * @param string $and_or
     *
     * @return $this
     */
    public function between(string $tableName, string $columnName, $firstValue, $secondValue, string $and_or = SqlCondition::_AND) : self
    {
        $thisTable = $this->getTable();
        if(
            $thisTable !== null &&
            $thisTable instanceof SqlTable
        ){
            $firstValue = addslashes($firstValue);
            $secondValue = addslashes($secondValue);
            $sqlCondition = new SqlCondition($tableName, $columnName, SqlCondition::BETWEEN, "'$firstValue' AND '$secondValue'", $and_or);
            $this->addCondition($sqlCondition);
        }
        return $this;
    }

    /**
     * @return string
     * @internal
     */
    private function getConditionString() : string
    {
        $conditionString = "";
        if(sizeof($this->arrayCondition) > 0 ){
            $conditionsString = "";
            for ($i=0; $i < sizeof($this->arrayCondition); $i++){
                $condition = $this->arrayCondition[$i];
                if($condition !== null && $condition instanceof SqlCondition){
                    $stringCondition = $condition->getConditionString();
                    $and_or = $condition->getAndOr();
                    if($i>0){
                        $stringCondition = " $and_or $stringCondition";
                    }
                    $conditionsString .= $stringCondition;
                }
            }

            $conditionString .= $conditionsString;
        }

        if(sizeof($this->arrayConditionGroup) > 0 ){
            $conditionsGroupString = "";
            for ($i=0; $i < sizeof($this->arrayConditionGroup); $i++){
                $conditionGroup = $this->arrayConditionGroup[$i];
                if($conditionGroup !== null && $conditionGroup instanceof SqlConditionGroup){
                    $stringConditionGroup = $conditionGroup->getConditionString();
                    $and_or = $conditionGroup->getAndOr();
                    if($i>0 || $conditionString !== ""){
                        $stringConditionGroup = " $and_or $stringConditionGroup";
                    }
                    $conditionsGroupString .= $stringConditionGroup;
                }
            }

            $conditionString .= $conditionsGroupString;
        }

        if($conditionString !== ""){
            $conditionString = "WHERE $conditionString";
        }
        return $conditionString;
    }

/* END CONDITION PART */


/******** START ORDER PART ********/

    /**
     * @param \salesteck\Db\SqlOrder $sqlOrder
     *
     * @return $this
     */
    public function addOrder(SqlOrder $sqlOrder){

        if($sqlOrder !== null && $sqlOrder instanceof SqlOrder){
            if(sizeof($this->arrayOrder)===0){
                array_push($this->arrayOrder, $sqlOrder);
            }else{
                $orderExist = false;
                foreach ($this->arrayOrder as $order){
                    if($order !== null && $order instanceof SqlOrder){
                        if(
                            $sqlOrder->getColumnName() === $order->getColumnName()
                        ){
                            $order->setDirection($sqlOrder->getDirection());
                            $orderExist = true;
                        }
                    }
                }
                if(!$orderExist){
                    array_push($this->arrayOrder, $sqlOrder);
                }
            }
        }
        return $this;
    }

    /**
     * @param string $tableName
     * @param string $columnName
     *
     * @return $this
     */
    public function orderAsc(string $tableName = "", string $columnName){
        if($tableName === ""){
            $tableName = $this->getTableName();
        }
        return $this->order($tableName, $columnName, SqlOrder::ASC);
    }

    /**
     * @param string $tableName
     * @param string $columnName
     *
     * @return $this
     */
    public function orderDesc(string $tableName = "",string $columnName){
        if($tableName === ""){
            $tableName = $this->getTableName();
        }
        return $this->order($tableName, $columnName, SqlOrder::DESC);
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @param string $direction
     *
     * @return $this
     * @internal
     */
    private function order(string $tableName = "", string $columnName, string $direction) : self
    {
        if($tableName === ""){
            $tableName = $this->getTableName();
        }
        $thisTable = $this->getTable();
        if(
            $thisTable !== null &&
            $thisTable instanceof SqlTable &&
            SqlOrder::_isDirectionValid($direction)
        ){
            $sqlOrder = new SqlOrder($tableName, $columnName, $direction);
            $this->addOrder($sqlOrder);
        }
        return $this;
    }

    /**
     * @return string
     * @internal
     */
    private function getOrderString() : string
    {
        $orderString = "";
        $thisTable = $this->getTable();
        if($thisTable !== null && $thisTable instanceof SqlTable && sizeof($this->arrayOrder)>0){
            $orderArray = [];
            foreach ($this->arrayOrder as $order ){
                if($order !== null && $order instanceof SqlOrder){
                    array_push($orderArray, $order->getOrderString());
                }
            }
            if(sizeof($orderArray) > 0 ){
                $orderArrayToString = implode(", ", $orderArray);
                $orderString = "ORDER BY $orderArrayToString ";
            }
        }
        return $orderString;
    }

/* END ORDER PART */


/******** START LIMIT PART ********/

    /**
     * @param int $limit
     * @param int $offSet
     *
     * @return $this
     */
    public function limit(int $limit = -1, int $offSet = -1) : self
    {
        if($limit > 0){
            $this->limit = $limit;
        }
        if($offSet > $limit){

            $this->offSet = $offSet;
        }
        return $this;
    }

    /**
     * @return string
     * @internal
     */
    private function getLimitString(){
        $limitString = "";


        $limit = $this->limit;
        if( is_integer($limit) && $limit > 0){
            $offSet = $this->offSet;

            $offSetString = "";

            if(is_int($offSet) && $offSet > 0 && $offSet > $limit){
                $offSetString = " OFFSET $offSet ";
            }

            $limitString = " LIMIT $limit $offSetString";
        }
        return $limitString;
    }

/* END LIMIT PART */


/******** START GROUP BY PART ********/


    /**
     * @param string $tableName
     * @param string $columnName
     *
     * @return \salesteck\Db\Sql
     */
    public function groupBy(string $tableName, string $columnName) : self
    {
        if(String_Helper::_isStringNotEmpty($tableName) && String_Helper::_isStringNotEmpty($columnName)){
            $groupString = "$tableName.$columnName";
            if( !$this->groupByExist($groupString) ){
                array_push($this->arrayGroupBy, $groupString);
            }
        }
        return $this;
    }

    /**
     * @param string $groupString
     *
     * @return bool
     * @internal
     */
    private function groupByExist(string $groupString) : bool
    {
        if(String_Helper::_isStringNotEmpty($groupString)){
            foreach ($this->arrayGroupBy as $groupBy){
                if($groupString === $groupBy){
                    return true;
                }
        }
        }
        return false;
    }


    /**
     * @return string
     * @internal
     */
    private function getGroupByString() : string
    {
        $returnStr = "";
        if (sizeof($this->arrayGroupBy) > 0){
            $returnStr = implode(", ", $this->arrayGroupBy);
        }

        return  $returnStr;
    }

/* END GROUP BY PART */


/******** START JOIN PART ********/

    /**
     * @param \salesteck\Db\SqlJoin $sqlJoin
     *
     * @return $this
     * @see \salesteck\Db\SqlJoin
     */
    public function addJoin(SqlJoin $sqlJoin){
        if($sqlJoin !== null && $sqlJoin instanceof SqlJoin){
            array_push($this->arrayTableJoin, $sqlJoin);
        }
        return $this;
    }

    /**
     * @param string $table1
     * @param string $column1
     * @param string $table2
     * @param string $column2
     * @param string $joinType
     *
     * @return $this
     */
    public function join(
        string $table1, string $column1, string $table2, string $column2, string $joinType = SqlJoin::INNER
    ) : self
    {
        $thisTable = $this->getTable();
        if(
            $thisTable !== null && $thisTable instanceof SqlTable &&
            SqlJoin::_isJoinValid($joinType)
        ){
            $sqlJoin = new SqlJoin($table1, $column1, $table2, $column2, $joinType);
            $this->addJoin($sqlJoin);
        }
        return $this;
    }

    /**
     * @param string $table1
     * @param string $column1
     * @param string $table2
     * @param string $column2
     *
     * @return $this
     */
    public function leftJoin(string $table1, string $column1, string $table2, string $column2) : self
    {
        return $this->join($table1, $column1, $table2, $column2, SqlJoin::LEFT);
    }

    /**
     * @param string $table1
     * @param string $column1
     * @param string $table2
     * @param string $column2
     *
     * @return $this
     */
    public function rightJoin(string $table1, string $column1, string $table2, string $column2) : self
    {
        return $this->join($table1, $column1, $table2, $column2, SqlJoin::RIGHT);
    }

    /**
     * @param string $table1
     * @param string $column1
     * @param string $table2
     * @param string $column2
     *
     * @return $this
     */
    public function innerJoin(string $table1, string $column1, string $table2, string $column2) : self
    {
        return $this->join($table1, $column1, $table2, $column2, SqlJoin::INNER);
    }

    /**
     * @param string $table1
     * @param string $column1
     * @param string $table2
     * @param string $column2
     *
     * @return $this
     */
    public function fullJoin(string $table1, string $column1, string $table2, string $column2) : self
    {
        return $this->join($table1, $column1, $table2, $column2, SqlJoin::FULL);
    }

    /**
     * @return string
     * @internal
     */
    private function getJoinString(): string
    {
        $joinString = "";
        $arrayJoin = $this->getArrayTableJoin();
        foreach ($arrayJoin as $join){
            if($join !== null && $join instanceof SqlJoin){
                $joinString .= " " . $join->getJoinString();
            }
        }
        return $joinString;
    }

    /**
     * @return string
     * @internal
     */
    private function getJoinTableString() : string
    {
        $joinTableString = "";
        $arrayJoin = $this->getArrayTableJoin();
        $arrayJoinTable = [];
        $dbName = Config::_getDbName();
        foreach ($arrayJoin as $join){
            if($join !== null && $join instanceof SqlJoin){
                $joinTable = $join->getTable2();
                if(String_Helper::_isStringNotEmpty($joinTable)){
                    array_push($arrayJoinTable, "$dbName.$joinTable");
                }
            }
        }
        if(sizeof($arrayJoinTable) > 0){
            $joinTableString = implode(", ", $arrayJoinTable);
        }
        return $joinTableString;
    }

/* END JOIN PART */


/******** START RESULT PART ********/

    /**
     * @return array
     */
    public function result() : array
    {
        return $this->getArrayResult();
    }

    /**
     * @param int $rowCount
     *
     * @return array|mixed
     */
    public function first(int $rowCount = 1)
    {
        if($rowCount<1){
            $rowCount = 1;
        }
        $arrayResult = $this->getArrayResult();
        if(sizeof($arrayResult)>0){
            if($rowCount===1){
                return $arrayResult[0];
            }else{
                if($rowCount > sizeof($arrayResult)){
                    return $arrayResult;
                } else{
                    return  array_slice($arrayResult,0,$rowCount);
                }
            }
        }else{
            return [];
        }
    }

/* END RESULT PART */


/******** START QUERY PART ********/

    /**
     * @return bool
     */
    public function tableExist() : bool
    {
        $arrayDebug = [];
        $this->reinitializeResult();
        $exist = false;
        $tableName = $this->getTableName();
        $arrayDebug["tableName"] = $tableName;
        if($tableName !== ""){
            $dbName = Config::_getDbName();
            $sqlQuery = "SHOW TABLES IN `$dbName` WHERE `Tables_in_$dbName` = '$tableName';";
            $arrayDebug["sqlQuery"] = $sqlQuery;
            $conn = Db::_getConnection();
            $statement = $conn->prepare($sqlQuery);
            if($statement->execute()){

                $rowCount = $statement->rowCount();
                $exist = $rowCount >0;
                $arrayDebug["rowCount"] = $rowCount;
            }

            Db::_closeConnection();
        }
        $arrayDebug["sql"] = $this;
        $arrayDebug["exist"] = $exist;
        Debug::_exposeVariableHtml($arrayDebug, false);
        return $exist;
    }

    /**
     * @param string $sqlQuery
     * @param array  $param
     *
     * @return bool
     */
    public function query(string $sqlQuery, array $param = []) : bool
    {
        $this->setQueryString($sqlQuery);
        $conn = Db::_getConnection();
        $statement = $conn->prepare($sqlQuery);
        $success =  $statement->execute($param);
        $this->status = $success;
        if($success){
            $arrayResult =$statement->fetchAll(PDO::FETCH_ASSOC);
            $arrayDebug["arrayResult"] = $arrayResult;
            $this->setArrayResult($arrayResult);
        }else{
            $this->setError([$statement->errorInfo()[2]]);
            throw new \Exception($statement->errorInfo()[2]);
        }
        Db::_closeConnection();
        $conn = null;

        $conn = null;
        return $success;
    }

    /**
     * @return string
     */
    public function getCountQueryString() : string
    {
        $tableName = $this->getTableName();
        if($tableName !== ""){
            $dbName = Config::_getDbName();

            $sqlQuery = "SELECT COUNT(*) FROM $dbName.$tableName";

            $joinString = $this->getJoinString();
            $sqlQuery = $joinString !== "" ? $sqlQuery."$joinString" : $sqlQuery;

            $whereCondition = $this->getConditionString();
            $sqlQuery = $whereCondition !== "" ? $sqlQuery." $whereCondition" : $sqlQuery;

            $orderString = $this->getOrderString();
            $sqlQuery = $orderString !== "" ? $sqlQuery." $orderString" : $sqlQuery;

            $limit = $this->getLimitString();
            $sqlQuery = $limit !== "" ? $sqlQuery." $limit" : $sqlQuery;

            $sqlQuery.= ";";

            return$sqlQuery;


        }
        return "";
    }

    /**
     * @return int
     */
    public function count() : int
    {
        $arrayDebug = [];
        $this->reinitializeResult();
        $count = -1;
        $tableName = $this->getTableName();
        $arrayDebug["tableName"] = $tableName;

        $sqlQueryString = $this->getCountQueryString();
        if(String_Helper::_isStringNotEmpty($sqlQueryString)){
            $this->setQueryString($sqlQueryString);
            $arrayDebug["sqlQuery"] = $sqlQueryString;
            $conn = Db::_getConnection();
            $statement = $conn->prepare($sqlQueryString);
            $success = $statement->execute();
            $arrayDebug["success"] = boolval($success);
            $this->status = $success;
            if($success){
                $count = intval($statement->fetchColumn());
                $arrayDebug["count"] = $count;
                $this->setRowCount($count);
            }else{
                $this->setError([$statement->errorInfo()[2]]);
            }
            Db::_closeConnection();
            $conn = null;
        }
        $arrayDebug["sql"] = $this;
        Debug::_exposeVariableHtml($arrayDebug, false);
        return $count;
    }


    /**
     * @param bool $distinct
     *
     * @return string
     */
    public function getSelectQueryString(bool $distinct = false) : string
    {

        $tableName = $this->getTableName();
        if($tableName !== ""){
            $tableAlias = $this->getTableAlias();
            $columnString = $this->getColumnString();
            $arrayDebug["columnString"] = $columnString;
            $columnString = $columnString === "" ? "*" : $columnString;
            $distinctString = $distinct? self::DISTINCT."" : "";

            $dbName = Config::_getDbName();

            $sqlQuery = "SELECT $distinctString $columnString FROM $dbName.$tableName";
            if(String_Helper::_isStringNotEmpty($tableAlias) && $tableAlias !== $tableName){
                $sqlQuery .= " AS $tableAlias";
            }

            $joinString = $this->getJoinString();
            $sqlQuery = $joinString !== "" ? $sqlQuery." $joinString" : $sqlQuery;
            $arrayDebug["joinString"] = $joinString;

            $whereCondition = $this->getConditionString();
            $sqlQuery = $whereCondition !== "" ? $sqlQuery." $whereCondition" : $sqlQuery;
            $arrayDebug["whereCondition"] = $whereCondition;

            $orderString = $this->getOrderString();
            $sqlQuery = String_Helper::_isStringNotEmpty($orderString) !== "" ? $sqlQuery." $orderString" : $sqlQuery;
            $arrayDebug["orderString"] = $orderString;

            $groupByString = $this->getGroupByString();
            $sqlQuery = String_Helper::_isStringNotEmpty($groupByString) !== "" ? $sqlQuery." $groupByString" : $sqlQuery;
            $arrayDebug["groupByString"] = $groupByString;

            $limit = $this->getLimitString();
            $sqlQuery = $limit !== "" ? $sqlQuery." $limit" : $sqlQuery;
            $arrayDebug["limit"] = $limit;

            $sqlQuery.= ";";

            return $sqlQuery;
        }

        return "";

    }

    /**
     * @param bool $distinct
     * @param bool $debug
     *
     * @return bool
     * @throws \Exception
     */
    public function select2(bool $distinct = false, bool $debug = false){
        $arrayDebug = [];
        $this->reinitializeResult();
        $success = false;


        $sqlQuery = $this->getSelectQueryString($distinct);

        if(String_Helper::_isStringNotEmpty($sqlQuery)){

            $this->setQueryString($sqlQuery);
            $arrayDebug["sqlQuery"] = $sqlQuery;
            $conn = Db::_getConnection();
            $statement = $conn->prepare($sqlQuery);
            $success = $statement->execute();
            $this->status = $success;
            $arrayDebug["success"] = boolval($success);
            if($success){
                $arrayResult =$statement->fetchAll(PDO::FETCH_ASSOC);
                $arrayDebug["arrayResult"] = $arrayResult;
                $this->setArrayResult($arrayResult);
            }else{
                $this->setError([$statement->errorInfo()[2]]);
                throw new \Exception($statement->errorInfo()[2]);
            }
            Db::_closeConnection();
            $conn = null;

        }

        $arrayDebug["sql"] = $this;
        Debug::_exposeVariableHtml($arrayDebug, $debug);
        Debug::_exposeVariable($arrayDebug, $debug);
        return $success;
    }

    /**
     * @param bool $distinct
     * @param bool $debug
     *
     * @return bool
     * @throws \Exception
     */
    public function select(bool $distinct = false, bool $debug = false) : bool
    {
        $arrayDebug = [];
        $this->reinitializeResult();
        $success = false;
        $tableName = $this->getTableName();
        $arrayDebug["tableName"] = $tableName;
        if(String_Helper::_isStringNotEmpty($tableName)){
            $columnString = $this->getColumnString();
            $arrayDebug["columnString"] = $columnString;
            $columnString = String_Helper::_isStringNotEmpty($columnString) ? $columnString :  "*" ;

            $distinctString = $distinct? self::DISTINCT."" : "";

            $dbName = Config::_getDbName();

            $sqlQuery = "SELECT $distinctString $columnString FROM $dbName.$tableName";

            $joinString = $this->getJoinString();
            $sqlQuery = $joinString !== "" ? $sqlQuery." $joinString" : $sqlQuery;
            $arrayDebug["joinString"] = $joinString;

            $whereCondition = $this->getConditionString();
            $sqlQuery = $whereCondition !== "" ? $sqlQuery." $whereCondition" : $sqlQuery;
            $arrayDebug["whereCondition"] = $whereCondition;

            $orderString = $this->getOrderString();
            $sqlQuery = $orderString !== "" ? $sqlQuery." $orderString" : $sqlQuery;
            $arrayDebug["orderString"] = $orderString;

            $limit = $this->getLimitString();
            $sqlQuery = $limit !== "" ? $sqlQuery." $limit" : $sqlQuery;
            $arrayDebug["limit"] = $limit;

            $sqlQuery.= ";";

            $this->setQueryString($sqlQuery);
            $arrayDebug["sqlQuery"] = $sqlQuery;
            $conn = Db::_getConnection();
            $statement = $conn->prepare($sqlQuery);
            $success = $statement->execute();
            $this->status = $success;
            $arrayDebug["success"] = boolval($success);
            if($success){
                $arrayResult =$statement->fetchAll(PDO::FETCH_ASSOC);
                $arrayDebug["arrayResult"] = $arrayResult;
                $this->setArrayResult($arrayResult);
            }else{
                $this->setError([$statement->errorInfo()[2]]);
//                throw new \Exception($statement->errorInfo()[2]);
            }
            Db::_closeConnection();
            $conn = null;
        }
        $arrayDebug["sql"] = $this;
        Debug::_exposeVariableHtml($arrayDebug, $debug);
        Debug::_exposeVariable($arrayDebug, $debug);
        return $success;
    }


    /**
     * @return string
     */
    public function getDeleteQueryString() : string
    {
        $tableName = $this->getTableName();
        $whereCondition = $this->getConditionString();
        if( String_Helper::_isStringNotEmpty($tableName) && String_Helper::_isStringNotEmpty($whereCondition)){
            $joinTableString = $this->getJoinTableString();
            if(String_Helper::_isStringNotEmpty($joinTableString)){
                $joinTableString = ", $joinTableString";
            }
            $joinString = $this->getJoinString();
            $dbName = Config::_getDbName();
            $sqlQuery = "DELETE $dbName.$tableName $joinTableString FROM $dbName.$tableName $joinString $whereCondition;";
            return $sqlQuery;
        }
        return "";

    }

    /**
     * @param bool $debug
     *
     * @return bool
     */
    public function delete(bool $debug = false) : bool
    {

        $arrayDebug = [];
        $success = false;

        $this->reinitializeResult();

        $tableName = $this->getTableName();
        $arrayDebug ["tableName"] = $tableName;

        $whereCondition = $this->getConditionString();
        $arrayDebug ["whereCondition"] = $whereCondition;

        $deleteQueryString = $this->getDeleteQueryString();

        if(String_Helper::_isStringNotEmpty($deleteQueryString)){
            $this->setQueryString($deleteQueryString);
            $arrayDebug ["query"] = $deleteQueryString;

            $conn = Db::_getConnection();
            $statement = $conn->prepare($deleteQueryString);
            $success = $statement->execute();

            $this->status = $success;
            if($success){
                $rowCount = $statement->rowCount();
                $this->setRowCount($rowCount);
                $arrayDebug ["rowCount"] = $rowCount;
            }else{
                $this->setError([$statement->errorInfo()[2]]);
            }
            Db::_closeConnection();
            $conn = null;
        }

        else{
            $this->setError(["No condition passed"]);
        }
        $arrayDebug ["success"] = boolval($success);
        Debug::_exposeVariableHtml($arrayDebug, $debug);
        return $success;
    }

    /**
     * @param array  $row
     * @param string $idColumn
     *
     * @return bool
     */
    public function insert(array $row, string $idColumn = "") : bool
    {
        return $this->bulkInsert([$row], $idColumn);
    }

    /**
     * @param array  $arrayRow
     * @param string $idColumn
     *
     * @return bool
     */
    public function bulkInsert(array $arrayRow, string $idColumn = "") : bool
    {
        $arrayDebug = [];
        $arrayDebug ["arrayRow"] = $arrayRow;
        $arrayDebug ["idColumn"] = $idColumn;
        $allSuccess = true;
        $this->reinitializeResult();
        $failedRow = [];
        $error= [];


        $insertedRow = [];
        if(sizeof($arrayRow)>0){
            $tableName = $this->getTableName();
            $arrayDebug ["tableName"] = $tableName;

            if($tableName !== ""){
                if($idColumn!== null && $idColumn !== ""){
                    $this->idColumn($idColumn);
                }

                $idColumnIndex = $this->idIndexColumn;
                $arrayDebug ["idColumnIndex"] = $idColumnIndex;

                $columnString = self::_bindParameterColumn($tableName, $arrayRow[0]);
                $arrayDebug ["columnString"] = $columnString;

                if($columnString !== ""){

                    $dbName = Config::_getDbName();

                    $sqlQuery = "INSERT INTO $dbName.$tableName $columnString;";
                    $this->setQueryString($sqlQuery);
                    $arrayDebug ["query"] = $sqlQuery;

                    $conn = Db::_getConnection();
                    $statement = $conn->prepare($sqlQuery);
                    for($i = 0; $i<sizeof($arrayRow); $i++){
                        $row = $arrayRow[$i];
                        $arrayValue = self::_bindParameterValue($row);
                        $arrayDebug["[$i]arrayValue"] = $arrayValue;

                        $success = $statement->execute($arrayValue);
                        if(boolval($success)){
                            if(String_Helper::_isStringNotEmpty($idColumnIndex) && !array_key_exists($idColumnIndex, $row)){
                                $row[$idColumnIndex] = $conn->lastInsertId();
                            }
                            array_push($insertedRow, $row);
                        }else{
                            $allSuccess = false;
                            array_push($failedRow, $row);
                            array_push($error, "[$i] ".$statement->errorInfo()[2]);
                            array_push($error, json_encode($row, JSON_PRETTY_PRINT));
                        }
                    }
                    Db::_closeConnection();
                    $conn = null;
                    $this->setArrayResult($insertedRow);
                    $this->setRowCount(sizeof($insertedRow));
                }else{
                    array_push($error, "Column string is empty!");
                }
            }else{
                array_push($error, "Table name is empty!");
            }
        }else{
            array_push($error, "empty data");
        }

        $this->status = $allSuccess;
        if(!$allSuccess){
            $successFullInsertSize = sizeof($insertedRow);
            $failedInsertSize = sizeof($failedRow);
            array_push($error, "successful insert : $successFullInsertSize, failed insert : $failedInsertSize");
        }

        $this->setError($error);
        $arrayDebug ["error"] = $error;
        Debug::_exposeVariableHtml($arrayDebug, false);


        return $allSuccess;
    }

    /**
     * @param array $arrayColumnValue
     * @param bool  $safePdoUpdate
     *
     * @return bool
     */
    public function update(array $arrayColumnValue, bool $safePdoUpdate = true) : bool
    {
        $arrayDebug = [];
        $arrayDebug ["arrayColumnValue"] = $arrayColumnValue;
        $arrayDebug ["safePdoUpdate"] = $safePdoUpdate;
        $success = true;
        $this->reinitializeResult();
        $error= [];

        if(sizeof($arrayColumnValue)>0 ){
            $tableName = $this->getTableName();
            $arrayDebug ["tableName"] = $tableName;

            if($tableName !== ""){
                $arraySet = [];
                if(Util::_isAssociativeArray($arrayColumnValue)){
                    foreach ($arrayColumnValue as $column => $value){
                        if($safePdoUpdate === true){
                            array_push($arraySet, "$tableName.$column=:$column");
                        }else{
                            $value = $value === "" ? "''" : $value;
                            array_push($arraySet, "$tableName.$column=$value");
                        }
                    }
                }
                $columnString = implode(", ", $arraySet);
                $arrayDebug ["columnString"] = $columnString;

                if($columnString !== ""){
                    $dbName = Config::_getDbName();

                    $whereCondition = $this->getConditionString();
                    $arrayDebug["whereCondition"] = $whereCondition;

                    $sqlQuery = "UPDATE $dbName.$tableName SET $columnString $whereCondition;";
                    $this->setQueryString($sqlQuery);
                    $arrayDebug ["sqlQuery"] = $sqlQuery;
                    $conn = Db::_getConnection();
                    $statement = $conn->prepare($sqlQuery);

                    if($safePdoUpdate === true) {
                        $arrayValue = self::_bindParameterValue($arrayColumnValue);
                        $arrayDebug ["arrayValue"] = $arrayValue;
                        $success = $statement->execute($arrayValue);
                    }else{
                        $success = $statement->execute();
                    }
                    $arrayDebug ["success"] = $success;

                    $this->status = $success;
                    if($success === false){
                        $error = [$statement->errorInfo()[2]];
                    }
                    Db::_closeConnection();
                    $conn = null;

                }else{
                    array_push($error, "Column string is empty!");
                }
            }else{
                array_push($error, "Table name is empty!");
            }
        }else{
            array_push($error, "empty data");
        }

        $this->setError($error);
        $arrayDebug ["error"] = $error;
        Debug::_exposeVariableHtml($arrayDebug, false);


        return $success;
    }

    /**
     * @param array $columns
     *
     * @return bool
     */
    public function increment(array $columns) : bool
    {
        $columnsValues = [];
        foreach ($columns as $column){
            if(is_string($column) && $column !== ""){
                $columnsValues[$column] = "$column+1";
            }
        }

        return self::update($columnsValues, false);
    }

    /**
     * @param array $columns
     *
     * @return bool
     */
    public function decrement(array $columns) : bool
    {
        $columnsValues = [];
        foreach ($columns as $column){
            if(is_string($column) && $column !== ""){
                $columnsValues[$column] = "$column-1";
            }
        }

        return self::update($columnsValues, false);
    }

/* END QUERY PART */


/******** START STATIC PART ********/

    /**
     * @param array<SqlColumn> $columns
     *
     * @return string
     * @internal
     */
    private static function _columnToString(array $columns){
        $columnString = "";
        if(sizeof($columns)>0){
            $columnsQuery = [];
            foreach ($columns as $sqlColumn){
                if($sqlColumn instanceof SqlColumn){
                    array_push($columnsQuery, $sqlColumn->getColumnQuery());
                }
            }
            $columnString = implode(", ", $columnsQuery);
        }
        return $columnString;
    }

    /**
     * @param array $arrayColumnValue
     *
     * @return array
     * @internal
     */
    private static function _bindParameterValue(array $arrayColumnValue) : array
    {
        $output = [];
        if(Util::_isAssociativeArray($arrayColumnValue)){
            foreach ($arrayColumnValue as $column => $value){
//                $value = gettype($value) === gettype([]) ? implode("|", $value) : $value;
                $output[":$column"] = ($value);
            }
        }else{
            $output = $arrayColumnValue;
        }
        return $output;
    }

    /**
     * @param string $tableName
     * @param array  $arrayColumnValue
     *
     * @return string
     * @internal
     */
    private static function _bindParameterColumn(string $tableName, array $arrayColumnValue) : string
    {
        $output = "";
        $arrayColumn = [];
        $arrayValue = [];
        if(Util::_isAssociativeArray($arrayColumnValue)){
            foreach ($arrayColumnValue as $column => $value){
                array_push($arrayColumn, "$tableName.$column");
                array_push($arrayValue, ":$column");
            }
        }
        if(sizeof($arrayColumn) > 0 && sizeof($arrayValue) > 0 && sizeof($arrayColumn) === sizeof($arrayValue)){
            $output = "(" . implode(", ", $arrayColumn) . ") VALUES (" . implode(", ", $arrayValue) ." )";
        }
        return $output;
    }

/* END STATIC PART */
}