<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-07-20
 * Time: 16:29
 */

namespace salesteck\DataCard;


use salesteck\admin\AdminI18;
use salesteck\admin\AdminI18_C;
use salesteck\admin\AdminParameter;
use salesteck\config\Config;
use salesteck\Db\Sql;
use salesteck\utils\Debug;
use salesteck\utils\File;
use salesteck\utils\FileUpload;


/**
 * Class FieldUpload
 * @package salesteck\DataCard
 */
class FieldUpload implements \JsonSerializable
{
    public const
        __ID__ = "__ID__",
        __EXT__ = "__EXT__",
        __CONTENT__ = "__CONTENT__",
        __CONTENT_TYPE__ = "__CONTENT_TYPE__",
        __FILE_NAME__ = "__FILE_NAME__",
        __FILE_SIZE__ = "__FILE_SIZE__",
        __MIME_TYPE__ = "__MIME_TYPE__",
        __READ_ONLY__ = "__READ_ONLY__",
        __SYSTEM_PATH__ = "__SYSTEM_PATH__",
        __WEB_PATH__ = "__WEB_PATH__",
        __ABSOLUTE_PATH__ = "__ABSOLUTE_PATH__",
        INDEX_UPLOAD = "upload",
        UPLOAD_FIELD = "uploadField",
        preUpload = "preUpload",
        postUpload = "postUpload"
    ;



    private $action;
    private $fileExt = [];
    private $_error = "";
    private $_extensionError = "";
    private $_dbTable = null;
    private $_dbPKey = null;
    private $_dbFields = [];
    private $_columnsValuesCondition = [];
    private $_dbCleanCallback = null;
    private $_dbCleanTableField = null;
    private $_validators = [];
    private $_webPathFolder = "";
    private $_maxSize = -1;

    /** @var array */
    private $_events = [];

    /** @var array */
    private $_debug = [];


    public static function _inst( $action=null, string $_webPathFolder = "") : self
    {
        return new self($action, $_webPathFolder);
    }

    private function debug(string $__FUNCTION, string $__LINE__, $var = null){
        $title = Debug::_getDebugString(__FILE__, $__FUNCTION, $__LINE__);
        array_push($this->_debug, ["$__LINE__:$title" => $var]);
    }


    /**
     * Set the action to take when a file is uploaded. This can be either of:
     *
     * * A string - the value given is the full system path to where the
     *   uploaded file is written to. The value given can include three "macros"
     *   which are replaced by the script dependent on the uploaded file:
     *   * `__EXT__` - the file extension
     *   * `__NAME__` - the uploaded file's name (including the extension)
     *   * `__ID__` - Database primary key value if the {@link db} method is
     *     used.
     * * A closure - if a function is given the responsibility of what to do
     *   with the uploaded file is transferred to this function. That will
     *   typically involve writing it to the file system so it can be used
     *   later.
     *
     * @param  string|callable $action Action to take - see description above.
     * @param string $_webPathFolder
     */
    public function __construct($action, string $_webPathFolder = "")
    {
        $this->debug(__FUNCTION__, __LINE__, ['action' => $action]);
        if ( $action  && ( is_string($action) || is_callable($action))) {
            $this->action( $action );
        }
        $this->_webPathFolder = $_webPathFolder;
    }

    /**
     * Set the action to take when a file is uploaded. This can be either of:
     *
     * * A string - the value given is the full system path to where the
     *   uploaded file is written to. The value given can include three "macros"
     *   which are replaced by the script dependent on the uploaded file:
     *   * `__EXTN__` - the file extension
     *   * `__NAME__` - the uploaded file's name (including the extension)
     *   * `__ID__` - Database primary key value if the {@link db} method is
     *     used.
     * * A closure - if a function is given the responsibility of what to do
     *   with the uploaded file is transferred to this function. That will
     *   typically involve writing it to the file system so it can be used
     *   later.
     *
     * @param  string|callable $action Action to take - see description above.
     * @return self Current instance, used for chaining
     */
    public function action ( $action )
    {
        $this->action = $action;
        $this->debug(__FUNCTION__, __LINE__, ['action' => $action]);

        return $this;
    }

