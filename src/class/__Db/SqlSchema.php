<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 03-11-19
 * Time: 16:04
 */

namespace salesteck\Db;


use salesteck\config\Config;
use salesteck\utils\Debug;

class SqlSchema extends Db
{
    private $tableName, $arrayColumn;

    /**
     * SqlSchema constructor.
     * @param string $tableName
     * @param array $arrayColumn
     */
    public function __construct(string $tableName = "", array $arrayColumn = [])
    {
        $this->tableName = $tableName;
        $this->arrayColumn = $arrayColumn;
    }

    /**
     * @return string
     */
    private function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     * @return $this
     */
    private function setTableName(string $tableName) : self
    {
        $this->tableName = $tableName;
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


    public function table(string $tableName): self
    {
        return $this->setTableName($tableName);
    }

    private function addColumn(SqlSchemaColumn $schemaColumn) : self
    {
        $arrayColumn = $this->getArrayColumn();
        if($schemaColumn !== null && $schemaColumn instanceof SqlSchemaColumn){
            if(sizeof($arrayColumn)===0){
                array_push($arrayColumn, $schemaColumn);
            }
//            TODO
//            else{
//
//            }

        }

        return $this;
    }

    public function truncate(string $tableName = "") : bool
    {
        $success = false;
        $arrayDebug = [];
        $arrayDebug["parameter tableName"] = $tableName;
        if($tableName !== ""){
            $this->setTableName($tableName);
        }
        $tableName = $this->getTableName();
        $arrayDebug["tableName"] = $tableName;

        if($tableName !== ""){
            $dbName = Config::_getDbName();
            $sqlQuery = "TRUNCATE TABLE $dbName.$tableName;";
            $arrayDebug["sqlQuery"] = $sqlQuery;
            $conn = self::_getConnection();
            $statement = $conn->prepare($sqlQuery);
            $success = $statement->execute();
        }
        $arrayDebug["success"] = $success;
        Debug::_exposeVariableHtml($arrayDebug, true);
        return $success;
    }






}