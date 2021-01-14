<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 26-10-19
 * Time: 01:31
 */

namespace salesteck\Email;

use salesteck\_base\Form_C;
use salesteck\utils\File;
use Soundasleep\Html2Text;

class EmailContent
{

    public const
        DIR_PATH = "/app/class/__Email/Template/",
        SECTION_PATH = self::DIR_PATH."section/",
        EMAIL_TEMPLATE_PATH = self::DIR_PATH."email_template.html",
        SECTION_BOOKING = self::SECTION_PATH."booking.html",
        SECTION_CONTACT = self::SECTION_PATH."contact.html",
        SECTION_SIGN_UP = self::SECTION_PATH."signup.html",
        SECTION_CLIENT_CONFIRM_SUBSCRIPTION = self::SECTION_PATH."client-confirm-subscription.html",
        SECTION_ORDER_INFO = self::SECTION_PATH."order-info.html",
        SECTION_ORDER_ITEM = self::SECTION_PATH."order-item.html",
        SECTION_SPACING = self::SECTION_PATH."spacing.html"
    ;


    public const OPERATION_RECOVERY = "OPERATION_RECOVERY";



    public const CID_LOGO = 'logoImg';

    private const
        ELEMENT_NAME = "{elementName}",
        START_STRING = "<!--".self::ELEMENT_NAME."-->",
        END_STRING = "<!--/".self::ELEMENT_NAME."-->"
    ;


    /**
     * return the html email template as string
     * @param string $filePath
     * @return bool|string
     */
    private static function _getTemplateContent(string $filePath = self::EMAIL_TEMPLATE_PATH) : string
    {
        return File::_fileGetContent($filePath);
    }

    /**
     * return the html email section as string
     * @param string $filePath
     * @return bool|string
     */
    private static function _getSection(string $filePath) : string
    {
        return File::_fileGetContent($filePath);
    }

    /**
     * return the html email template as string after inserting the chosen section
     * + additionally add the spacing section
     * @param array $filePaths
     * @return bool|mixed|string
     */
    protected static function _getEmailHtmlContentWithSection(array $filePaths = []) : string
    {
        $outputSection = "";
        foreach ($filePaths as $sectionPath){
            $spacing = self::_getSection(self::SECTION_SPACING);
            $section = self::_getSection($sectionPath);
            $outputSection = $outputSection.$spacing.$section;
        }

        $emailTemplate = self::_getTemplateContent();
        $replaced = str_replace('${section}', $outputSection, $emailTemplate);
        $emailTemplate = gettype($replaced) === gettype("") ? $replaced : $emailTemplate;
        $emailTemplate = self::_translateField($emailTemplate, self::_getFieldText());
        return $emailTemplate;
    }

    /**
     * return the html email template as string after replacing the appropriate string
     * + replace $key by $value
     * @param string $content
     * @param array $tagElements
     * @return string
     */
    private static function _replaceString(string $content, array $tagElements = []) : string
    {
        if($content !== ""){
            foreach ($tagElements as $key => $value){

                $replaced = str_replace($key, ucfirst($value), $content);
                $content = gettype($replaced) !== false ? $replaced : $content;
            }
        }
        return $content;
    }

    /**
     * return the html email template as string after replacing the appropriate string
     * + replace "{$key}" by $value
     * @param string $content
     * @param array $element
     * @return string
     */
    private static function _translateField(string $content, array $element = []) : string
    {
        $outPutElement = [];
        foreach ($element as $key => $value){
            $outPutElement[self::_getFieldTag($key)] = $value;
        }
        return self::_replaceString($content, $outPutElement);
    }

    /**
     * return the html email template as string after replacing the appropriate string
     * + replace "{$key}" by $value
     * @param string $content
     * @param array $element
     * @return string
     */
    public static function _replaceValue(string $content, array $element = []) : string
    {
        $outPutElement = [];
        foreach ($element as $key => $value){
            $outPutElement[self::_getValueTag($key)] = $value;
        }
        return self::_replaceString($content, $outPutElement);
    }

