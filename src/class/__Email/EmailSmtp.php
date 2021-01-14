<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 14-05-20
 * Time: 13:36
 */

namespace salesteck\Email;


use Content\Image;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use salesteck\config\Config;
use salesteck\utils\CustomDateTime;
use salesteck\utils\File;

date_default_timezone_set(CustomDateTime::DEFAULT_TIME_ZONE);

class EmailSmtp extends PHPMailer implements \JsonSerializable
{
    private const debug = false;

    /**
     * EmailSmtp constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->isSMTP();

        $this->SMTPDebug = SMTP::DEBUG_OFF;
        if(self::debug){
            $this->SMTPDebug = SMTP::DEBUG_SERVER;
        }
        $this->isSMTP();

        //Set the encryption mechanism to use - STARTTLS or SMTPS
        //Whether to use SMTP authentication
        $this->SMTPAuth = true;
        //Set AuthType to use XOAUTH2
//        $this->AuthType = 'XOAUTH2';

        $this->CharSet = PHPMailer::CHARSET_UTF8;
        $this->addEmbeddedImage(File::_getFileFullPath(Image::LOGO_PATH), EmailContent::CID_LOGO);

        $this->Host = "smtp.zoho.eu";
        $this->Port = "465";

        $this->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->setFrom(Config::EMAIL_NO_REPLY, "no-reply Storkoo");

        $this->SMTPAuth   = true;                                   // Enable SMTP authentication
        $this->Username   = Config::EMAIL_NO_REPLY;                     // SMTP username
        $this->Password   = Config::EMAIL_NO_REPLY_PASSWORD;

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