    /**
     * Database configuration method. When used, this method will tell Editor
     * what information you want written to a database on file upload, should
     * you wish to store relational information about your file on the database
     * (this is generally recommended).
     *
     * @param  string $tableName        The name of the table where the file information
     *                                  should be stored
     * @param  string $columnId         Primary key column name. The `Upload` class
     *                                  requires that the database table have a single primary key so each
     *                                  row can be uniquely identified.
     * @param  array  $columnProperties A list of the fields to be written to on upload.
     *                                  The property names are the database columns and the values can be
     *                                  defined by the constants of this class. The value can also be a
     *                                  string or a closure function if you wish to send custom information
     *                                  to the database.
     * @param array   $columnsValuesCondition
     *
     * @return \salesteck\DataCard\FieldUpload Current instance, used for chaining
     */
    public function db( string $tableName = "", string $columnId = "", array $columnProperties = [], array $columnsValuesCondition = [])
    {
        $this->_dbTable = $tableName;
        $this->_dbPKey = $columnId;
        $this->_dbFields = $columnProperties;
        $this->_columnsValuesCondition = $columnsValuesCondition;
        $this->debug(__FUNCTION__, __LINE__, [
            'tableName' => $tableName,
            'columnId' => $columnId,
            'columnProperties' => $columnProperties
        ]);


        return $this;
    }

    /**
     * Add a validation method to check file uploads. Multiple validators can be
     * added by calling this method multiple times - they will be executed in
     * sequence when a file has been uploaded.
     *
     * @param  callable $fn Validation function. A PHP `$_FILES` parameter is
     *     passed in for the uploaded file and the return is either a string
     *     (validation failed and error message), or `null` (validation passed).
     * @return self Current instance, used for chaining
     */
    public function validator ( $fn )
    {
        if(is_callable($fn)){
            array_push($this->_validators, $fn);
        }
//        $this->_debug(['$this->validator($fn)' => $fn]);
        $this->debug(__FUNCTION__, __LINE__, [
            'fn' => $fn
        ]);

        return $this;
    }

    /**
     * @param array $i18n
     * @return bool|int|string
     */
    public function exec(array $i18n = array()){
        $id = null;
        $uploadFile = null;
//        $this->_debug(['$this->exec()']);

        $uploadFile = FileUpload::_inst(self::INDEX_UPLOAD);
        $this->debug(__FUNCTION__, __LINE__, [
            'uploadFile' => $uploadFile
        ]);

        if($uploadFile instanceof FileUpload){
            $error = $uploadFile->getError();
            if( $error !== UPLOAD_ERR_OK ){

                if ( $error === UPLOAD_ERR_INI_SIZE ) {
                    $maxUploadSize = AdminParameter::_maxImageSize();
                    $this->_error = AdminI18_C::_getValueUcFirst(
                        AdminI18::EDITOR_ERROR_IMAGE_SIZE,
                        $i18n,
                        [ DataCard::INDEX_SIZE_REPLACE => Config::octetToString($maxUploadSize) ]
                    );
                }
                else {
                    $uploadError = $uploadFile->getError();
                    $this->_error = "There was an error uploading the file (".$uploadError.")";
                }
                return false;
            }
            $validators = $this->_execValidator($uploadFile);
            if( $validators !== true ){
                return false;
            }

            $trigger = $this->_trigger( self::preUpload);
            if($trigger !== false){
                $id = $this->_dbExec( $uploadFile );
                return $this->_actionExec( $id, $uploadFile);
            }

        }
        return false;
    }

    private function _execValidator(FileUpload $uploadFile) : bool
    {
//        $this->_debug(['$this->_execValidator(upload)' => $uploadFile]);
        $this->debug(__FUNCTION__, __LINE__, [
            'uploadFile' => $uploadFile
        ]);
        // Validation - custom callback
        $count = count($this->_validators);
        for ( $i=0 ; $i<$count ; $i++ ) {
            $res = $this->_validators[$i]( $uploadFile );
            if ( is_string( $res ) ) {
                $this->_error = $res;
                return false;
            }
        }
        return true;
    }

