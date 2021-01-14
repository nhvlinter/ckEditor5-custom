<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 16-10-19
 * Time: 18:16
 */

namespace salesteck\_base;

use salesteck\admin\AdminI18;
use salesteck\admin\AdminI18_C;
use salesteck\Db\Db;

class Form_C extends Db
{
    public const
        FORM_QUOTATION = "form-quotation",
        FORM_CHECKOUT = "form-checkout",
        FORM_CONTACT = "form-contact",
        FORM_BOOKING = "form-booking",
        FORM_ADMIN_LOGIN = "form-admin-login",
        FORM_NEWSLETTER = "form-newsletter"
    ;

    public const
        STATUS_CREATED = 0,
        STATUS_SEND = 1,
        STATUS_RECEIVED = 2
    ;

    public static function _getEditorOptionType(string $language){
        $i18n = AdminI18_C::_getInstance($language);
        $created = AdminI18_C::_getValueFromKey(AdminI18::FORM_STATUS_CREATED, $i18n);
        $send = AdminI18_C::_getValueFromKey(AdminI18::FORM_STATUS_SEND, $i18n);
        $receive = AdminI18_C::_getValueFromKey(AdminI18::FORM_STATUS_RECEIVED, $i18n);
        $arrayOption = [
            $created => strval(self::STATUS_CREATED),
            $send => strval(self::STATUS_SEND),
            $receive => strval(self::STATUS_RECEIVED)
        ];
        return $arrayOption;
    }

    public const
        /**
         * general index
         */

        INDEX_FORM = "form",
        INDEX_NAME = "name",
        INDEX_RING_NAME = "ring-name",
        INDEX_LAST_NAME = "lastName",
        INDEX_PHONE = "phone",
        INDEX_EMAIL = "email",
        INDEX_ADDRESS = "address",
        INDEX_POST_CODE = "postCode",
        INDEX_SUB_ADDRESS = "subAddress",
        INDEX_COMMENT = "comment",

        INDEX_COMPANY = "company",
        INDEX_COUNTRY = "country",
        INDEX_INDUSTRY = "industry",
        INDEX_LOGIN = "login",
        INDEX_MESSAGE = "message",
        INDEX_PASSWORD = "password",
        INDEX_PASSWORD_CONFIRM = "passwordConfirm",
        INDEX_REMEMBER_ME = "remember-me",
        INDEX_REQUEST = "request",
        INDEX_SERVICE = "service",
        INDEX_SUBJECT = "subject",
        INDEX_TITLE = "title",
        INDEX_WEBSITE = "website",


        /**
         * for booking
         */
        INDEX_PEOPLE = "people",
        INDEX_DATE = "date",
        INDEX_TIME = "time",

        /**
         * for order
         */
        INDEX_TAKE_AWAY_DELIVERY = "take-away-delivery",
        INDEX_DELIVERY_ZONE = "delivery-zone",
        INDEX_HOURS = "hours",
        INDEX_QTY = "qty",
        INDEX_CART_ELEMENT = "cart_element",
        INDEX_PRICE = "price",
        INDEX_PROMOTION = "promotion",
        INDEX_PROMOTION_CODE = "promotion_code",
        INDEX_TOTAL = "total",
        INDEX_PAYMENT = "payment"
    ;

    public const
        _col_subject = '_col_subject',
        _col_message = '_col_message',
        _col_country = self::_col."_country"
    ;
}