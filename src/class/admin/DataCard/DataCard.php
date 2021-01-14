<?php

namespace salesteck\DataCard;

use salesteck\admin\AdminI18;
use salesteck\admin\AdminI18_C;
use salesteck\admin\AdminParameter;
use salesteck\admin\PortalLanguage_C;
use salesteck\_base\Language;
use salesteck\config\Config;
use salesteck\DataTable\DataTable;
use salesteck\Db\Sql;
use salesteck\Db\SqlCondition;
use salesteck\Db\SqlJoin;
use salesteck\Db\SqlOrder;
use salesteck\security\Security;
use salesteck\utils\CustomDateTime;
use salesteck\utils\String_Helper;

/**
 * Created by PhpStorm.
 * User: Son
 * Date: 16-06-20
 * Time: 01:30
 */

/**
 * Class DataCard
 * @package salesteck\DataCard
 */
class DataCard implements \JsonSerializable
{


    private const
        ACTION_READ = 'read',
        ACTION_CREATE = 'create',
        ACTION_EDIT = 'edit',
        ACTION_REMOVE = 'remove',
        ACTION_UPLOAD = 'upload';


    /**
     * Constant event
     */
    public const
        preGet = "preGet",
        postGet = "postGet",
        preCreate = "preCreate",
        writeCreate = "writeCreate",
        postCreate = "postCreate",
        preEdit = "preEdit",
        writeEdit = "writeEdit",
        postEdit = "postEdit",
        preRemove = "preRemove",
        postRemove = "postRemove",
        preUpload = "preUpload",
        postUpload = "postUpload",
        //TODO
        processed = "processed";

    public const
        INDEX_LANGUAGE = DataTable::_index_language,
        INDEX_SIZE_REPLACE = '{size}',
        INDEX_MAX_SIZE_REPLACE = '{maxSize}',
        INDEX_EXT_REPLACE = '{ext}';


    /**
     * @const bool validEvent
     * @internal
     */
    private const validEvent = [
        self::preGet,
        self::postGet,
        self::preCreate,
        self::writeCreate,
        self::postCreate,
        self::preEdit,
        self::writeEdit,
        self::postEdit,
        self::preRemove,
        self::postRemove,
        self::preUpload,
        self::postUpload

    ];



    private const
        INDEX_DATA = "data",
        INDEX_ACTION = "action";


    /**
     * @var string
     */
    private $idSrc = "";

    /** @var string $dataSrc */
    private $dataSrc = "";

    /** @var string $dataAlias */
    private $dataAlias = "";

    /** @var string $idSrcMulti */
    private $idSrcMulti = "";

    /** @var string */
    private $dataMultiSrc = "";

    /** @var array */
    private $fields = [];

    /** @var array */
    private $fieldsMulti = [];

    /** @var array */
    private $multiItems = [];

    /** @var string */
    private $multiItemSrc = "";

    /** @var array */
    private $_events = [];

    /** @var array */
    private $_validator = [];

    /** @var array */
    private $_conditions = [];

    /** @var array */
    private $_conditionsMulti = [];

    /** @var array */
    private $_order = [];

    /** @var array */
    private $_join = [];

    /** @var array */
    private $_elements = [];

    /** @var array */
    private $_response = null;

    /** @var boolean */
    private $debug;


    /**
     * DataCard constructor.
     *
     * @param string $dataSrc
     * @param string $idSrc
     * @param string $dataAlias
     */
    private function __construct(string $dataSrc = "", string $idSrc = "", string $dataAlias = "")
    {
        $this->idSrc = $idSrc;
        $this->dataSrc = $dataSrc;
        $this->dataAlias = $dataAlias;
        $this->_response = DataCardResponse::_inst();
        $this->debug = Config::_isDebug();
    }




    /** * * * * * * * PUBLIC FUNCTION PART * * * * * * * * * * * */

    /**
     * set the main table name
     *
     * @param string $dataSrc
     *
     * @return $this for chaining
     */
    public function dataSrc(string $dataSrc)
    {
        if ($dataSrc !== "") {
            $this->dataSrc = $dataSrc;
        }
        return $this;

    }

    /**
     * set main table id source
     *
     * @param string $idSrc
     *
     * @return $this
     */
    public function idSrc(string $idSrc): self
    {
        if ($idSrc !== "") {
            $this->idSrc = $idSrc;
        }
        return $this;
    }

    /**
     * @param string $dataMultiSrc
     *
     * @return DataCard
     */
    public function dataMultiSrc(string $dataMultiSrc): self
    {
        if ($dataMultiSrc !== "") {
            $this->dataMultiSrc = $dataMultiSrc;
        }
        return $this;

    }

    /**
     * @param string $idSrcMulti
     *
     * @return DataCard
     */
    public function idSrcMulti(string $idSrcMulti): self
    {
        if ($idSrcMulti !== "") {
            $this->idSrcMulti = $idSrcMulti;
        }
        return $this;
    }

    /**
     * @param string $multiItemSrc
     *
     * @return DataCard
     */
    public function multiItemSrc(string $multiItemSrc): self
    {
        if ($multiItemSrc !== "") {
            $this->multiItemSrc = $multiItemSrc;
        }
        return $this;
    }

    /**
     * @param array $multiItems
     *
     * @return DataCard
     */
    public function multiItems(array $multiItems): self
    {
        $arrayMulti = [];
        foreach ($multiItems as $item) {
            if (gettype($item) === gettype("") && $item !== "") {
                array_push($arrayMulti, $item);
            }
        }
        $this->multiItems = $arrayMulti;
        return $this;
    }

    /**
     * @param string|array|null $field
     *
     * @return $this|Field
     * @throws \Exception
     */
    public function field($field = null)
    {

        if (is_string($field)) {
            foreach ($this->fields as $fieldElement) {
                if ($fieldElement instanceof Field) {
                    if ($fieldElement->getName() === $field) {
                        return $fieldElement;
                    }
                }
            }
            throw new \Exception(Field::class.' : Unknown field: ' . $field);
        }else if (is_array($field)) {
            $arrayField = [];
            foreach ($field as $item) {
                if ($item instanceof Field) {
                    array_push($arrayField, $item);
                }
            }
            $this->fields = $arrayField;
        }
        return $this;
    }