    private function _dbExec(FileUpload $fileUpload)
    {
        $id = null;
//        $this->_debug(['$this->_dbExec(fileUpload)' => $fileUpload]);
        $this->debug(__FUNCTION__, __LINE__, [
            'fileUpload' => $fileUpload
        ]);
        $tableName = $this->getDbTable();
        $primaryKey = $this->getDbPKey();
        if($primaryKey){

            $sql = Sql::_inst($tableName);
            $sql->idColumn($primaryKey);
            $arrayColumnValue = [];
            foreach ( $this->_dbFields as $column => $prop ) {
                switch ( $prop ) {
                    case self::__READ_ONLY__:
                        break;

                    case self::__CONTENT__:
                        $arrayColumnValue[$column] = $fileUpload->getTmpName();
                        break;

                    case self::__CONTENT_TYPE__:

                    case self::__MIME_TYPE__:
                        break;

                    case self::__EXT__:
                        $arrayColumnValue[$column] = $fileUpload->getExtension();
                        break;

                    case self::__FILE_NAME__:
                        $arrayColumnValue[$column] = $fileUpload->getName();
                        break;

                    case self::__FILE_SIZE__:
                        $arrayColumnValue[$column] = $fileUpload->getSize();
                        break;

                    default:
                        if ( is_callable($prop) && is_object($prop) ) { // is a closure
                            $arrayColumnValue[$column] = $prop( $fileUpload );
                        }
                        $arrayColumnValue[$column] = $prop;
                        break;
                }
            }

            if($sql->insert($arrayColumnValue)){
                $resultRow = $sql->first();
                if(array_key_exists($primaryKey, $resultRow)){
                    $id = $resultRow[$primaryKey];
                }
            }
//            $this->_debug(['arrayColumnValue' => $arrayColumnValue, 'id' => $id]);
            $this->debug(__FUNCTION__, __LINE__, [
                'arrayColumnValue' => $arrayColumnValue,
                'id' => $id
            ]);


        }
        return $id;

    }

    /**
     * Execute the configured action for the upload
     *
     * @param int $id Primary key value
     * @param FileUpload $fileUpload
     * @return bool|int|string
     */
    private function _actionExec ( $id, $fileUpload )
    {
        $this->debug(__FUNCTION__, __LINE__, [
            'id' => $id,
            'fileUpload' => $fileUpload
        ]);
        if($fileUpload instanceof FileUpload && $id !== null){
            if ( ! is_string( $this->action ) && is_callable($this->action)) {
                // Custom function
                $action = $this->action;
                /** @var callable $action */
                return $action( $fileUpload, $id );
            }

            $fileAbsolutePath  = $this->_path( $fileUpload->getName(), $id );
            $fileWebPath = $this->_getWebPath($fileAbsolutePath);

            $fileFullPath = $this->_getFullPath($fileWebPath);
            $fileFullPath = str_replace("\\", "/", $fileFullPath);

            $dirName = dirname($fileFullPath);
            $dirName = str_replace("\\", "/", $dirName);

            $this->debug(__FUNCTION__, __LINE__, [
                "dirName" => $dirName,
                'fileAbsolutePath' => $fileAbsolutePath,
                'fileFullPath' => $fileFullPath,
                'fileWebPath' => $fileWebPath,
                'tmpName' => $fileUpload->getTmpName()
            ]);



            if( ! is_dir($dirName)){
                mkdir($dirName);
            }

            $fileMove = move_uploaded_file( $fileUpload->getTmpName(), $fileFullPath );
            $update = $this->_updateDb($fileWebPath, $id);

            $this->debug(__FUNCTION__, __LINE__,[
                "fileMove" => $fileMove,
                "update" => $update
            ]);
            if($fileMove === false || $update === false){
                $this->_error = "An error occurred while moving the uploaded file.";
                return false;
            }


            return $id !== false ?
                $id :
                $fileAbsolutePath;
        }
        return false;
    }

