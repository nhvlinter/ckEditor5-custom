<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 18-05-20
 * Time: 16:57
 */

namespace salesteck\Email;


use PHPMailer\PHPMailer\PHPMailer;
use salesteck\_base\Form_C;

class EmailContact extends EmailContent
{
    private static function _getEmailContent(){
        $emailContent =  self::_getEmailHtmlContentWithSection([self::SECTION_CONTACT]);
        $emailContent = self::_removeBetweenTag($emailContent, [
            Form_C::INDEX_COMPANY,
            Form_C::INDEX_WEBSITE,
            Form_C::INDEX_SERVICE,
            Form_C::INDEX_INDUSTRY,
            Form_C::INDEX_COUNTRY
        ]);
        return $emailContent;
    }
    private static function _getSubject(){
        $subject = "Vous avez une nouvelle demande de contact";

        return $subject;
    }

    private static function _replaceInfo(string $emailContent = "", string $name, string $email, string $phone, string $subject, string $message){
        $emailContent = self::_replaceValue($emailContent, [
            'title' => self::_getSubject(),
            Form_C::INDEX_NAME => $name,
            Form_C::INDEX_EMAIL => $email,
            Form_C::INDEX_PHONE => $phone,
            Form_C::INDEX_SUBJECT => $subject,
            Form_C::INDEX_MESSAGE => $message
        ]);
        return $emailContent;
    }

    private static function _insertButtons(string $emailContent = ""){
        $emailContent = $emailContent !== "" ? $emailContent : self::_getEmailContent();
        $confirmButton = self::_getHtmlButton("Traiter", '');

        $emailContent = self::_replaceBetweenTag($emailContent, [
            "processButton" =>"$confirmButton"
        ]);
        return $emailContent;
    }

    private static function _getContactEmailContent(string $name, string $email, string $phone, string $subject, string $message){
        $emailContent = self::_getEmailContent();
        $emailContent = self::_replaceInfo($emailContent, $name, $email, $phone, $subject, $message);
        $emailContent = self::_insertButtons($emailContent);
        return $emailContent;
    }

    public static function _getEmail(string $name, string $email, string $phone, string $subject, string $message) : PHPMailer
    {
        $emailSender = new EmailSmtp();
        $emailSubject = self::_getSubject();
        $emailContent = self::_getContactEmailContent($name, $email, $phone, $subject, $message);
        $emailSender->Subject = $emailSubject;
        $emailSender->msgHTML($emailContent);
        $emailSender->AltBody = EmailContent::_getAltBody($emailContent);
        return $emailSender;
    }

}