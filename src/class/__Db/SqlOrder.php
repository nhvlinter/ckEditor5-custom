<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 31-10-19
 * Time: 00:25
 */

namespace salesteck\Db;


class SqlOrder implements \JsonSerializable
{
    public const
        ASC = "ASC",
        DESC = "DESC"
    ;

    private const AVAILABLE_DIRECTION = [self::ASC, self::DESC];

    private $tableName, $columnName, $direction;

    public static function _inst(string $tableName = "", string $columnName, string $direction = self::ASC){
        return new self($tableName, $columnName, $direction);
    }

    /**
     * SqlOrder constructor.
     * @param string $tableName
     * @param string $columnName
     * @param string $direction
     */
    public function __construct(string $tableName = "", string $columnName, string $direction = self::ASC)
    {
        $this->tableName = $tableName;
        $this->columnName = $columnName;
        if(!self::_isDirectionValid($direction)){
            $direction = self::ASC;
        }
        $this->direction = $direction;
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
     */
    public function setTableName(string $tableName)
    {
        $this->tableName = $tableName;
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
     */
    public function setColumnName(string $columnName)
    {
        $this->columnName = $columnName;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     */
    public function setDirection(string $direction)
    {
        $this->direction = $direction;
    }


    public function getOrderString(string $tableName = ""){
        if($tableName === ""){
            $tableName = $this->getTableName();
        }
        $columnName  = $this->getColumnName();
        $direction = $this->getDirection();
        $tableName = $tableName !== "" ? "$tableName." : $tableName;
        return $tableName."$columnName $direction";
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
        $obj->queryString = $this->getOrderString();
        return get_object_vars($obj);
    }



    public static function _isDirectionValid(string $direction) : bool
    {
        return in_array($direction, self::AVAILABLE_DIRECTION);
    }

}