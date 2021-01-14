<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 07-11-19
 * Time: 02:34
 */

namespace salesteck\admin;


class AdminI18
{

    public const
        appName = "appName",
        COL_ADDRESS =       "COL_ADDRESS",
        COL_ALLERGEN =      "COL_ALLERGEN",
        COL_ANSWER =        "COL_ANSWER",
        COL_CODE =          "COL_CODE",
        COL_CATEGORY =      "COL_CATEGORY",
        COL_COLOR =         "COL_COLOR",
        COL_COMMENT =       "COL_COMMENT",
        COL_CLASS =         "COL_CLASS",
        COL_COUNTRY =       "COL_COUNTRY",
        COL_CREATE_DATE =   "COL_CREATE_DATE",
        COL_DELIVERY_FEE =  "COL_DELIVERY_FEE",
        COL_DAYS =          "COL_DAYS",
        COL_DATE =          "COL_DATE",
        COL_DELIVERY_ZONE = "COL_DELIVERY_ZONE",
        COL_DESC =          "COL_DESC",
        COL_EMAIL =         "COL_EMAIL",
        COL_END =           "COL_END",
        COL_FILE_PATH =     "COL_FILE_PATH",
        COL_ICON =          "COL_ICON",
        COL_ID =            "COL_ID",
        COL_IMAGE =         "COL_IMAGE",
        COL_IMAGE_PATH =    "COL_IMAGE_PATH",
        COL_IS_AUTH =       "COL_IS_AUTH",
        COL_IS_ACCEPT =     "COL_IS_ACCEPT",
        COL_IS_DEFAULT =    "COL_IS_DEFAULT",
        COL_IS_DISPLAY =    "COL_IS_DISPLAY",
        COL_IS_ENABLE =     "COL_IS_ENABLE",
        COL_IS_MULTIPLE =   "COL_IS_MULTIPLE",
        COL_IS_ONLINE =     "COL_IS_ONLINE",
        COL_KEY =           "COL_KEY",
        COL_KEYWORDS =      "COL_KEYWORDS",
        COL_LABEL =         "COL_LABEL",
        COL_LANGUAGE =      "COL_LANGUAGE",
        COL_LAST_MODIFIED = "COL_LAST_MODIFIED",
        COL_LAST_NAME =     "COL_LAST_NAME",
        COL_LINK =          "COL_LINK",
        COL_LIMIT =         "COL_LIMIT",
        COL_MAX_QTY =       "COL_MAX_QTY",
        COL_MESSAGE =       "COL_MESSAGE",
        COL_MISSION =       "COL_MISSION",
        COL_MINIMUM_ORDER = "COL_MINIMUM_ORDER",
        COL_NAME =          "COL_NAME",
        COL_ORDER =         "COL_ORDER",
        COL_OPTION =        "COL_OPTION",
        COL_PAYMENT =       "COL_PAYMENT",
        COL_PARAMETER =     "COL_PARAMETER",
        COL_PASSWORD =      "COL_PASSWORD",
        COL_PEOPLE =        "COL_PEOPLE",
        COL_PHONE =         "COL_PHONE",
        COL_POST_CODE =     "COL_POST_CODE",
        COL_PREFIX =        "COL_PREFIX",
        COL_PRICE =         "COL_PRICE",
        COL_PROMOTION =     "COL_PROMOTION",
        COL_QTY =           "COL_QTY",
        COL_ROLE =          "COL_ROLE",
        COL_ROUTE =         "COL_ROUTE",
        COL_SALES_TYPE =    "COL_SALES_TYPE",
        COL_START =         "COL_START",
        COL_STATUS =        "COL_STATUS",
        COL_STEP =          "COL_STEP",
        COL_SUBJECT =       "COL_SUBJECT",
        COL_TEXT =          "COL_TEXT",
        COL_TITLE =         "COL_TITLE",
        COL_TICKET =        "COL_TICKET",
        COL_TOTAL =         "COL_TOTAL",
        COL_TYPE =          "COL_TYPE",
        COL_VALUE =         "COL_VALUE",
        COL_VALIDITY =      "COL_VALIDITY",
        COL_USED =          "COL_USED",
        COL_VARIABLE =      "COL_VARIABLE"
    ;

    public const
        DAY_MONDAY =    "DAY_MONDAY",
        DAY_TUESDAY =   "DAY_TUESDAY",
        DAY_WEDNESDAY = "DAY_WEDNESDAY",
        DAY_THURSDAY =  "DAY_THURSDAY",
        DAY_FRIDAY =    "DAY_FRIDAY",
        DAY_SATURDAY =  "DAY_SATURDAY",
        DAY_SUNDAY =    "DAY_SUNDAY"
    ;

    public const
        MONTH_JANUARY =     "MONTH_JANUARY",
        MONTH_FEBRUARY =    "MONTH_FEBRUARY",
        MONTH_MARCH =       "MONTH_MARCH",
        MONTH_APRIL =       "MONTH_APRIL",
        MONTH_MAY =         "MONTH_MAY",
        MONTH_JUNE =        "MONTH_JUNE",
        MONTH_JULY =        "MONTH_JULY",
        MONTH_AUGUST =      "MONTH_AUGUST",
        MONTH_SEPTEMBER =   "MONTH_SEPTEMBER",
        MONTH_OCTOBER =     "MONTH_OCTOBER",
        MONTH_NOVEMBER =    "MONTH_NOVEMBER",
        MONTH_DECEMBER =    "MONTH_DECEMBER"
    ;

    public const
        FORM_ENTER_LOGIN =          "FORM_ENTER_LOGIN",
        FORM_ENTER_PASSWORD =       "FORM_ENTER_PASSWORD",
        BUTTON_LOGIN =              "BUTTON_LOGIN",
        BUTTON_LOGOUT =             "BUTTON_LOGOUT",
        FORM_LOGOUT =               "FORM_LOGOUT"