    private static function _getFieldTag(string $field){
        return '+{'.$field.'}';
    }

    private static function _getValueTag(string $value){
        return '${'.$value.'}';
    }

    /**
     * return the html email template as string after inserting the appropriate part
     * + insert $value between <!--$key--> & <!--/$key-->
     * @param string $emailContent
     * @param array $element
     * @return string
     */
    public static function _insertBetweenTag(string $emailContent, array $element = []) : string
    {
        if($emailContent !== ""){
            foreach ($element as $key => $value){
                $firstString = str_replace(self::ELEMENT_NAME, $key, self::START_STRING);
                $secondString = str_replace(self::ELEMENT_NAME, $key, self::END_STRING);
                $position1 = strpos($emailContent, $firstString);
                $position2 = strpos($emailContent, $secondString);
                if ($position1 !== false && $position2 !== false && $position1 !== $position2 && $position1 < $position2){

                    $position1 +=  strlen($firstString);

                    $emailContent =  substr_replace($emailContent, $value, $position1, 0);

                }
            }
        }
        return $emailContent;
    }

    /**
     * return the html email template as string after inserting the appropriate part
     * + insert $value between <!--$key--> & <!--/$key-->
     * @param string $emailContent
     * @param array $element
     * @return string
     */
    public static function _replaceBetweenTag(string $emailContent, array $element) : string
    {
        if($emailContent !== ""){
            $arrayKey = array_keys($element);
            $emailContent = self::_removeBetweenTag($emailContent, $arrayKey);
            $emailContent = self::_insertBetweenTag($emailContent, $element);
        }
        return $emailContent;
    }

    /**
     * @param string $emailContent
     * @param array $tagElements
     * @return string
     */
    public static function _removeBetweenTag(string $emailContent, array $tagElements = []) : string
    {
        if($emailContent !== ""){
            foreach ($tagElements as $key){
                $firstString = str_replace(self::ELEMENT_NAME, $key, self::START_STRING);
                $secondString = str_replace(self::ELEMENT_NAME, $key, self::END_STRING);
                $position1 = strpos($emailContent, $firstString);
                $position2 = strpos($emailContent, $secondString);
                if ($position1 !== false && $position2 !== false && $position1 !== $position2 && $position1 < $position2){
                    $position1 +=  strlen($firstString);

                    $start = substr($emailContent, 0, $position1);

                    $end = substr($emailContent, $position2);

                    $emailContent =  $start.$end;

                }
            }
        }
        return $emailContent;
    }

    /**
     * @param string $emailContent
     * @param string $tagElement
     * @return string
     */
    public static function _getBetweenTag(string $emailContent, string $tagElement) : string
    {
        $contentBetweenTag = "";
        if($emailContent !== ""){
            $firstString = str_replace(self::ELEMENT_NAME, $tagElement, self::START_STRING);
            $secondString = str_replace(self::ELEMENT_NAME, $tagElement, self::END_STRING);
            $startPosition = strpos($emailContent, $firstString);
            $endPosition = strpos($emailContent, $secondString);
            if ($startPosition !== false && $endPosition !== false && $startPosition !== $endPosition && $startPosition < $endPosition){
                $startPosition +=  strlen($firstString);
                $contentLength = $endPosition - $startPosition;
                $splitContent = substr($emailContent, $startPosition, $contentLength);
                if($splitContent !== false){
                    $contentBetweenTag = $splitContent;
                }
            }
        }
        return $contentBetweenTag;
    }

    public static function _getAltBody(string $emailContent){

        $options = array(
            'ignore_errors' => true,
            'drop_links' => true
        );
        return Html2Text::convert($emailContent, $options);
    }

    public static function _getHtmlContact(){
        return EmailContent::_getEmailHtmlContentWithSection([EmailContent::SECTION_CONTACT]);
    }

    public static function _getHtmlOrder(){
        return EmailContent::_getEmailHtmlContentWithSection([self::SECTION_ORDER_INFO, self::SECTION_ORDER_ITEM]);
    }