    private function _updateDb(string $fileAbsolutePath, $id){
        $update = false;
        $fileExist = File::_fileExist($fileAbsolutePath);
        $this->debug(__FUNCTION__, __LINE__,[
            "filePath" => $fileAbsolutePath,
            "id" => $id,
            "fileExist" => $fileExist
        ]);
        if($fileExist && $id){
            $tableName = $this->getDbTable();
            $primaryKey = $this->getDbPKey();
            $this->debug(__FUNCTION__, __LINE__,[
                "tableName" => $tableName,
                "primaryKey" => $primaryKey
            ]);
            if(is_string($tableName) && $tableName !== "" && is_string($primaryKey) && $primaryKey !== ""){
                $sql = Sql::_inst($tableName);
                $sql->equal($tableName, $primaryKey, $id);
                $arrayColumnValue = [];
                foreach ( $this->getDbFields() as $column => $prop ) {
                    switch ( $prop ) {
                        case self::__WEB_PATH__:
                            $arrayColumnValue[$column] = $fileAbsolutePath;
                            break;
                        case self::__ABSOLUTE_PATH__:
                            $arrayColumnValue[$column] = $fileAbsolutePath;
                            break;
                        case self::__SYSTEM_PATH__:
                            $arrayColumnValue[$column] = $this->_getFullPath($fileAbsolutePath);
                            break;
                    }
                }
                if(sizeof($arrayColumnValue) > 0){
                    $update = $sql->update($arrayColumnValue);
                }
                $this->debug(__FUNCTION__, __LINE__,[
                    "updateSql" => $sql
                ]);
            }
        }
        return $update;
    }

    private function getSql() : ? Sql
    {
//        $this->_debug(['$this->getSql()']);
        $sql = null;
        $tableName = $this->_dbTable;
        $primaryKey = $this->_dbPKey;
        $columnsValueCondition = $this->_columnsValuesCondition;
        if($tableName !== "" && $primaryKey !== ""){
            $sql = Sql::_inst($this->_dbTable);
            $sql->idColumn($this->_dbPKey);
            $arrayColumn = [];
            foreach ($this->_dbFields as $column => $property){
                array_push($arrayColumn, $column);
            }
            if( !in_array($primaryKey, $arrayColumn) ){
                array_push($arrayColumn, $primaryKey);

            }
            $sql->column($arrayColumn);
            foreach ($columnsValueCondition as $columnName => $value){
                $sql->equal($tableName, $columnName, $value);
            }
        }
        $this->debug(__FUNCTION__, __LINE__, [
            'sql' => $sql
        ]);
        return $sql;
    }

    public function getFiles() : array
    {
//        $this->_debug(['$this->getFiles()']);
        $tableName = $this->getDbTable();
        $primaryKey = $this->getDbPKey();
        $arrayFiles = [];
        if($tableName !== "" && $primaryKey !== ""){

            $sql = $this->getSql();
            if($sql instanceof Sql){
                if($sql->select()){
                    $arrayResult = $sql->result();
                    foreach ($arrayResult as $resultRow){
                        if(array_key_exists($primaryKey, $resultRow)){
                            $id = $resultRow[$primaryKey];
                            $arrayFiles[$id] = $resultRow;
                        }
                    }
                }
            }
        }
        $this->debug(__FUNCTION__, __LINE__, [
            'arrayFiles' => $arrayFiles
        ]);
        return $arrayFiles;
    }








    public function getError(){
        return $this->_error;
    }

    /**
     * Set a callback function that is used to remove files which no longer have
     * a reference in a source table.
     *
     * @param $tableField
     * @param  callable $callback Function that will be executed on clean. It is
     *     given an array of information from the database about the orphaned
     *     rows, and can return true to indicate that the rows should be
     *     removed from the database. Any other return value (including none)
     *     will result in the records being retained.
     * @return FieldUpload Current instance, used for chaining
     */
    public function _dbClean($tableField, $callback = null )
    {
        if ( $callback === null ) {
            $callback = $tableField;
            $tableField = null;
        }

        $this->_dbCleanCallback = $callback;
        $this->_dbCleanTableField = $tableField;
        $this->debug(__FUNCTION__, __LINE__, [
            'tableField' => $tableField,
            'callback' => $callback
        ]);

        return $this;
    }


