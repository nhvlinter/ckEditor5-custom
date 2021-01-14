<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 09-03-20
 * Time: 20:59
 */
namespace salesteck\config;
use salesteck\api\RequestResponse;
use salesteck\_base\Language_C;
use salesteck\_base\Page_C;
use salesteck\utils\File;

/**
 * Class Config
 * @package salesteck\config
 */
class Config
{

    public const
        DbName = "_salesteck",
        UserName = "shouny",
        Password = "Son07031987",
        DefaultCollation = "utf8_bin",
        DefaultCharset = "utf8",
        Engine = "MyISAM",
        ID_LENGTH = 6,
        _TRANSLATION = "_translation",
        _col = "_col",
        homePage = Page_C::pageHome,
        defaultLanguage = Language_C::_FR
    ;
    public const ASSET_VERSION = "?v=1.0";

    public const EMAIL_ORDER = "info@salesteck.com";

    public const
        IS_DEMO = false,
        IS_WEB_ACTIVE = true,
        DEBUG = false,
        ServerDbName = "admin_salesteck",
        ServerDemoDbName = "admin_salesteck_demo",
        ServerUser = "son.nguyen",
        ServerDemoUser = "son.nguyen",
        ServerPassword = "Son07031987*"
    ;

    public const
        EMAIL_INFO = "info@salesteck.com",
        EMAIL_NO_REPLY = "noreply@salesteck.com",
        EMAIL_NO_REPLY_PASSWORD = "Son07031987"
    ;

    /**
     * @return bool
     */
    public static function _isDebug(){
        return self::_isLocal() || self::DEBUG;
    }

    /**
     * @return string
     */
    public static function _getDbName():string
    {
        if(self::_isOnDemoWeb()){
            return self::ServerDemoDbName;
        }
        return self::_isLocal() ? self::DbName : self::ServerDbName;
    }

    /**
     * @return string
     */
    public static function _getUserName():string
    {
        if(self::_isOnDemoWeb()){
            return self::ServerDemoUser;
        }
        return self::_isLocal() ? self::UserName : self::ServerUser;
    }

    /**
     * @return string
     */
    public static function _getDbPassword():string
    {
        return self::_isLocal() ? self::Password : self::ServerPassword;
    }

    /**
     * @return string
     */
    public static function _getRootAddress(){
        return self::_isLocal() ? 'http://localhost' : self::_getWebRoot();
    }

    public static function _getWebRoot(){
        return "https://salesteck.com";
    }

    /**
     * @param bool $debug
     */
    public static function _displayError(bool $debug = self::DEBUG){

        if($debug || self::_isDebug()){
            ini_set('display_errors',1);
        }
    }

    /**
     * @param int $headerCode
     */
    public static function header(int $headerCode){
        header("HTTP/2.0 $headerCode");
    }

    /**
     *
     */
    public static function header200(){
        self::header(RequestResponse::HTTP_OK_200);
    }

    /**
     *
     */
    public static function header404(){
        self::header(RequestResponse::HTTP_NOT_FOUND_404);
    }

    /**
     * @return int
     */
    public static function _getMaxUploadSize() : int
    {
        $maxPostSize = self::_getMaxPostSize();
        $maxUploadSize = (int) str_replace('M', '', ini_get('upload_max_filesize'));
        $maxUploadSize = self::megaOctetToOctet($maxUploadSize);

        return min($maxUploadSize, $maxPostSize);

    }

    /**
     * @return int
     */
    public static function _getPostSize()
    {
        $postSize = -1;

        if(array_key_exists('CONTENT_LENGTH', $_SERVER)){
            $postSize = (int) $_SERVER['CONTENT_LENGTH'];
        }
        return $postSize;
    }

    /**
     * @return int
     */
    public static function _getMaxPostSize() : int
    {
        $maxPostSize = (int) str_replace('M', '', ini_get('post_max_size'));
        return self::megaOctetToOctet($maxPostSize);

    }

    /**
     * @param int $megaOctetSize
     *
     * @return int
     */
    private static function megaOctetToOctet(int $megaOctetSize)
    {
        return $megaOctetSize * File::MO;
    }

    /**
     * @param int $octetSize
     *
     * @return string
     */
    public static function octetToString(int $octetSize){
        if($octetSize >= File::GO){
            return strval( (float)($octetSize  / File::GO )).' go';
        }elseif ($octetSize >= File::MO){
            return strval( (float)($octetSize  / File::MO )).' mo';
        }elseif ($octetSize >= File::KO){
            return strval( (float)($octetSize  / File::KO )).' ko';
        }else{
            return "$octetSize octet";
        }
    }

    /**
     * @param string $link
     */
    public static function redirect(string $link = "/"){
        header("Location:$link");
        exit();
    }




    public static function _isLocal (){
        return strpos($_SERVER['HTTP_HOST'], "localhost") !== false;
    }


    public static function _isOnDemoWeb(){
        if(self::IS_DEMO){
            return true;
        }
        $uri = File::_getUri();
        return strpos($uri, "https://demo.salesteck.com");
    }
}