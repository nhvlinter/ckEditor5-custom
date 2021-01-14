<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 12-05-20
 * Time: 14:28
 */

namespace salesteck\Email;


use PHPMailer\PHPMailer\PHPMailer;
use salesteck\_base\Form_C;
use salesteck\_base\Page_C;
use salesteck\config\Config;
use salesteck\order\Order_C;
use salesteck\order\OrderElement;
use salesteck\order\OrderPromotion;
use salesteck\order\OrderTranslation;
use salesteck\utils\CustomDateTime;

class EmailOrder extends EmailContent
{
    private static function _getEmailContent(){
        $emailContent =  self::_getEmailHtmlContentWithSection([self::SECTION_ORDER_INFO, self::SECTION_ORDER_ITEM]);
        return $emailContent;
    }

    private static function _getTitle(OrderTranslation $orderTranslation){
        $title = "";
        if ($orderTranslation !== null && $orderTranslation instanceof OrderTranslation){

            if ($orderTranslation !== null && $orderTranslation instanceof OrderTranslation){
                $language = $orderTranslation->getLanguage();
                $startTimeStamp = $orderTranslation->getStartTime();
                $dayIndex = CustomDateTime::_timeStampToFormat($startTimeStamp, CustomDateTime::F_DAY_OF_WEEK_MON_SUN);
                $dayString = CustomDateTime::_getDayTranslation($language, $dayIndex);
                $date = CustomDateTime::_timeStampToFormat($startTimeStamp, CustomDateTime::F_DATE_DAY_MONTH);
                $startTime = CustomDateTime::_timeStampToFormat($startTimeStamp, CustomDateTime::F_HOUR_MINUTE);
                $endTime = CustomDateTime::_timeStampToFormat($orderTranslation->getEndTime(), CustomDateTime::F_HOUR_MINUTE);
                $orderIdCode = $orderTranslation->getIdCode();
                $orderTypeString = $orderTranslation->getTypeString();
                $title =
                    "Commande : '$orderIdCode' - $orderTypeString  <br> $dayString $date à $startTime - $endTime";
            }
        }
        return $title;
    }

    private static function _getSubject(OrderTranslation $orderTranslation, int $orderStatus){
        $subject = "";
        if ($orderTranslation !== null && $orderTranslation instanceof OrderTranslation){
            $language = $orderTranslation->getLanguage();
            $startTimeStamp = $orderTranslation->getStartTime();
            $dayIndex = CustomDateTime::_timeStampToFormat($startTimeStamp, CustomDateTime::F_DAY_OF_WEEK_MON_SUN);
            $dayString = CustomDateTime::_getDayTranslation($language, $dayIndex);
            $date = CustomDateTime::_timeStampToFormat($startTimeStamp, CustomDateTime::F_DATE_DAY_MONTH);
            $startTime = CustomDateTime::_timeStampToFormat($startTimeStamp, CustomDateTime::F_HOUR_MINUTE);
            $orderIdCode = $orderTranslation->getIdCode();
            $orderTypeString = $orderTranslation->getTypeString();
            switch ($orderStatus){
                case Order_C::STATUS_CONFIRMED:
                    $subject =
                        "Votre commande a éte confirmée : '$orderIdCode' pour le $dayString $date à $startTime";
                    break;
                case Order_C::STATUS_DENIED:
                    $subject =
                        "Votre commande a éte déclinée : '$orderIdCode' - $orderTypeString $dayString $date à $startTime";
                    break;
                default :
                    $subject =
                        "Commande : '$orderIdCode' - $orderTypeString $dayString $date à $startTime";
            }
        }
        return $subject;
    }

    private static function _replaceOrderInfo(OrderTranslation $orderTranslation, string $emailContent = "", int $orderStatus = Order_C::STATUS_SEND) :string
    {
        $emailContent = $emailContent !== "" ? $emailContent : self::_getEmailContent();
        if ($orderTranslation !== null && $orderTranslation instanceof OrderTranslation){
            $title = self::_getTitle($orderTranslation);
            $language = $orderTranslation->getLanguage();
            $startTimeStamp = $orderTranslation->getStartTime();
            $dayIndex = CustomDateTime::_timeStampToFormat($startTimeStamp, CustomDateTime::F_DAY_OF_WEEK_MON_SUN);
            $dayString = CustomDateTime::_getDayTranslation($language, $dayIndex);
            $date = CustomDateTime::_timeStampToFormat($startTimeStamp, CustomDateTime::F_DATE_DAY_MONTH);
            $startTime = CustomDateTime::_timeStampToFormat($startTimeStamp, CustomDateTime::F_HOUR_MINUTE);
            $endTime = CustomDateTime::_timeStampToFormat($orderTranslation->getEndTime(), CustomDateTime::F_HOUR_MINUTE);
            $paymentName = $orderTranslation->getPaymentName();
            if($paymentName !== ""){

                $emailContent = self::_replaceValue($emailContent, [
                    Form_C::INDEX_PAYMENT => $paymentName
                ]);
            }else{
                $emailContent = self::_removeBetweenTag($emailContent, [
                    Form_C::INDEX_PAYMENT
                ]);
            }
            $emailContent = self::_replaceValue($emailContent, [
                'title' => $title,
                Form_C::INDEX_NAME => $orderTranslation->getClientName(),
                Form_C::INDEX_EMAIL => $orderTranslation->getClientEmail(),
                Form_C::INDEX_PHONE => $orderTranslation->getClientPhone(),
                Form_C::INDEX_COMMENT => $orderTranslation->getComment(),
                Form_C::INDEX_DATE => "$dayString $date",
                Form_C::INDEX_HOURS => "$startTime - $endTime"
            ]);
            if($orderTranslation->getType() === Order_C::TYPE_DELIVERY){
                $emailContent = self::_replaceValue($emailContent, [
                    "address" => $orderTranslation->getAddress(),
                    "addressUrl" => urlencode($orderTranslation->getAddress())
                ]);
            }else{
                $emailContent = self::_removeBetweenTag($emailContent, [
                    "deliveryInfo",
                    "totalProduct" => $orderTranslation->getTotalProductString()
                ]);
            }
            if($orderStatus === Order_C::STATUS_CONFIRMED){
                $emailContent = self::_replaceValue($emailContent, [
                    "warning" => "Commande confirmée"
                ]);
            }elseif ($orderStatus === Order_C::STATUS_DENIED){
                $emailContent = self::_replaceValue($emailContent, [
                    "warning" => "Commande declinée"
                ]);

            }else{
                $emailContent = self::_removeBetweenTag($emailContent, [
                    'order-warning'
                ]);

            }
        }
        return $emailContent;
    }

