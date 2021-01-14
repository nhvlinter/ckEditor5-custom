<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 26-09-19
 * Time: 15:41
 */

namespace salesteck\utils;

/**
 * Class File
 * @package salesteck\utils
 */
class File implements \JsonSerializable
{
    public const PHP_EXT = ".php";

    private $dirName, $dirLabel, $baseName, $extension, $fileName, $path;

    public const
        FILE_DELETED = 1,
        FILE_NOT_FOUND = 0,
        ERROR = -1
    ;

    public const
        KO = 1000,
        MO = self::KO * self::KO,
        GO = self::MO * self::KO
    ;



    /**
     * File constructor.
     * @param string $file
     * @internal param string $dirName
     * @internal param string $baseName
     * @internal param string $extension
     * @internal param string $fileName
     */
    public function __construct(string $file)
    {
        $this->path = $file;
        $pathInfo = pathinfo($file);

        $this->dirName = $pathInfo["dirname"];
        $fullDir = $pathInfo["dirname"];


        $this->dirLabel = str_replace(dirname($pathInfo["dirname"]).'/', "", $fullDir);
        $this->baseName = $pathInfo["basename"];
        $this->extension = $pathInfo["extension"];
        $this->fileName = $pathInfo["filename"];
    }

    /**
     * @return string
     */
    public function getDirName() :string
    {
        return $this->dirName;
    }

    /**
     * @return string
     */
    public function getDirLabel() :string
    {
        return $this->dirLabel;
    }

    /**
     * @return string
     */
    public function getBaseName() :string
    {
        return $this->baseName;
    }

