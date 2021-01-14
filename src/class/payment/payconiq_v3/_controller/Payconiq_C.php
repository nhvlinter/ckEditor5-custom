<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 04-11-20
 * Time: 17:37
 */
namespace salesteck\payconiq_v3;

class Payconiq_C extends \stdClass
{

    protected const
        EUR = "EUR",
        AMOUNT_MAX = 999999,
        DESC_MAX_CHAR = 140,
        REF_MAX_CHAR = 35,
        BULK_ID_MAX_CHAR = 35,
        RETURN_URL_MAX_CHAR = 2048
    ;

    protected const
        PAYCONIQ_EXT_URL = 'https://api.ext.payconiq.com/v3/payments',
        PAYCONIQ_PROD_URL = 'https://api.payconiq.com/v3/payments',
        API_SEPARATOR = "-",
        API_KEY_FORMAT = [8, 4, 4, 4, 12]
    ;

    public const
        TEST_API = "0f7cb08a-552d-4ebd-bcb3-55bb7595647e",
        TEST_MERCHANT_ID = "5f9ff841353f8a000634fc06",
        TEST_MERCHANT_NAME = "Storkoo",
        TEST_PROFILE_ID = "5f9ff897353f8a000634fc07"
    ;


    public const
        S_OK = 200,
        S_CREATED = 201,
        S_PAYMENT_CANCELLED = 204,
        ERR_MISSING_INFORMATION = 400,
        ERR_UNAUTHORIZED = 401,
        ERR_ACCESS_DENIED = 403,
        ERR_MERCHANT_PROFILE_NOT_FOUND = 404,
        ERR_UNABLE_TO_PAY_CREDITOR = 422,
        ERR_TOO_MANY_REQUEST = 429,
        ERR_PAYCONIQ_TECHNICAL_ERROR = 500,
        ERR_SERVICE_UNAVAILABLE = 503
    ;

    protected static function _getCurlHeader(string $apiKey) : ? array
    {
        if(self::_isValidApiStringFormat($apiKey)){
            return [
                "Authorization: Bearer $apiKey",
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ];
        }
        return [];
    }


    protected static function _isValidApiStringFormat(string $apiKey) : bool
    {
        $isValid = false;
        if(is_string($apiKey) && $apiKey !== ""){
            $arrayApiKey = explode(self::API_SEPARATOR, $apiKey);
            $sameFormat = true;
            if(sizeof($arrayApiKey) === sizeof(self::API_KEY_FORMAT)){
                for ($i=0; $i< sizeof($arrayApiKey); $i++){
                    $apiKeyElem = $arrayApiKey[$i];
                    $apiFormat = self::API_KEY_FORMAT[$i];
                    if(strlen($apiKeyElem) !== ($apiFormat)){
                        return $sameFormat = false;
                    }
                }
            }
            $isValid = $sameFormat;
        }
        return $isValid;
    }
}