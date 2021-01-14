<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 07-11-19
 * Time: 01:13
 */

namespace salesteck\Db;


class SqlSchemaColumn
{


    public const
        CONSTRAINT_NOT_NULL = "NOT NULL",
        CONSTRAINT_UNIQUE = "UNIQUE",
        CONSTRAINT_PRIMARY_KEY = "PRIMARY KEY",
        CONSTRAINT_DEFAULT = "NOT NULL"
    ;

    public const
        TYPE_VARCHAR = "VARCHAR",
        TYPE_INT = "INT"
    ;


    private $columnName, $dataType, $arrayConstraints;

    /**
     * SqlSchemaColumn constructor.
     * @param string $columnName
     * @param string $dataType
     * @param array|string[] ...$constraints
     */
    public function __construct(string $columnName, string $dataType, ...$constraints)
    {
        $this->columnName = $columnName;
        $this->dataType = $dataType;
        $this->arrayConstraints = [];
        foreach ($constraints as $constraint) {
            if (gettype($constraint) === gettype("")) {
                array_push($this->arrayConstraints, $constraint);
            }
        }
    }


}