    /**
     * Clean the database
     * @param  DataCard $dataCard Calling DataCard instance
     * @param  Field $field   Host field
     */
    public function _dbCleanExec ( $dataCard, $field )
    {

        //TODO
//        if($dataCard instanceof DataCard) {

            // Database and file system clean up BEFORE adding the new file to
            // the db, otherwise it will be removed immediately
//            $tables = $dataCard->table();
//            $this->_dbClean($tables[0], $field->dbField());
//            $this->debug(__FUNCTION__, __LINE__, [
//                'dataCard' => $dataCard,
//                'field' => $field
//            ]);
//        }
    }


    /**
     * Apply macros to a user specified path
     *
     * @param  string $name File path
     * @param  int $id Primary key value for the file
     * @return string Resolved path
     */
    private function _path ( $name, $id )
    {
        $ext = pathinfo( $name, PATHINFO_EXTENSION );

        $to = $this->action;
        $to = str_replace( self::__FILE_NAME__, $name, $to   );
        $to = str_replace( self::__ID__,   $id,   $to   );
        $to = str_replace( self::__EXT__, $ext, $to );

        return $to;
    }

    /**
     * Add an event listener. The `Editor` class will trigger an number of
     * events that some action can be taken on.
     *
     * @param  string $name     Event name
     * @param  callable $callback Callback function to execute when the event
     *     occurs
     * @return self Self for chaining.
     */
    public function on ( $name, $callback )
    {
        $this->debug(__FUNCTION__, __LINE__, [
            'name' => $name,
            'callback' => $callback
        ]);
        if(gettype($callback) === gettype(function (){})){
            if ( ! isset( $this->_events[ $name ] ) ) {
                $this->_events[ $name ] = array();
            }

            $this->_events[ $name ][] = $callback;
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDbTable()
    {
        return $this->_dbTable;
    }

    /**
     * @return null|string
     */
    private function getDbPKey()
    {
        return $this->_dbPKey;
    }

    /**
     * @return null
     */
    private function getDbFields()
    {
        return $this->_dbFields;
    }

    /**
     * @return null
     */
    private function getDbCleanCallback()
    {
        return $this->_dbCleanCallback;
    }

    /**
     * @return null
     */
    private function getDbCleanTableField()
    {
        return $this->_dbCleanTableField;
    }


    /**
     * Trigger an event
     *
     * @private
     * @param $eventName
     * @param null $arg1
     * @param null $arg2
     * @param null $arg3
     * @param null $arg4
     * @param null $arg5
     * @return mixed|null|void
     */
    private function _trigger ( $eventName, &$arg1=null, &$arg2=null, &$arg3=null, &$arg4=null, &$arg5=null )
    {
        $this->debug(__FUNCTION__, __LINE__, [
            'eventName' => $eventName
        ]);
        $out = null;
        $argc = func_num_args();
        $args = array( $this );

        // Hack to enable pass by reference with a "variable" number of parameters
        for ( $i=1 ; $i<$argc ; $i++ ) {
            $name = 'arg'.$i;
            $args[] = &$$name;
        }

        if ( ! isset( $this->_events[ $eventName ] ) ) {
            return;
        }

        $events = $this->_events[ $eventName ];

        for ( $i=0, $ien=count($events) ; $i<$ien ; $i++ ) {
            $res = call_user_func_array( $events[$i], $args );

            if ( $res !== null ) {
                $out = $res;
            }
        }

        return $out;
    }


    private function _getFullPath (string $fileAbsolutePath){
        $projectRoot = File::_getProjectRoot();
        return "$projectRoot$fileAbsolutePath";

    }



    private function _getWebPath (string $fileAbsolutePath){
        return$this->_webPathFolder.$fileAbsolutePath;

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
}