    private static function _replaceOrderCart(OrderTranslation $orderTranslation, string $emailContent = "") : string
    {
        $emailContent = $emailContent !== "" ? $emailContent : self::_getEmailContent();
        if ($orderTranslation !== null && $orderTranslation instanceof OrderTranslation){
            $productRow = "";
            $productRowTemplate = self::_getBetweenTag($emailContent, "product");
            foreach ($orderTranslation->getArrayOrderElement() as $orderElement){
                if($orderElement instanceof OrderElement){
                    $productRow .= self::_replaceValue($productRowTemplate, [
                        'qty' => $orderElement->getQty(),
                        'product' => $orderElement->getDisplayedName(),
                        'price' => $orderElement->getPriceString(),
                        'total' => $orderElement->getTotalString()
                    ]);
                }
            }

            $arrayPromotion = $orderTranslation->getPromotions();
            if(sizeof($arrayPromotion) > 0){
                $promotionRow = "";
                $promotionRowTemplate = self::_getBetweenTag($emailContent, "promotion");
                foreach ($orderTranslation->getPromotions() as $promotion){
                    if($promotion instanceof OrderPromotion){
                        $promotionRow .= self::_replaceValue($promotionRowTemplate, [
                            'promoCode' =>  $promotion->getValueString(),
                            'promotionValue' => $promotion->getValuePrice()
                        ]);
                    }
                }
                $emailContent = self::_replaceBetweenTag($emailContent, [
                    Form_C::INDEX_PROMOTION => $promotionRow
                ]);
            }else{
                $emailContent = self::_removeBetweenTag($emailContent, [
                    Form_C::INDEX_PROMOTION
                ]);
            }

            if($orderTranslation->getType() === Order_C::TYPE_DELIVERY){
                $emailContent = self::_replaceValue($emailContent, [
                    "deliveryFee" => $orderTranslation->getDeliveryFeeString()
                ]);
            }else{
                $emailContent = self::_removeBetweenTag($emailContent, [
                    "deliveryRow"
                ]);
            }
            $emailContent = self::_replaceBetweenTag($emailContent, [
                "product" => $productRow
            ]);
            $emailContent = self::_replaceValue($emailContent, [
                "totalProduct" => $orderTranslation->getTotalProductString(),
                "totalCart" => $orderTranslation->getOrderTotalString()
            ]);
        }
        return $emailContent;
    }

    private static function _insertButtons(OrderTranslation $orderTranslation, string $emailContent = "", int $orderStatus = Order_C::STATUS_SEND){
        $emailContent = $emailContent !== "" ? $emailContent : self::_getEmailContent();

        if ($orderTranslation !== null && $orderTranslation instanceof OrderTranslation && $orderStatus === Order_C::STATUS_SEND){
            $requestUrl = Config::_getRootAddress().Page_C::_getPageLink(Page_C::pageRequest);
            //TODO check
//            $encryptedConfirmUrl = $orderTranslation->encryptUrl();
            $encryptedConfirmUrl = OrderTranslation::_encryptUrl($orderTranslation->getIdCode(), $orderTranslation->getLanguage());
            $encryptedConfirmUrl = "$requestUrl?q=$encryptedConfirmUrl";
            $confirmButton = self::_getHtmlButton("Traiter", $encryptedConfirmUrl);

            $emailContent = self::_replaceBetweenTag($emailContent, [
                "processButton" =>"$confirmButton"
            ]);
        }
        return $emailContent;
    }

    private static function _getOrderEmailContent(OrderTranslation $orderTranslation, int $orderStatus = Order_C::STATUS_SEND){
        $emailContent = "";
        if($orderTranslation !== null && $orderTranslation instanceof OrderTranslation){
            $emailContent = self::_getEmailContent();
            $emailContent = self::_replaceOrderInfo($orderTranslation, $emailContent, $orderStatus);
            $emailContent = self::_replaceOrderCart($orderTranslation, $emailContent);
            $emailContent = self::_insertButtons($orderTranslation, $emailContent, $orderStatus);
        }
        return $emailContent;
    }

    public static function _getEmailMerchant(OrderTranslation $orderTranslation, int $orderStatus = Order_C::STATUS_SEND) : PHPMailer
    {
        $emailSender = new EmailSmtp();
        $subject = self::_getSubject($orderTranslation, $orderStatus);
        $emailContent = self::_getOrderEmailContent($orderTranslation, $orderStatus);
        $emailSender->Subject = $subject;
        $emailSender->msgHTML($emailContent);
        $emailSender->AltBody = EmailContent::_getAltBody($emailContent);
        return $emailSender;
    }

}