    public static function _getHtmlBooking(){
        return EmailContent::_getEmailHtmlContentWithSection([self::SECTION_BOOKING]);
    }

    public static function _getHtmlClientSubscription(){
        return EmailContent::_getEmailHtmlContentWithSection([self::SECTION_CLIENT_CONFIRM_SUBSCRIPTION]);
    }

    public static function _getHtmlQuotation(){
        return EmailContent::_getEmailHtmlContentWithSection([self::SECTION_CONTACT]);
    }

    public static function _getHtmlContent(){
        return EmailContent::_getEmailHtmlContentWithSection([
            self::SECTION_CONTACT,
            self::SECTION_CLIENT_CONFIRM_SUBSCRIPTION,
            self::SECTION_BOOKING,
            self::SECTION_ORDER_INFO,
            self::SECTION_ORDER_ITEM
        ]);
    }

    private static function _getFieldText(){
        return [
            Form_C::INDEX_SUBJECT => "sujet",
            Form_C::INDEX_REQUEST => "requête",
            Form_C::INDEX_INDUSTRY => "industrie",
            Form_C::INDEX_NAME => "nom",
            Form_C::INDEX_PHONE => "tél",
            Form_C::INDEX_DATE => "date",
            Form_C::INDEX_TIME => "heure",
            Form_C::INDEX_PEOPLE => "personne(s)",
            Form_C::INDEX_ADDRESS => "adresse",
            Form_C::INDEX_PAYMENT => "paiement",
            Form_C::INDEX_EMAIL => "email",
            Form_C::INDEX_QTY => "qté",
            Form_C::INDEX_CART_ELEMENT => "produit",
            Form_C::INDEX_PRICE => "prix",
            Form_C::INDEX_TOTAL => "total",
            Form_C::INDEX_COMPANY => "société",
            Form_C::INDEX_WEBSITE => "site web",
            Form_C::INDEX_SERVICE => "service",
            Form_C::INDEX_COUNTRY => "pays",
            Form_C::INDEX_MESSAGE => "message",
            Form_C::INDEX_COMMENT => "commentaire",
            'product' => "produit"
        ];
    }

    public static function _getHtmlButton(string $buttonText, string $url, string $hrefAttribute = ""){
        if($hrefAttribute !== ''){
            $hrefAttribute = "$hrefAttribute:";
        }
        return
            '<td width="150px" height="40px" bgcolor="#009688"
                style="margin:10px;-webkit-border-radius: 10px; -moz-border-radius: 10px; border-radius: 10px; color: #ffffff; display: inline-block">
                <a href="'.$hrefAttribute.$url.'"
                   style="text-align:center;font-size:16px; font-weight: bold; font-family: Helvetica, Arial, sans-serif; text-decoration: none; line-height:40px; width:100%; display:inline-block">
                    <span style="color: #FFFFFF">
                        '.ucfirst($buttonText).'
                    </span>
                </a>
            </td>';
    }

    public static function _addButtons(string $emailContent, array $buttons){
        $button = implode("", $buttons);
        $emailContent = self::_insertBetweenTag($emailContent, ["button"=>$button]);
        return $emailContent;
    }

    private static function _getHtmlInfoBox(string $boxTitle){
        return
            '<td width="200px" height="40px"
                style="margin-top:20px;margin-bottom:20px;text-align:center;border:2px solid #009688;-webkit-border-radius: 10px; -moz-border-radius: 10px; border-radius: 10px; color: #009688; display: block">
                    <span style="font-size:16px; font-weight: bold; font-family: Helvetica, Arial, sans-serif; text-decoration: none; line-height:40px; width:100%; display:inline-block">
                        <span style="color: #009688;margin: auto">
                            '.$boxTitle.'
                        </span>
                    </span>
            </td>';
    }

    public static function _addInfoBox(string $emailContent, string $boxTitle){
        $infoBox = self::_getHtmlInfoBox($boxTitle);
        return self::_insertBetweenTag($emailContent, ["info-box"=>$infoBox]);
    }

}