    /**
     * @param null $field
     *
     * @return $this|Field
     * @throws \Exception
     */
    public function fieldMulti($field = null)
    {
        if (is_string($field)) {
            foreach ($this->fieldsMulti as $fieldElement) {
                if ($fieldElement instanceof Field) {
                    if ($fieldElement->getName() === $field) {
                        return $fieldElement;
                    }
                }
            }
            throw new \Exception('Unknown field: ' . $field);
        }

        if ($field !== null && is_array($field)) {
            $arrayField = [];
            foreach ($field as $item) {
                if ($item instanceof Field) {
                    array_push($arrayField, $item);
                }
            }
            $this->fieldsMulti = $arrayField;
        }
        return $this;
    }

    public function setDebug(bool $debug){
        $this->debug = $debug;
        return $this;
    }

    /**
     * set the debug property for debugging
     *
     * @param array $arg
     *
     * @return $this for chaining
     * @internal param string $name
     * @internal param $val
     *
     * @internal param bool $debug
     */
    public function debug(...$arg)
    {
        $args  =func_get_args();
        if(sizeof($args) > 1){
            if ($this->debug) {
                $this->_response
                    ->debug($args[0], $args[1]);
            }
        }else if(sizeof($args) === 1){

            if ($this->debug) {
                $this->_response
                    ->debug( $args[0]);
            }
        }
        return $this;
    }

    /**
     * add a join with another table
     *
     * @param SqlJoin $join
     *
     * @return $this
     */
    public function join(SqlJoin $join)
    {
        if (
            $join instanceof SqlJoin &&
            $join->getTable1() === $this->dataSrc &&
            $join->getTable2() !== "" &&
            in_array($join->getColumn1(), $this->getFieldsName())
        ) {
            array_push($this->_join, $join);

        }
        return $this;
    }

    /**
     * set elements table name and column
     *
     * @param SqlJoin $join
     *
     * @return $this
     */
    public function elements(SqlJoin $join)
    {
        if (
            $join instanceof SqlJoin &&
            $join->getTable1() === $this->dataSrc &&
            $join->getTable2() !== "" &&
            in_array($join->getColumn1(), $this->getFieldsName())
        ) {
            array_push($this->_elements, $join);

        }
        return $this;
    }

    /**
     * set sql condition
     *
     * @param SqlCondition|null $condition
     *
     * @return $this for chaining
     */
    public function condition(SqlCondition $condition = null)
    {
        if ($condition !== null && $condition instanceof SqlCondition) {
            array_push($this->_conditions, $condition);
        }
        return $this;
    }

    /**
     * set sql condition
     *
     * @param SqlCondition|null $condition
     *
     * @return $this for chaining
     */
    public function conditionMulti(SqlCondition $condition = null)
    {
        if ($condition !== null && $condition instanceof SqlCondition) {
            array_push($this->_conditionsMulti, $condition);
        }
        return $this;
    }

    /**
     * set sql order by column name and direction asc|desc
     *
     * @param string $columnName
     * @param string $direction
     *
     * @return $this for chaining
     */
    public function order(string $columnName = "", string $direction = SqlOrder::ASC)
    {
        if (
            String_Helper::_isStringNotEmpty($columnName) && in_array($columnName, $this->getFieldsName()) &&
            String_Helper::_isStringNotEmpty($this->dataSrc) && SqlOrder::_isDirectionValid($direction)
        ) {
            array_push($this->_order, new SqlOrder($this->dataSrc, $columnName, $direction));
        }
        return $this;
    }

    /**
     * set sql order by column name and direction asc|desc
     *
     * @param string $columnName
     * @param string $direction
     *
     * @return $this for chaining
     */
    public function orderMulti(string $columnName = "", string $direction = SqlOrder::ASC)
    {
        if (
            String_Helper::_isStringNotEmpty($columnName) && in_array($columnName, $this->getFieldsName()) &&
            String_Helper::_isStringNotEmpty($this->dataMultiSrc) && SqlOrder::_isDirectionValid($direction)
        ) {
            array_push($this->_order, new SqlOrder($this->dataMultiSrc, $columnName, $direction));
        }
        return $this;
    }

    /**
     * Add an event listener. The `Editor` class will trigger an number of
     * events that some action can be taken on.
     * each event as a determined parameter's number
     *
     * @param  string   $eventName Event name
     * @param  callable $callback  Callback function to execute when the event occurs
     *
     * @return $this for chaining
     */
    public function on(string $eventName, $callback): self
    {
        if (in_array($eventName, self::validEvent)) {

            if (gettype($callback) === gettype(function () {
                })) {
                if (!isset($this->_events[$eventName])) {
                    $this->_events[$eventName] = array();
                }

                $this->_events[$eventName][] = $callback;
            }

        }
        return $this;
    }

    /**
     * Add global validator
     * $callable is a function that has 3 parameters
     * 1 - $editor, instance of DataCard
     * 2 - $action, action name is either read, create, edit, remove or upload
     * 3 - $data, data submitted by the client
     *
     * function ($editor, $action, $data){
     *
     * }
     *
     * if that function return a string, an error message will display on the editor
     *
     * @param null $callable
     *
     * @return $this for chaining
     */
    public function validator($callable = null): self
    {
        if ( is_callable($callable) ) {
            array_push($this->_validator, $callable);
        }
        return $this;
    }

    public function error(string $message)
    {
        if ($this->debug) {
            $this->_response->error($message);
        }
        return $this;
    }



    /** * * * * * * * PRIVATE FUNCTION PART * * * * * * * * * * * */

    /**
     * get array string of fields name
     * @return array
     */
    private function getFieldsName(): array
    {
        $dataSrc = $this->dataSrc;
        $columnId = $this->idSrc;
        $arrayFieldName = [];
        if ($dataSrc !== "" && $columnId !== "") {
            foreach ($this->fields as $field) {
                if ($field instanceof Field && $field->getName() !== "") {
                    array_push($arrayFieldName, $field->getName());
                }
            }
            if (!in_array($columnId, $arrayFieldName)) {
                array_push($arrayFieldName, $columnId);
            }
        }
        return $arrayFieldName;
    }

