<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 11-07-20
 * Time: 16:29
 */

namespace salesteck\DataCard;


use salesteck\Db\Sql;
use salesteck\utils\File;
use salesteck\utils\FileUpload;

class Upload implements \JsonSerializable
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
    private $_dbCleanCallback = null;
    private $_dbCleanTableField = null;
    private $_validators = [];

    /** @var array */
    private $_events = [];

    /** @var array */
    private $_debug = [];


    public static function _inst( $action=null) : self
    {
        return new self($action);
    }

    private function _debug(array $var){
        array_push($this->_debug, $var);
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
     */
    public function __construct($action)
    {
        $this->_debug(['__construct($action)' => $action]);
        if ( $action  && ( is_string($action) || is_callable($action))) {
            $this->action( $action );
        }
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
        $this->_debug(['$this->action($action)' => $action]);

        return $this;
    }







    /**
     * Database configuration method. When used, this method will tell Editor
     * what information you want written to a database on file upload, should
     * you wish to store relational information about your file on the database
     * (this is generally recommended).
     *
     * @param  string $tableName  The name of the table where the file information
     *     should be stored
     * @param  string $columnId   Primary key column name. The `Upload` class
     *     requires that the database table have a single primary key so each
     *     row can be uniquely identified.
     * @param  array $columnProperties A list of the fields to be written to on upload.
     *     The property names are the database columns and the values can be
     *     defined by the constants of this class. The value can also be a
     *     string or a closure function if you wish to send custom information
     *     to the database.
     * @return self Current instance, used for chaining
     */
    public function db( string $tableName = "", string $columnId = "", array $columnProperties = [])
    {
        $this->_dbTable = $tableName;
        $this->_dbPKey = $columnId;
        $this->_dbFields = $columnProperties;
        $this->_debug(['$this->db(tableName, $columnId, $columnProperties)' => [$tableName, $columnId, $columnProperties]]);


        return $this;
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
     * @return Upload Current instance, used for chaining
     */
    public function _dbClean($tableField, $callback = null )
    {
        // Argument swapping
        if ( $callback === null ) {
            $callback = $tableField;
            $tableField = null;
        }

        $this->_dbCleanCallback = $callback;
        $this->_dbCleanTableField = $tableField;
        $this->_debug(['$this->_dbClean(tableField, callback)' => [$tableField, $tableField]]);

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
        // Database and file system clean up BEFORE adding the new file to
        // the db, otherwise it will be removed immediately
//        $tables = $dataCard->table();
//        $this->_dbClean( $tables[0], $field->dbField() );
//        $this->_debug(['$this->dbCleanExec($dataCard, $field)' => [$dataCard, $field]]);
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
        $this->_debug(['$this->validator($fn)' => $fn]);

        return $this;
    }

    public function exec(){
        $id = null;
        $uploadFile = null;
        $this->_debug(['$this->exec()']);

        $uploadFile = FileUpload::_inst(self::INDEX_UPLOAD);

        if($uploadFile instanceof FileUpload){
            $error = $uploadFile->getError();
            if( $error !== UPLOAD_ERR_OK){

                if ( $error === UPLOAD_ERR_INI_SIZE ) {
                    $this->_error = "File exceeds maximum file upload size";
                }
                else {
                    $uploadError = $uploadFile->getError();
                    $this->_error = "There was an error uploading the file (".$uploadError.")";
                }
                return null;
            }
            $validators = $this->_execValidator($uploadFile);
            if( !$validators ){
                return null;
            }

            $trigger = $this->_trigger( self::preUpload);
            if($trigger !== false){
                $id = $this->_dbExec( $uploadFile );
                return $this->_actionExec( $id, $uploadFile);
            }

        }
        return false;
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

        $this->_debug(['$this->_actionExec($id, $fileUpload)' => [$id, $fileUpload]]);
        if($fileUpload instanceof FileUpload && $id !== null){
            if ( ! is_string( $this->action ) && is_callable($this->action)) {
                // Custom function
                $action = $this->action;
                /** @var callable $action */
                return $action( $fileUpload, $id );
            }

            $filePath  = $this->_path( $fileUpload->getName(), $id );
            $filePath  = File::_getFileFullPath($filePath);
            $this->_debug(['filePath' => $filePath, 'tmpName' => $fileUpload->getTmpName()]);
            $fileMove = move_uploaded_file( $fileUpload->getTmpName(), $filePath );

            if($fileMove === false || $this->_updateDb($filePath, $id) === false){
                $this->_error = "An error occurred while moving the uploaded file.";
                return false;
            }


            return $id !== null ?
                $id :
                $filePath;
        }
        return null;
    }

    private function _updateDb(string $filePath, $id){
        $this->_debug(['$this->_updateDb(filePath)' => $filePath]);
        if(File::_fileExist($filePath) && $id){
            $tableName = $this->getDbTable();
            $primaryKey = $this->getDbPKey();
            if(is_string($tableName) && $tableName !== "" && is_string($primaryKey) && $primaryKey !== ""){
                $sql = Sql::_inst($tableName);
                $sql->equal($tableName, $primaryKey, $id);
                $arrayColumnValue = [];
                foreach ( $this->getDbFields() as $column => $prop ) {
                    switch ( $prop ) {
                        case self::__WEB_PATH__:
                            $arrayColumnValue[$column] = File::_getAbsolutePath($filePath);
                            break;
                        case self::__SYSTEM_PATH__:
                            $arrayColumnValue[$column] = $filePath;
                            break;
                    }
                }
                if(sizeof($arrayColumnValue) > 0){
                    return
                        $sql->update($arrayColumnValue);
                }
            }
        }
        return false;
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
        if(gettype($callback) === gettype(function (){})){
            if ( ! isset( $this->_events[ $name ] ) ) {
                $this->_events[ $name ] = array();
            }

            $this->_events[ $name ][] = $callback;
        }

        return $this;
    }

    private function _execValidator(FileUpload $upload) : bool
    {
        $this->_debug(['$this->_execValidator(upload)' => $upload]);
        // Validation - custom callback
        for ( $i=0, $ien=count($this->_validators) ; $i<$ien ; $i++ ) {
            $res = $this->_validators[$i]( $upload );

            if ( is_string( $res ) ) {
                $this->_error = $res;
                return false;
            }
        }
        return true;
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

    private function getSql() : ? Sql
    {
        $this->_debug(['$this->getSql()']);
        $sql = null;
        $tableName = $this->_dbTable;
        $primaryKey = $this->_dbPKey;
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
        }
        return $sql;
    }

    private function _dbExec(FileUpload $fileUpload)
    {
        $id = null;
        $this->_debug(['$this->_dbExec(fileUpload)' => $fileUpload]);
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
            $this->_debug(['arrayColumnValue' => $arrayColumnValue, 'id' => $id]);


        }
        return $id;

    }

    public function getFiles() : array
    {
        $this->_debug(['$this->getFiles()']);
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
        return $arrayFiles;
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