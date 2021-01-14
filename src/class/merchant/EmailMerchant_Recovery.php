<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 29-10-20
 * Time: 22:47
 */
namespace salesteck\merchant;

use PHPMailer\PHPMailer\PHPMailer;
use salesteck\_base\Form_C;
use salesteck\_base\Page_C;
use salesteck\config\Config;
use salesteck\Email\EmailContent;
use salesteck\Email\EmailSmtp;
use salesteck\security\MCrypt;
use stdClass;

class EmailMerchant_Recovery extends EmailContent
{
    private static function _getEmailContent(){
        $emailContent =  self::_getEmailHtmlContentWithSection([self::SECTION_SIGN_UP]);
//        $emailContent = self::_removeBetweenTag($emailContent, [
//            Form_Controller::INDEX_COMPANY,
//            Form_Controller::INDEX_WEBSITE,
//            Form_Controller::INDEX_SERVICE,
//            Form_Controller::INDEX_INDUSTRY,
//            Form_Controller::INDEX_COUNTRY
//        ]);
        return $emailContent;
    }
    private static function _getSubject(){
        $subject = "Récupérer votre mot de passe sur Storkoo.be";

        return $subject;
    }

    private static function _replaceInfo(string $emailContent = "", string $name, string $email, string $phone){
        $emailContent = self::_replaceValue($emailContent, [
            'title' => self::_getSubject(),
            Form_C::INDEX_NAME => $name,
            Form_C::INDEX_EMAIL => $email,
            Form_C::INDEX_PHONE => $phone
        ]);
        return $emailContent;
    }

    private static function _insertButtons(string $emailContent = "", string $idCode){
        $emailContent = $emailContent !== "" ? $emailContent : self::_getEmailContent();

        $object = new stdClass();
        $object->idCode = $idCode;
        $operation = Merchant::OPERATION;
        $object->$operation = Merchant::OPE_RECOVERY;

        $object->className = Merchant::class;
        $jsonString = json_encode($object);
        $requestUrl = Config::_getRootAddress().Page_C::_getPageLink(Page_C::pageSignUp);
        $url = MCrypt::url_encode($jsonString);
        $url = "$requestUrl?q=$url";
        $confirmButton = self::_getHtmlButton("Récupérer", $url);

        $emailContent = self::_replaceBetweenTag($emailContent, [
            "button" =>$confirmButton
        ]);
        return $emailContent;
    }

    private static function _getContactEmailContent(string $customerIdCode, string $name, string $email, string $phone){
        $emailContent = self::_getEmailContent();
        $emailContent = self::_replaceInfo($emailContent, $name, $email, $phone);
        $emailContent = self::_insertButtons($emailContent, $customerIdCode);
        return $emailContent;
    }

    public static function _getEmail(string $customerIdCode, string $name, string $email, string $phone) : PHPMailer
    {
        $emailSender = new EmailSmtp();
        $emailSubject = self::_getSubject();
        $emailContent = self::_getContactEmailContent($customerIdCode, $name, $email, $phone);
        $emailSender->Subject = $emailSubject;
        $emailSender->msgHTML($emailContent);
        $emailSender->AltBody = EmailContent::_getAltBody($emailContent);
        return $emailSender;
    }

}