    /**
     * get array string of multi fields name
     * @return array
     */
    private function getMultiFieldsName(): array
    {
        $columnId = $this->idSrc;
        $dataSrcMulti = $this->dataMultiSrc;
        $columnIdMulti = $this->idSrcMulti;
        $columnMulti = $this->multiItemSrc;
        $arrayFieldName = [];
        $arrayFieldsMulti = $this->fieldsMulti;
        if ($dataSrcMulti !== "" && $columnIdMulti !== "" && $columnMulti !== "" && $columnId !== "") {
            foreach ($arrayFieldsMulti as $field) {
                if ($field instanceof Field && $field->getName() !== "") {
                    array_push($arrayFieldName, $field->getName());
                }
            }
            if (!in_array($columnIdMulti, $arrayFieldName)) {
                array_push($arrayFieldName, $columnIdMulti);
            }
            if (!in_array($columnMulti, $arrayFieldName)) {
                array_push($arrayFieldName, $columnMulti);
            }
            if (!in_array($columnId, $arrayFieldName)) {
                array_push($arrayFieldName, $columnId);
            }
        }
        return $arrayFieldName;
    }

    /**
     * Format the data row with Field->_setFormat function for row editing|creating
     *
     * @param array $fields
     * @param array $dataRow
     * @param       $dataGroup
     *
     * @return array
     */
    private static function _setRowValueProcess(array $fields, array $dataRow, $dataGroup): array
    {

        foreach ($fields as $field) {
            if ($field instanceof Field && $field->getName() !== "" && $field->hasSetValueProcessor()) {
                $fieldName = $field->getName();
                if ($field instanceof Field) {
                    $fieldValue = array_key_exists($fieldName, $dataRow) ? $dataRow[$fieldName] : "";
                    $dataRow[$fieldName] = $field->_setFormat($fieldValue, $dataRow, $dataGroup);
                }
            }
        }
        return $dataRow;
    }

    /**
     * Format the data row with Field->_getFormat function for row rendering
     *
     * @param array $fields
     * @param array $dataRow
     *
     * @return array
     */
    private static function _getRowValueProcess(array $fields, array $dataRow): array
    {

        foreach ($fields as $field) {
            if ($field instanceof Field && $field->getName() !== "" && $field->hasGetValueProcessor()) {
                $fieldName = $field->getName();
                if ($field instanceof Field) {
                    $fieldValue = array_key_exists($fieldName, $dataRow) ? $dataRow[$fieldName] : "";
                    $dataRow[$fieldName] = $field->_getFormat($fieldValue, $dataRow);
                }
            }
        }
        return $dataRow;
    }

    /**
     * get array options for fields and multi fields
     * @return array
     */
    private function getOptions(): array
    {
        $arrayOptions = [];
        $dataSrc = $this->dataSrc;
        if ($dataSrc !== "") {
            foreach ($this->fields as $field) {
                if ($field instanceof Field) {
                    $fieldName = $field->getName();
                    $fieldOptions = $field->getOptions();
                    if (sizeof($fieldOptions) > 0 && $fieldName !== "") {
                        $arrayOptions[$dataSrc][$fieldName] = $fieldOptions;
                    }
                }
            }
        }

        $dataMultiSrc = $this->dataMultiSrc;
        if ($dataMultiSrc !== "") {
            foreach ($this->fieldsMulti as $fieldMulti) {
                if ($fieldMulti instanceof Field) {
                    $fieldName = $fieldMulti->getName();
                    $fieldOptions = $fieldMulti->getOptions();
                    if (sizeof($fieldOptions) > 0 && $fieldName !== "") {
                        $arrayOptions[$dataMultiSrc][$fieldName] = $fieldOptions;
                    }
                }
            }
        }
        return $arrayOptions;
    }

    /**
     * Run the file clean up
     * TODO
     * @private
     */
    private function _fileClean()
    {
        //TODO
        foreach ($this->fields as $field) {
            if ($field instanceof Field) {

                $upload = $field->getUpload();
                if ($upload instanceof FieldUpload) {
                    $upload->_dbCleanExec($this, $field);
                }
            }
        }
    }

    /**
     * get files data for fields & multi fields
     * loop through array fields & array multi fields
     * if field upload is not null, get the files of the upload instance
     * @return array
     */
    private function _getFiles()
    {
        $files = [];
        $allFields = array_merge($this->fields, $this->fieldsMulti);

        foreach ($allFields as $field) {
            if ($field instanceof Field) {
                $fieldUpload = $field->getUpload();
                if ($fieldUpload instanceof FieldUpload) {
                    $arrayFiles = $fieldUpload->getFiles();
                    if (sizeof($arrayFiles) > 0) {
                        $tableName = $fieldUpload->getDbTable();
                        if ($tableName !== "") {
                            $files[$tableName] = $arrayFiles;
                        }
                    }
                }
            }
        }
        return $files;
    }

    /**
     * render data properly for client side
     * data format
     * [{
     * "id": {
     * "table": {
     * "column" : "value"
     * },
     * "tableMulti" : [
     * {
     * "itemMulti" : {
     * "column" : "value"
     * }
     * }
     * ]
     * }
     * }]
     *
     * @param array $data
     *
     * @return array
     */
    private function _renderData(array $data = [])
    {
        $renderData = [];
        foreach ($data as $key => $value) {
            $item = [];
            $item[$key] = $value;
            array_push($renderData, $item);
        }
        return $renderData;
    }