    ;


    public const
        EDITOR_ERROR_VALUE_EXIST = "EDITOR_ERROR_VALUE_EXIST",


        /* Integer validation */
        EDITOR_ERROR_NUMERIC_VALUE = "EDITOR_ERROR_NUMERIC_VALUE",
        EDITOR_ERROR_INTEGER_VALUE = "EDITOR_ERROR_INTEGER_VALUE",
        EDITOR_ERROR_INTEGER_MIN = "EDITOR_ERROR_INTEGER_MIN",
        EDITOR_ERROR_INTEGER_MAX = "EDITOR_ERROR_INTEGER_MAX",
        EDITOR_ERROR_INTEGER_LOWER = "EDITOR_ERROR_INTEGER_LOWER",
        EDITOR_ERROR_INTEGER_GREATER = "EDITOR_ERROR_INTEGER_GREATER",

        /* String validation */
        EDITOR_ERROR_EMPTY_VALUE = "EDITOR_ERROR_EMPTY_VALUE",
        EDITOR_ERROR_NO_SPACE = "EDITOR_ERROR_NO_SPACE",
        EDITOR_ERROR_UNIQUE = "EDITOR_ERROR_UNIQUE",
        EDITOR_ERROR_STR_LENGTH = "EDITOR_ERROR_STR_LENGTH",
        EDITOR_ERROR_STR_CONTAIN = "EDITOR_ERROR_STR_CONTAIN",
        EDITOR_ERROR_STR_NO_CONTAIN = "EDITOR_ERROR_STR_NO_CONTAIN",
        EDITOR_ERROR_STR_START = "EDITOR_ERROR_STR_START",
        EDITOR_ERROR_STR_END = "EDITOR_ERROR_STR_END",
        EDITOR_ERROR_STR_NO_START = "EDITOR_ERROR_STR_NO_START",
        EDITOR_ERROR_STR_NO_END = "EDITOR_ERROR_STR_NO_END",

        /* File validation */
        EDITOR_ERROR_EMPTY_IMAGE = "EDITOR_ERROR_EMPTY_IMAGE",
        EDITOR_ERROR_IMAGE = "EDITOR_ERROR_IMAGE",
        EDITOR_ERROR_EXT = "EDITOR_ERROR_EXT",
        EDITOR_ERROR_IMAGE_SIZE = "EDITOR_ERROR_IMAGE_SIZE"
    ;


    public const
        TYPE_ALL = "TYPE_ALL",
        TYPE_TAKE_AWAY = "TYPE_TAKE_AWAY",
        TYPE_DELIVERY = "TYPE_DELIVERY"
    ;


    public const
        ORDER_STATUS_CREATED = "ORDER_STATUS_CREATED",
        ORDER_STATUS_SEND = "ORDER_STATUS_SEND",
        ORDER_STATUS_CONFIRMED = "ORDER_STATUS_CONFIRMED",
        ORDER_STATUS_DENIED = "ORDER_STATUS_DENIED",
        ORDER_STATUS_EXPIRED = "ORDER_STATUS_EXPIRED"
    ;


    public const
        FORM_STATUS_CREATED = "FORM_STATUS_CREATED",
        FORM_STATUS_SEND = "FORM_STATUS_SEND",
        FORM_STATUS_RECEIVED = "FORM_STATUS_RECEIVED"
    ;


    public const
        ADMIN_ROLE_SUPER_ADMIN = "ADMIN_ROLE_SUPER_ADMIN",
        ADMIN_ROLE_ADMIN = "ADMIN_ROLE_ADMIN",
        ADMIN_ROLE_USER = "ADMIN_ROLE_USER"
    ;

    public const
        page_dashBoard = "page_dashBoard",

        pages_request = "pages_request",
        page_booking = "page_booking",
        page_contactRequest = "page_contactRequest",
        page_quotationRequest = "page_quotationRequest",

        pages_sales = "pages_sales",
        page_order = "page_order",
        page_productAllergen = "page_productAllergen",
        page_productCategory = "page_productCategory",
        page_product = "page_product",
        page_productOption = "page_productOption",
        page_categoryOption = "page_categoryOption",
        page_menuCategory = "page_menuCategory",
        page_menu = "page_menu",
        page_promotion = "page_promotion",


        pages_content = "pages_content",
        page_webPage = "page_webPage",
        page_faq = "page_faq",
        page_customer = "page_customer",
        page_socialMedia = "page_socialMedia",
        page_industry = "page_industry",
        page_skills = "page_skill",
        page_services = "page_services",
        page_checkIn = "page_checkIn",
        page_country = "page_country",
        page_newsletter = "page_newsletter",
        page_gallery = "page_gallery",
        page_preview = "page_preview",
        page_Reference = "page_Reference",
        page_contentEdit = "page_contentEdit",

        pages_config = "pages_config",
        page_parameter = "page_parameter",
        page_payment = "page_payment",
        page_adminUser = "page_adminUser",
        page_constant = "page_constant",
        page_deliveryZone = "page_deliveryZone",
        page_deliveryHours = "page_deliveryHours",
        page_takeAwayHour = "page_takeAwayHour",
        page_openingHours = "page_openingHours",
        page_Hours = "page_Hours",


        page_adminPage = "page_adminPage",
        page_adminLanguage = "page_adminLanguage",
        page_adminConstant = "page_adminConstant",
        page_adminParameter = "page_adminParameter",
        page_language = "page_language",
        page_adminConfig = "page_adminConfig",


        pages_merchant = "pages_merchant",
        page_merchantCategory = "page_merchantCategory"
    ;
}