    /**
     * @return string
     */
    public function getExtension() :string
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getFileName() :string
    {
        return $this->fileName;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
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


    /**
     * include file if asset exists
     * @param string $absolutePath
     * @param array $param
     * @param bool $debug
     * @return bool
     */
    public static function _includeFile(string $absolutePath, array $param = [], $debug = false) : bool
    {
        Debug::_trace();
        Debug::_exposeVariableHtml(['absolutePath'=> self::_getFileFullPath($absolutePath)], $debug);
        if(self::_fileExist($absolutePath)){
            extract($param);
            return include_once self::_getFileFullPath($absolutePath);
        }else{
            Debug::_traceHtml($debug);
            return false;
        }
    }

    /**
     * include file if asset exists
     * @param string $absolutePath
     * @param bool $debug
     * @return bool|string
     */
    public static function _fileGetContent(string $absolutePath, bool $debug = false)
    {
        Debug::_trace();
        $arrayDebug = [];
        $arrayDebug["absolutePath"] = $absolutePath;

        $content = "";

        $fullPath = self::_getFileFullPath($absolutePath);
        $arrayDebug["fullPath"] = $fullPath;

        $fileExist = self::_fileExist($absolutePath);
        $arrayDebug["fileExist"] = $fileExist;

        if($fileExist){
            $content = file_get_contents($fullPath);
            $arrayDebug["content"] = $content;
        }

        Debug::_exposeVariableHtml($arrayDebug);
        Debug::_exposeVariable($arrayDebug, $debug);
        return $content;
    }

    public static function _getFileFullPath(string $absolutePath, bool $debug = false) : string
    {
        $arrayDebug = [];
        $arrayDebug["filePath"] = $absolutePath;

        $serverRoot = self::_getServerRoot();
        $serverRoot = str_replace("\\", "/",$serverRoot);
        $arrayDebug["serverRoot"] = $serverRoot;

        $absolutePath = str_replace("\\", "/", $absolutePath);
        $absolutePath = str_replace($serverRoot, "", $absolutePath);
        $arrayDebug["filePath"] = $absolutePath;

        $returnPath = dirname(dirname(dirname(__DIR__))).$absolutePath;
        $returnPath = str_replace("\\", "/",$returnPath);
        $arrayDebug["returnPath"] = $returnPath;
        Debug::_exposeVariableHtml($arrayDebug, $debug);
        return ($returnPath) ;
    }

    public static function _getFileProjectFullPath(string $absolutePath, bool $debug = false) : string
    {
        $arrayDebug = [];
        $arrayDebug["filePath"] = $absolutePath;

        $projectRoot = self::_getProjectRoot();
        $projectRoot = str_replace("\\", "/",$projectRoot);
        $arrayDebug["projectRoot"] = $projectRoot;

        $absolutePath = str_replace("\\", "/", $absolutePath);
        $absolutePath = str_replace($projectRoot, "", $absolutePath);
        $arrayDebug["filePath"] = $projectRoot;

        $returnPath = dirname(dirname(dirname(__DIR__))).$absolutePath;
        $returnPath = str_replace("\\", "/",$returnPath);
        $arrayDebug["returnPath"] = $returnPath;
        Debug::_exposeVariableHtml($arrayDebug, $debug);
        return ($returnPath) ;
    }

    public static function _getAbsolutePath(string $filePath, bool $debug = false){

        $arrayDebug = [];
        $arrayDebug["filePath"] = $filePath;

        $serverRoot = self::_getServerRoot();
        $arrayDebug["server"] = $_SERVER;
        $serverRoot = str_replace("\\", "/",$serverRoot);
        $arrayDebug["serverRoot"] = $serverRoot;

        $filePath = str_replace("\\", "/",$filePath);
        $returnPath = str_replace($serverRoot, "", $filePath);

        Debug::_exposeVariableHtml($arrayDebug, $debug);
        return $returnPath ;
    }

    public static function _getProjectAbsolutePath(string $filePath, bool $debug = false){

        $arrayDebug = [];
        $arrayDebug["filePath"] = $filePath;

        $serverRoot = self::_getProjectRoot();
        $arrayDebug["server"] = $_SERVER;
        $serverRoot = str_replace("\\", "/",$serverRoot);
        $arrayDebug["serverRoot"] = $serverRoot;

        $filePath = str_replace("\\", "/",$filePath);
        $returnPath = str_replace($serverRoot, "", $filePath);

        Debug::_exposeVariableHtml($arrayDebug, $debug);
        return $returnPath ;
    }

    public static function _getDirNameAbsolutePath(string $__FILE__){
        return dirname(self::_getAbsolutePath($__FILE__));
    }

    /**
     * @param string $filePath
     * @return bool
     */
    public static function _fileExist(string $filePath) : bool
    {
        Debug::_trace();
        if($filePath !== ""){
            return file_exists(self::_getFileFullPath($filePath));
        }
        return false;
    }


    public static function _deleteFile(string $absolutePath) : int
    {
        $fileFullPath = self::_getFileFullPath($absolutePath);
        if(file_exists($fileFullPath)){

            if(unlink($fileFullPath)){
                return self::FILE_DELETED;
            }
            else{
                return self::ERROR;
            }
        }else{
            return self::FILE_NOT_FOUND;

        }
    }

    public static function _createFile($absolutePath)
    {
        if($absolutePath !== "" && ! self::_folderExist($absolutePath)){
            fopen(File::_getFileFullPath($absolutePath), "w");
        }
    }

    public static function _createDir($absolutePath){
        if($absolutePath !== "" && ! self::_fileExist($absolutePath)){
            $filePath = File::_getFileFullPath($absolutePath);
            if( is_dir($filePath) === false )
            {
                mkdir($filePath);
                return true;
            }
        }
        return false;

    }

    public static function _removeDir($absolutePath){
        if($absolutePath !== "" && self::_folderExist($absolutePath)){
            $filePath = File::_getFileFullPath($absolutePath);
            if( is_dir($filePath) )
            {
                rmdir($filePath);
                return true;
            }
        }
        return false;

    }

    public static function _folderExist(string $folderPath) : bool
    {

        $exist = false;
        $arrayDebug = [];
        $arrayDebug["folderPath"] = $folderPath;
        Debug::_trace();
        if($folderPath !== ""){

            $path = self::_getFileFullPath($folderPath);
            $arrayDebug["path"] = $path;
            $exist =  is_dir(self::_getFileFullPath($folderPath));
        }
        $arrayDebug["exist"] = $exist;
        Debug::_exposeVariableHtml($arrayDebug);
        return $exist;
    }

    public static function _getProjectRoot() : string
    {
        return dirname(dirname(dirname(__DIR__)));
    }


    public static function _getServerRoot() : string
    {
        $documentRoot =  str_replace("/", "\\", $_SERVER["DOCUMENT_ROOT"]);
        return $documentRoot;
    }

    public static function _getFolderElements(string $absolutePath) : array
    {
        $root = File::_getProjectRoot();
        return scandir($root.$absolutePath, 0);
    }

    public static function _getFilesFromFolder(string $absolutePath) : array
    {
        return array_diff(self::_getFolderElements($absolutePath), array('..', '.'));
    }

    public static function _getFileName(string $file) :string
    {
        $path_parts = pathinfo($file);
        return $path_parts["filename"];
    }


    public static function _getUri(){
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
            $link = "https";
        }else{
            $link = "http";
        }
// Here append the common URL characters.
        $link .= "://";

// Append the host(domain name, ip) to the URL.
        $link .= $_SERVER['HTTP_HOST'];

// Append the requested resource location to the URL
        $link .= $_SERVER['REQUEST_URI'];

// Print the link
        return $link;
    }


    public static function _getQueryString(){
        return $_SERVER['QUERY_STRING'];
    }


    public static function _setContent(string $absolutePath, string $fileContent)
    {
        Debug::_trace();
        $arrayDebug = [];
        $arrayDebug["absolutePath"] = $absolutePath;

        $content = "";

        $fullPath = self::_getFileFullPath($absolutePath);
        $arrayDebug["fullPath"] = $fullPath;

        $fileExist = self::_fileExist($absolutePath);
        $arrayDebug["fileExist"] = $fileExist;

        if($fileExist){
            $content = file_put_contents($fullPath, $fileContent);
            $arrayDebug["content"] = $content;
        }

        Debug::_exposeVariableHtml($arrayDebug);
        Debug::_exposeVariable($arrayDebug, false);
        return $content !== false;

    }


}