    /**
     * Trigger an event
     *
     * @private
     *
     * @param      $eventName
     * @param null $arg1
     * @param null $arg2
     * @param null $arg3
     * @param null $arg4
     * @param null $arg5
     *
     * @return mixed|null
     */
    private function _trigger($eventName, &$arg1 = null, &$arg2 = null, &$arg3 = null, &$arg4 = null, &$arg5 = null)
    {
        $out = null;
        $argc = func_num_args();
        $args = array($this);

        // Hack to enable pass by reference with a "variable" number of parameters
        for ($i = 1; $i < $argc; $i++) {
            $name = 'arg' . $i;
            $args[] = &$$name;
        }

        if (!isset($this->_events[$eventName])) {
            return null;
        }

        $events = $this->_events[$eventName];

        for ($i = 0, $ien = count($events); $i < $ien; $i++) {
            $res = call_user_func_array($events[$i], $args);

            if ($res !== null) {
                $out = $res;
            }
        }

        return $out;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function _getElementsData(array $data = []): array
    {
        $tableName = $this->dataSrc;
        $idSrc = $this->idSrc;
        if ($tableName !== "" && $idSrc !== "") {
            foreach ($this->_elements as $joinElement) {
                if ($joinElement instanceof SqlJoin) {
                    $table1 = $joinElement->getTable1();
                    $column1 = $joinElement->getColumn1();

                    if ($tableName === $table1 && $idSrc === $column1) {
                        $joinTable = $joinElement->getTable2();
                        $joinColumn = $joinElement->getColumn2();

                        $arrayColumn = $joinElement->getColumns();

                        $sqlElements = Sql::_inst($joinTable);
                        $sqlElements->column($arrayColumn);
                        if ($sqlElements->select()) {
                            $resultsElement = $sqlElements->result();
                            $this->debug("resultsElement[$joinTable]", $resultsElement);
                            foreach ($resultsElement as $row_Join) {
                                if (array_key_exists($joinColumn, $row_Join)) {
                                    $dataId = $row_Join[$joinColumn];
                                    if ($dataId !== "") {
                                        foreach ($data as $rowId => $value) {
                                            if (array_key_exists($table1, $value)) {
                                                $valueTable = $value[$table1];
                                                if (array_key_exists($column1, $valueTable) && $valueTable[$column1] === $dataId) {
                                                    if (!isset($data[$rowId][$joinTable])) {
                                                        $data[$rowId][$joinTable] = [];
                                                    }
                                                    array_push($data[$rowId][$joinTable], $row_Join);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $this->debug("sqlElements[$joinTable]", $sqlElements);
                    }
                }
            }

        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function _getJoinData(array $data = []): array
    {
        $tableName = $this->dataSrc;
        if ($tableName !== "") {
            foreach ($this->_join as $join) {
                if ($join instanceof SqlJoin) {
                    $table1 = $join->getTable1();
                    if ($tableName === $table1) {
                        $joinTable = $join->getTable2();
                        $joinColumn = $join->getColumn2();
                        $column1 = $join->getColumn1();
                        $arrayColumn = $join->getColumns();
                        $sqlJoin = Sql::_inst($joinTable);
                        $sqlJoin->column($arrayColumn);
                        if ($sqlJoin->select()) {
                            $resultJoin = $sqlJoin->result();
                            foreach ($resultJoin as $row_Join) {
                                if (array_key_exists($joinColumn, $row_Join)) {
                                    $rowJoinValue = $row_Join[$joinColumn];
                                    if ($rowJoinValue !== "") {
                                        foreach ($data as $rowId => $value) {
                                            if (array_key_exists($table1, $value)) {
                                                $valueTable = $value[$table1];
                                                if (array_key_exists($column1, $valueTable) && $valueTable[$column1] === $rowJoinValue) {
//                                                    if(!isset($data[$rowId][$joinTable]) ){
//                                                        $data[$rowId][$joinTable] = [];
//                                                    }
//                                                    array_push($data[$rowId][$joinTable], $row_Join);
                                                    $data[$rowId][$joinTable] = $row_Join;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $this->debug("sqlJoin[$joinTable]", $sqlJoin);
                    }
                }
            }

        }
        return $data;
    }

    /**
     * Perform global validation.
     *
     * @param string $action
     * @param        $data
     *
     * @return bool
     *      true : no error
     *      false : error, error message is set to the response
     */
    private function _validate(string $action, $data): bool
    {
        $validators = $this->_validator;
        if ($validators) {
            for ($i = 0; $i < count($validators); $i++) {
                $validator = $validators[$i];
                $result = $validator($this, $action, $data);

                if (is_string($result)) {
                    $this->error($result);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Perform field validation.
     *
     * Note that validation is performed on data only when the action is
     * `create` or `edit`. Additionally, validation is performed on the _wire
     * data_ - i.e. that which is submitted from the client, without formatting.
     * Any formatting required by `setFormatter` is performed after the data
     * from the client has been validated.
     *
     * @param string $action
     * @param array  $data The format data to check
     *
     * @return bool `true` if the data is valid, `false` if not.
     */
    private function _allFieldsValidate(string $action, array $data)
    {
        $errors = [];
        $idVal = null;
        foreach ($data as $id => $dataRow) {
            $table = $this->dataSrc;
            $idSrc = $this->idSrc;
            if ($table !== "" && $idSrc !== "") {
                $tableData = array_key_exists($table, $dataRow) ? $dataRow[$table] : [];
                if (sizeof($tableData) > 0) {
                    $fields = $this->fields;
                    for ($i = 0; $i < count($fields); $i++) {
                        $field = $fields[$i];
                        if ($field instanceof Field) {
                            $fieldName = $field->getName();
                            if ($fieldName !== "") {
                                $id = array_key_exists($idSrc, $tableData) ? $tableData[$idSrc] : null;
                                if ($action === self::ACTION_EDIT && $id !== "") {
                                    $idVal = $id;
                                }
                                $fieldValue = array_key_exists($fieldName, $tableData) ? $tableData[$fieldName] : null;
                                if ($fieldValue !== null) {
                                    $validation = $field->validate($fieldValue, $dataRow, $this, $idVal);

                                    if ($validation !== true && $validation !== null) {
                                        $errors[$table][$fieldName] = $validation;
                                        $this->_response->fieldError("$table.$fieldName", $validation);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $tableMulti = $this->dataMultiSrc;
            $idSrcMulti = $this->idSrcMulti;
            if ($tableMulti !== "" && $idSrcMulti !== "") {
                $tableMultiData = array_key_exists($tableMulti, $dataRow) ? $dataRow[$tableMulti] : [];
                if (sizeof($tableMultiData) > 0) {
                    $multiItems = $this->multiItems;
                    foreach ($multiItems as $item) {
                        $dataItem = array_key_exists($item, $tableMultiData) ? $tableMultiData[$item] : [];
                        $fieldsMulti = $this->fieldsMulti;
                        for ($i = 0; $i < count($fieldsMulti); $i++) {
                            $fieldMulti = $fieldsMulti[$i];
                            if ($fieldMulti instanceof Field) {
                                $fieldName = $fieldMulti->getName();
                                if ($fieldName !== "") {
                                    $id = array_key_exists($idSrcMulti, $dataItem) ? $dataItem[$idSrcMulti] : null;
                                    if ($action === self::ACTION_EDIT && $id !== "") {
                                        $idVal = $id;
                                    }
                                    $fieldValue = array_key_exists($fieldName, $dataItem) ? $dataItem[$fieldName] : "";
                                    $validation = $fieldMulti->validate($fieldValue, $dataRow, $this, $idVal);

                                    if ($validation !== true && $validation !== null) {
                                        $this->_response->fieldError("$tableMulti.$item.$fieldName", $validation);
                                        $errors[$tableMulti][$item][$fieldName] = $validation;
                                    }
                                }
                            }
                        }
                    }

                }
            }


        }

        return count($errors) > 0 ? false : true;
    }


    /** * * * * * * * GET DATA PART * * * * * * * * * * * */

    /**
     * @param array $request
     *
     * @return DataCard
     */
    private function get(array $request): self
    {
        $action = self::ACTION_READ;

        $processTime = CustomDateTime::_getTimeStampMilli();

        $this
            ->debug(self::INDEX_ACTION, $action)
            ->debug('postData', $request);

        $globalValidation = $this->_validate($action, $request);
        if ($globalValidation === false) {
            return $this;
        }

        $cancel = $this->_trigger(self::preGet);
        if ($cancel !== false) {

            $data = $this->_getData();
            $data = $this->_getMultiData($data);
            $data = $this->_getJoinData($data);
            $data = $this->_getElementsData($data);
            $data = $this->_renderData($data);

            $this->_trigger(self::postGet, $data);

            $this->_response
                ->data($data)
                ->options($this->getOptions())
                ->multiItems($this->multiItems)
                ->files($this->_getFiles());
        }
        $processTime = CustomDateTime::_getTimeStampMilli() - $processTime;

        $this
            ->debug(self::INDEX_ACTION, $action)
            ->debug('request', $request)
            ->debug('processTimeMilli', $processTime);
        return $this;
    }

    /**
     * @param array $data
     *
     * @return array $data
     */
    private function _getData(array $data = []): array
    {
        $tableName = $this->dataSrc;
        $idSrc = $this->idSrc;
        $arrayFieldsName = $this->getFieldsName();
        $this->debug("arrayFieldsName", $arrayFieldsName);
        if ($tableName !== "" && $idSrc !== "") {
            $sql = Sql::_inst($tableName);
            $sql->column($arrayFieldsName);
            foreach ($this->_conditions as $condition) {
                if ($condition instanceof SqlCondition) {
                    $sql->addCondition($condition);
                }
            }
            foreach ($this->_order as $order) {
                if ($order instanceof SqlOrder) {
                    $sql->addOrder($order);
                }
            }
            $results = [];
            if ($sql->select()) {
                $results = $sql->result();
            }

            $this
                ->debug('sql', $sql)
                ->debug('result', $results);

            foreach ($results as $row) {
                if (array_key_exists($idSrc, $row)) {
                    $rowId = $row[$idSrc];
                    if ($rowId !== "") {
                        $data[$rowId][$tableName] = self::_getRowValueProcess($this->fields, $row);
                    }
                }
            }

        }
        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function _getMultiData(array $data = []): array
    {

        $tableName = $this->dataSrc;
        $multiItemSrc = $this->multiItemSrc;
        $tableMulti = $this->dataMultiSrc;
        $arrayFieldsNameMulti = $this->getMultiFieldsName();
        $idSrc = $this->idSrc;
        if ($tableName !== "" && $tableMulti !== "" && $multiItemSrc !== "" && $idSrc !== "") {
            $sql_multi = Sql::_inst($tableMulti);
            $sql_multi->column($arrayFieldsNameMulti);
            foreach ($this->_conditionsMulti as $condition) {
                if ($condition instanceof SqlCondition) {
                    $sql_multi->addCondition($condition);
                }
            }
            $result_multi = [];
            if ($sql_multi->select()) {
                $result_multi = $sql_multi->result();
            }
            foreach ($result_multi as $row_multi) {
                if (array_key_exists($idSrc, $row_multi) && array_key_exists($multiItemSrc, $row_multi)) {
                    $rowId = $row_multi[$idSrc];
                    $rowLanguage = $row_multi[$multiItemSrc];
                    if ($rowId !== "" && $rowLanguage !== "" && array_key_exists($rowId, $data)) {
                        $data[$rowId][$tableMulti][$rowLanguage] = self::_getRowValueProcess($this->fieldsMulti, $row_multi);
                    }

                }
            }
            $this
                ->debug('sql_multi', $sql_multi)
                ->debug('result_multi', $result_multi);
        }
        return $data;
    }



    /** * * * * * * * EDIT DATA PART * * * * * * * * * * * */

    /**
     * @param array $postData
     * @param array $i18n
     *
     * @return DataCard
     */
    private function edit(array $postData = [], array $i18n = array()): self
    {

        $action = self::ACTION_EDIT;

        $processTime = CustomDateTime::_getTimeStampMilli();

        $this
            ->debug(self::INDEX_ACTION, $action)
            ->debug('postData', $postData);

        $globalValidation = $this->_validate($action, $postData);
        if ($globalValidation === false) {
            return $this;
        }

        $fieldValidation = self::_allFieldsValidate($action, $postData);
        if ($fieldValidation === false) {
            return $this;
        }

//        $cancel = $this->_trigger(self::preEdit, $postData);
//        if ($cancel === false) {
//            return $this;
//        }
        $tableName = $this->dataSrc;
        $columnId = $this->idSrc;
        if ($tableName !== "" && $columnId !== "") {

            $data = [];
            $dataCanceled = [];
            foreach ($postData as $id => $dataGroup) {
                $cancel = $this->_trigger(self::preEdit, $id, $dataGroup);
                if ($cancel === false || is_string($cancel)) {
                    if(is_string($cancel)){
                        array_push($dataCanceled, $cancel);
                    }else{
                        array_push($dataCanceled, "Data ($id) was canceled : ".json_encode($dataGroup));
                    }
                }else{
                    $data = $this->_editData($id, $dataGroup, $data);
                }
            }

            $data = $this->_getJoinData($data);
            $data = $this->_renderData($data);


            $this->_trigger(self::postEdit, $data);

            $this->_response
                ->data($data)
                ->options($this->getOptions())
                ->multiItems($this->multiItems)
                ->files($this->_getFiles())
            ;
            if(sizeof($dataCanceled) > 0){
                $this->_response->message(implode(", ", $dataCanceled));
            }
        }


        $processTime = CustomDateTime::_getTimeStampMilli() - $processTime;


        $this
            ->debug('processTimeMilli', $processTime);
        return $this;
    }

    /**
     * @param       $id
     * @param       $dataGroup
     * @param array $returnData
     *
     * @return array
     */
    private function _editData($id, $dataGroup, array $returnData = []): array
    {
        if ($id !== "") {
            $tableName = $this->dataSrc;

            $columnId = $this->idSrc;

            $fieldsName = $this->getFieldsName();

            $dataRow = array_key_exists($tableName, $dataGroup) ? $dataGroup[$tableName] : [];

            $dataRow = self::_setRowValueProcess($this->fields, $dataRow, $dataGroup);

            $returnData[$id][$tableName] = [];
            $sql = Sql::_inst($tableName);
            $sql->equal($tableName, $columnId, $id);
            unset($dataRow[$columnId]);
            if ($sql->update($dataRow)) {
                $sql->column($fieldsName);
                if ($sql->select()) {
                    $returnData[$id][$tableName] = self::_getRowValueProcess(
                        $this->fields, $sql->first()
                    );
                }
                $returnData = $this->_editDataMulti($id, $dataGroup, $returnData);
                $this->_trigger(self::writeEdit, $id, $returnData);

            } else {
                $this->error($sql->error());
            }


            $this
                ->debug("dataRow", $dataRow)
                ->debug('sql', $sql);
        }

        return $returnData;
    }

    /**
     * @param       $id
     * @param       $dataGroup
     * @param array $returnData
     *
     * @return array
     */
    private function _editDataMulti($id, $dataGroup, array $returnData = []): array
    {

        $columnId = $this->idSrc;

        $tableNameMulti = $this->dataMultiSrc;
        $columnMulti = $this->multiItemSrc;
        $columnIdMulti = $this->idSrcMulti;

        if ($tableNameMulti !== "" && $columnMulti !== "" && $columnIdMulti !== "" && $id !== "") {

            $dataMulti = array_key_exists($tableNameMulti, $dataGroup) ? $dataGroup[$tableNameMulti] : [];

            foreach ($dataMulti as $keyMulti => $dataMultiRow) {

                $dataMultiRow = self::_setRowValueProcess($this->fieldsMulti, $dataMultiRow, $dataGroup);

                $dataMultiRow[$columnMulti] = $keyMulti;
                $dataMultiRow[$columnId] = $id;

                $this->debug("dataMulti[$keyMulti]", $dataMultiRow);

                $returnData[$id][$tableNameMulti][$keyMulti] = [];

                $sqlMulti = Sql::_inst($tableNameMulti);
                $sqlMulti
                    ->equal($tableNameMulti, $columnId, $id)
                    ->equal($tableNameMulti, $columnMulti, $keyMulti);

                unset($dataMultiRow[$columnIdMulti]);

                $fieldsMultiName = $this->getMultiFieldsName();

                if ($sqlMulti->count() > 0) {
                    if ($sqlMulti->update($dataMultiRow)) {
                        $sqlMulti->column($fieldsMultiName);
                        if ($sqlMulti->select()) {
                            $returnData[$id][$tableNameMulti][$keyMulti] = self::_getRowValueProcess(
                                $this->fieldsMulti, $sqlMulti->first()
                            );
                        }

                    } else {
                        $this->error($sqlMulti->error());
                    }
                } else {
                    $sqlMultiInsert = Sql::_inst($tableNameMulti);
                    $dataMultiRow[$columnMulti] = $keyMulti;
                    $dataMultiRow[$columnId] = $id;
                    if ($sqlMultiInsert->insert($dataMultiRow)) {
                        $sqlMultiInsert
                            ->equal($tableNameMulti, $columnId, $id)
                            ->equal($tableNameMulti, $columnMulti, $keyMulti);
                        $sqlMultiInsert->column($fieldsMultiName);
                        if ($sqlMultiInsert->select()) {
                            $returnData[$id][$tableNameMulti][$keyMulti] = self::_getRowValueProcess(
                                $this->fieldsMulti, $sqlMultiInsert->first()
                            );
                        }
                    } else {
                        $this->error($sqlMultiInsert->error());
                        $this->debug("sqlMultiInsert[$keyMulti]", $sqlMultiInsert);
                    }

                }

                $this
                    ->debug("dataMulti[$keyMulti]", $dataMultiRow)
                    ->debug("sqlMulti[$keyMulti]", $sqlMulti);
            }
        }
        return $returnData;
    }



    /** * * * * * * * CREATE DATA PART * * * * * * * * * * * */

    /**
     * @param array $postData
     * @param array $i18n
     *
     * @return DataCard
     */
    private function create(array $postData = [], array $i18n = array()): self
    {
        $action = self::ACTION_CREATE;

        $processTime = CustomDateTime::_getTimeStampMilli();

        $this
            ->debug(self::INDEX_ACTION, $action)
            ->debug('postData', $postData);

        $globalValidation = $this->_validate($action, $postData);
        if ($globalValidation === false) {
            return $this;
        }

        $fieldValidation = self::_allFieldsValidate($action, $postData);

        if ($fieldValidation === false) {
            return $this;
        }


        if (sizeof($postData) > 0) {

            $columnId = $this->idSrc;
            $tableName = $this->dataSrc;

            if ($tableName !== "" && $columnId !== "") {

                $data = [];
                $dataCanceled = [];
                foreach ($postData as $index => $dataGroup) {
                    $cancel = $this->_trigger(self::preCreate, $dataGroup);

                    if ($cancel !== false && ! is_string($cancel)) {
                        $data = $this->_createData($dataGroup, $data);
                    }else{
                        if(is_string($cancel)){
                            array_push($dataCanceled, $cancel);
                        }else{
                            array_push($dataCanceled, "Data ($index) was canceled : ".json_encode($dataGroup));
                        }
                    }
                }


                $data = $this->_getJoinData($data);

                $data = $this->_renderData($data);

                $this->_response
                    ->data($data)
                    ->options($this->getOptions())
                    ->multiItems($this->multiItems)
                    ->files($this->_getFiles())
                ;
                if(sizeof($dataCanceled) > 0){
                    $this->_response->message(implode(", ", $dataCanceled));
                }

                $this->_trigger(self::postCreate, $data);
            }
        }
        $processTime = CustomDateTime::_getTimeStampMilli() - $processTime;

        $this
            ->debug(self::INDEX_ACTION, $action)
            ->debug('postData', $postData)
            ->debug('processTimeMilli', $processTime);
        return $this;

    }

    /**
     * @param array $dataGroup
     * @param array $returnData
     *
     * @return array
     */
    private function _createData(array $dataGroup, array $returnData = []): array
    {

        $columnId = $this->idSrc;
        $tableName = $this->dataSrc;
        $arrayFieldsName = $this->getFieldsName();
        $dataRow = array_key_exists($tableName, $dataGroup) ? $dataGroup[$tableName] : [];
        if (sizeof($dataRow) > 0) {

            $dataRow = self::_setRowValueProcess($this->fields, $dataRow, $dataGroup);
            $dataId = array_key_exists($columnId, $dataRow) ? $dataRow[$columnId] : null;

            if ($dataId === "" || $dataId === null) {
                unset($dataRow[$columnId]);
            }


            $sql = Sql::_inst($tableName);
            $sql->column($arrayFieldsName);
            $sql->idColumn($columnId);
            if ($sql->insert($dataRow)) {
                $row = $sql->first();
                $dataId = $row[$columnId];
                $returnData[$dataId][$tableName] = self::_getRowValueProcess($this->fields, $row);

                $returnData = $this->_createDataMulti($dataId, $dataGroup, $returnData);

                $this->_trigger(self::writeCreate, $dataId, $returnData);

            } else {
                $this->error($sql->error());
            }
            $this
                ->debug("data", $dataRow)
                ->debug("sql", $sql);

        } else {
            $this->error("no data posted");
        }
        return $returnData;
    }

    /**
     * @param       $dataId
     * @param array $dataGroup
     * @param array $returnData
     *
     * @return array
     */
    private function _createDataMulti($dataId, array $dataGroup, array $returnData = []): array
    {

        $columnId = $this->idSrc;
        $colIdMulti = $this->idSrcMulti;
        $tableNameMulti = $this->dataMultiSrc;

        $columnMulti = $this->multiItemSrc;

        $arrayMultiFieldsName = $this->getMultiFieldsName();
        $dataMulti = array_key_exists($tableNameMulti, $dataGroup) ? $dataGroup[$tableNameMulti] : [];
        if (sizeof($dataMulti) > 0) {
            foreach ($dataMulti as $keyMulti => $dataMultiRow) {
                $sqlMulti = Sql::_inst($tableNameMulti);
                $sqlMulti->idColumn($colIdMulti);


                $dataMultiRow = self::_setRowValueProcess($this->fieldsMulti, $dataMultiRow, $dataGroup);

                $returnData[$dataId][$tableNameMulti][$keyMulti] = [];

                $dataIdMulti = array_key_exists($colIdMulti, $dataMultiRow) ? $dataMultiRow[$colIdMulti] : null;

                if ($dataIdMulti === "" || $dataIdMulti === null) {
                    unset($dataMultiRow[$colIdMulti]);
                }


                $dataMultiRow[$columnMulti] = $keyMulti;
                $dataMultiRow[$columnId] = $dataId;

                $this->debug("dataMulti[$keyMulti]", $dataMultiRow);

                if ($sqlMulti->insert($dataMultiRow)) {
                    $this->debug("sqlMultiInsert[$keyMulti]", $sqlMulti);


                    $sqlMulti
                        ->equal($tableNameMulti, $columnId, $dataId)
                        ->equal($tableNameMulti, $columnMulti, $keyMulti);
                    $this->debug("sqlMultiRead[$keyMulti]", $sqlMulti);

                    $sqlMulti->column($arrayMultiFieldsName);
                    if ($sqlMulti->select()) {
                        $returnData[$dataId][$tableNameMulti][$keyMulti] = self::_getRowValueProcess($this->fieldsMulti, $sqlMulti->first());
                    }
                } else {
                    $this->error($sqlMulti->error());
                }
                $this
                    ->debug("dataMulti[$keyMulti]", $dataMultiRow)
                    ->debug("sqlMulti[$keyMulti]", $sqlMulti);
            }
        }
        return $returnData;
    }



    /** * * * * * * * DATA PART * * * * * * * * * * * */

    /**
     * @param array $postData
     * @param array $i18n
     *
     * @return DataCard
     */
    private function remove(array $postData = [], array $i18n = array()): self
    {
        $action = self::ACTION_REMOVE;

        $this
            ->debug(self::INDEX_ACTION, $action)
            ->debug('postData', $postData);

        $globalValidation = $this->_validate($action, $postData);
        if ($globalValidation === false) {
            return $this;
        }

        if (sizeof($postData) > 0) {

            $columnId = $this->idSrc;

            $tableName = $this->dataSrc;

            $tableNameMulti = $this->dataMultiSrc;

            $colMultiItem = $this->multiItemSrc;

            if ($columnId !== "" && $tableName !== "") {

                $arrayBackupData = [];
                foreach ($postData as $id => $dataRow) {
                    $sql = Sql::_inst($tableName);
                    $sql->equal($tableName, $columnId, $id);
                    foreach ($this->_join as $join) {
                        if ($join instanceof SqlJoin) {
                            $sql->addJoin($join);
                        }
                    }


                    if ($sql->count() === 1 && $sql->select()) {
                        $backupData = $sql->first();
                        $arrayBackupData[$id] = self::_getRowValueProcess($this->fields, $backupData);
                        if ($sql->delete()) {


                            $removeStatus = true;

                            if ($tableNameMulti !== "" && $colMultiItem !== "") {
                                $arrayBackupDataMulti = [];
                                $dataMulti = array_key_exists($tableNameMulti, $dataRow) ? $dataRow[$tableNameMulti] : [];

                                $sqlMulti = Sql::_inst($tableNameMulti);
                                foreach ($dataMulti as $key => $itemMulti) {
                                    $sqlMulti
                                        ->equal($tableNameMulti, $columnId, $id)
                                        ->equal($tableNameMulti, $colMultiItem, $key);

                                    if ($sqlMulti->count() === 1 && $sqlMulti->select()) {
                                        $backupDataMulti = $sqlMulti->first();
                                        array_push($arrayBackupDataMulti, $backupDataMulti);

                                        if (!$sqlMulti->delete()) {
                                            $removeStatus = false;
                                        }
                                    }

                                }

                                if (!$removeStatus) {
                                    $sqlMulti->bulkInsert($arrayBackupDataMulti);
                                    $this
                                        ->debug('arrayBackupDataMulti', $arrayBackupDataMulti)
                                        ->error($sqlMulti->error());
                                }


                            }

                            if (!$removeStatus) {
                                $sql->insert($backupData);
                                $this->debug('arrayBackupData', $arrayBackupData);
                            }
                        }
                        $this->debug('sql', $sql);
                    }
                    $this->error($sql->error());


                    $this->debug('dataSql', $sql);
                }
            }


            $this
                ->debug(self::INDEX_ACTION, $action)
                ->debug('postData', $postData)
                ->debug('idSrc', $columnId)
                ->debug('tableName', $tableName)
                ->debug('tableNameMulti', $tableNameMulti)
                ->debug('colMultiItem', $colMultiItem)
                ->debug('multiItems', $this->multiItems);


        }
        return $this;
    }



    /** * * * * * * * UPLOAD PART * * * * * * * * * * * */

    /**
     * @param array $request
     * @param array $i18n
     *
     * @return $this
     */
    private function upload(array $request, array $i18n = array())
    {
        $action = self::ACTION_UPLOAD;
        $this
            ->debug(self::INDEX_ACTION, $action)
            ->debug('postData', $request);

        $globalValidation = $this->_validate($action, $request);
        if ($globalValidation === false) {
            return $this;
        }

        if (array_key_exists(FieldUpload::UPLOAD_FIELD, $request)) {
            $uploadFieldName = $request[FieldUpload::UPLOAD_FIELD];
            $uploadFieldNameIndex = explode(".", $uploadFieldName);
            $selectedField = null;
            if (sizeof($uploadFieldNameIndex) === 2) {
                foreach ($this->fields as $field) {
                    if ($field instanceof Field) {
                        if ($field->getName() === $uploadFieldNameIndex[sizeof($uploadFieldNameIndex) - 1] && $field->isUpload()) {
                            $selectedField = $field;
                        }
                    }
                }
            } else if (sizeof($uploadFieldNameIndex) === 3) {
                foreach ($this->fieldsMulti as $fieldMulti) {
                    if ($fieldMulti instanceof Field) {
                        if ($fieldMulti->getName() === $uploadFieldNameIndex[sizeof($uploadFieldNameIndex) - 1] && $fieldMulti->isUpload()) {
                            $selectedField = $fieldMulti;
                        }
                    }
                }
            }
            if ($selectedField instanceof Field) {
                $uploadField = $selectedField->getUpload();
                if ($uploadField instanceof FieldUpload) {
                    $uploadResult = $uploadField->exec($i18n);
                    if ($uploadResult === false) {
                        $this->_response->fieldError($uploadFieldName, $uploadField->getError());
                    }

                    if ($uploadResult !== null) {
                        $this->_response->upload($uploadResult);
                        $this->_response->files($this->_getFiles());

                    }
                    $debug = [
                        'uploadField' => $uploadField,
                        'uploadResult' => $uploadResult,
                        'request' => $request,
                        'selectedField' => $selectedField,
                        'dataCard' => $this,

                    ];
                    $this->debug('debug', $debug);
                }
            }

        }
        return $this;
    }



    /** * * * * * * * EXECUTION PART * * * * * * * * * * * */

    /**
     * @param array $i18n translation
     */
    public function exec($i18n = []): void
    {

        $request = isset($_REQUEST) ? Security::checkXss($_REQUEST) : [];

        $data = array_key_exists(self::INDEX_DATA, $request) ? $request[self::INDEX_DATA] : [];

        $action = array_key_exists(self::INDEX_ACTION, $request) ? $request[self::INDEX_ACTION] : "";


        switch ($action) {
            case self::ACTION_READ :
                $this->get($request);
                break;
            case self::ACTION_CREATE :
                $this->create($data);
                break;
            case self::ACTION_EDIT :
                $this->edit($data);
                break;
            case self::ACTION_REMOVE :
                $this->remove($data);
                break;
            case self::ACTION_UPLOAD :
                $this->upload($request, $i18n);
                break;
            default :
                $this->checkDefaultError($request, $i18n);
                $this->debug("postData", $request);
                $this->debug("action", $action);
                break;
        }
        $this->_json();
    }

    private function checkDefaultError(array $request, $i18n = [])
    {
        $postSize = Config::_getPostSize();
        $maxUploadSize = AdminParameter::_maxImageSize();
        if ($postSize > 0 && $postSize > $maxUploadSize) {
            $fieldName = array_key_exists("fieldName", $request) ? $request['fieldName'] : "";
            if (is_string($fieldName) && $fieldName !== "") {
                $this->_response->fieldError(
                    $fieldName,
                    AdminI18_C::_getValueUcFirst(AdminI18::EDITOR_ERROR_IMAGE_SIZE, $i18n,
                        [self::INDEX_SIZE_REPLACE => Config::octetToString($maxUploadSize)])
                );
                $this->debug("upload : $fieldName", "max upload size");
            }
        } else {
            $this->error("action required!");
        }
    }

    /**
     * @return $this
     */
    public function _json()
    {
        $json = (object)$this->_response;
        if ($this->debug) {
            $json->dataCard = $this;
            unset($json->dataCard->_response);
        }
        echo htmlspecialchars_decode(json_encode($json));
        return $this;
    }


    /**
     * @internal
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



    /** * * * * * * * STATIC FUNCTION PART * * * * * * * * * * * */

    /**
     * initialize DataCard instance
     *
     * @param string $dataSrc
     * @param string $idSrc
     *
     * @param string $dataAlias
     *
     * @return \salesteck\DataCard\DataCard
     */
    public static function _inst(string $dataSrc = "", string $idSrc = "", string $dataAlias = "")
    {
        return new self($dataSrc, $idSrc, $dataAlias);
    }

    public static function _getMultiItemLanguage(): array
    {
        $multiItems = [];

        $allLanguage = PortalLanguage_C::_getAllActiveLanguage();

        foreach ($allLanguage as $language) {
            if ($language instanceof Language) {
                array_push($multiItems, $language->getIdCode());
            }
        }
        return $multiItems;
    }


}