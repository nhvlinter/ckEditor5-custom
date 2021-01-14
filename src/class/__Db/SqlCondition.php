<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 30-10-19
 * Time: 18:48
 */

namespace salesteck\Db;


/**
 * Class SqlCondition
 * @package salesteck\Db
 */
class SqlCondition implements \JsonSerializable
{
    public const
        BETWEEN = "BETWEEN",
        DIFFERENT = "!=",
        EQUAL = "=",
        GREATER = ">",
        GREATER_EQUAL = self::GREATER.self::EQUAL,
        IN = "IN",
        NOT_IN = "NOT IN",
        LESS = "<",
        LESS_EQUAL = self::LESS.self::EQUAL,
        LIKE = "LIKE",
        NOT_LIKE = "NOT LIKE",

        LIKE_PATTERN = "%",

        _OR = "OR",
        _AND = "AND"
    ;



    private const
        AVAILABLE_OPERATOR =
            [
                self::BETWEEN, self::DIFFERENT, self::EQUAL, self::GREATER, self::GREATER_EQUAL,
                self::IN, self::NOT_IN, self::LESS, self::LESS_EQUAL, self::LIKE, self::NOT_LIKE
            ],
        AVAILABLE_ADD_OPERATOR = [self::_AND, self::_OR]
    ;

    private $tableName, $columnName, $operator, $value, $and_or, $caseSensitive;


    /**
     * @param string $tableName
     * @param string $columnName
     * @param string $operator
     * @param string $value
     * @param string $and_or
     * @param bool   $caseSensitive
     *
     * @return null|\salesteck\Db\SqlCondition
     */
    public static function _inst(
        string $tableName, string $columnName, string $operator, string $value,
        string $and_or = self::_AND, bool $caseSensitive = false
    ) : ? self
    {
        return new self($tableName, $columnName, $operator, $value, $and_or, $caseSensitive);
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @param string $value
     * @param string $and_or
     * @param bool   $caseSensitive
     *
     * @return null|\salesteck\Db\SqlCondition
     */
    public static function _instEqual(
        string $tableName, string $columnName, string $value,
        string $and_or = self::_AND, bool $caseSensitive = false
    ) : ? self
    {
        return self::_inst($tableName, $columnName, self::EQUAL, $value, $and_or, $caseSensitive);
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @param string $value
     * @param string $and_or
     * @param bool   $caseSensitive
     *
     * @return null|\salesteck\Db\SqlCondition
     */
    public static function _instBetween(
        string $tableName, string $columnName, string $value,
        string $and_or = self::_AND, bool $caseSensitive = false
    ) : ? self
    {
        return self::_inst($tableName, $columnName, self::BETWEEN, $value, $and_or, $caseSensitive);
    }

    /**
     * SqlCondition constructor.
     * @param string $tableName
     * @param string $columnName
     * @param string $operator
     * @param string $value
     * @param string $and_or
     * @param bool $caseSensitive
     */
    public function __construct(
        string $tableName, string $columnName, string $operator, string $value,
        string $and_or = self::_AND, bool $caseSensitive = false
    )
    {
        $this->columnName = $columnName;
        if(!self::_isOperatorValid($operator)){
            $operator = self::EQUAL;
        }
        $this->tableName = $tableName;
        $this->operator = $operator;
        if(!self::_isAddOperatorValid($and_or)){
            $and_or = self::_AND;
        }
        $this->and_or = $and_or;
        $this->value = $value;
        $this->caseSensitive = $caseSensitive;
    }

    /**
     * @return mixed
     */
    public function getTableName() : string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     * @return SqlCondition
     */
    public function setTableName(string $tableName) : self
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
     * @return $this
     */
    public function setColumnName(string $columnName) :self
    {
        $this->columnName = $columnName;
        return $this;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     * @return $this
     */
    public function setOperator(string $operator) :self
    {
        $this->operator = $operator;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value) :self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAndOr() :string
    {
        return $this->and_or;
    }

    /**
     * @param string $and_or
     * @internal param string $and_or
     * @return $this
     */
    public function setAndOr(string $and_or) :self
    {
        if(self::_isAddOperatorValid($and_or)){
            $this->and_or = $and_or;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCaseSensitive() : bool
    {
        return $this->caseSensitive;
    }

    /**
     * @param mixed $caseSensitive
     * @return $this
     */
    public function setCaseSensitive(bool $caseSensitive) : self
    {
        $this->caseSensitive = $caseSensitive;
        return $this;
    }


    /**
     * @return string
     */
    public function getConditionString() : string
    {

        $columnName  = $this->getColumnName();
        $operator = $this->getOperator();
        $value = $this->getValue();
        $tableName = $this->getTableName();
        $table_column = "$tableName.$columnName";
        if(!$this->caseSensitive){
            $table_column = "UPPER($table_column)";
            $value = "UPPER($value)";
        }
        return  "( $table_column $operator $value )";
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
        $obj->queryString = $this->getConditionString();
        return get_object_vars($obj);
    }

    /**
     * @param string $operator
     *
     * @return bool
     */
    public static function _isOperatorValid(string $operator) : bool
    {
        return in_array($operator, self::AVAILABLE_OPERATOR);
    }

    /**
     * @param string $addOperator
     *
     * @return bool
     */
    public static function _isAddOperatorValid(string $addOperator) : bool
    {
        return in_array($addOperator, self::AVAILABLE_ADD_OPERATOR);
    }

    /**
     * @param \salesteck\Db\SqlCondition $sqlCondition
     * @param \salesteck\Db\SqlCondition $sqlConditionCompare
     *
     * @return bool
     */
    public static function _compare(SqlCondition $sqlCondition, SqlCondition $sqlConditionCompare) : bool
    {
        $serialize = serialize($sqlCondition);
        $serializeCompare = serialize($sqlConditionCompare);
        return $serialize === $serializeCompare;
    }

}