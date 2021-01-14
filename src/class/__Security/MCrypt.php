<?php
namespace salesteck\security;

use salesteck\utils\Json;

class MCrypt
{

    const PRIVATE_SSL_METHOD = 'aes-256-cbc';

    const PRIVATE_DELIMITER = ':';

    const PRIVATE_KEY = "yGZuh0aLvpCIOahEzam5wSu9kAG7mti1DJiUmlXRDYI=";

    const URL_FIND_CHAR = "+/=";
    const URL_REPLACE_CHAR = "-_,";

    public static function encryptSsl($data) {
        return self::encrypt($data, self::PRIVATE_KEY);
    }

    public static function decryptSsl($data) {
        return self::decrypt($data, self::PRIVATE_KEY);
    }

    private static function generateKey()
    {
         return base64_encode(openssl_random_pseudo_bytes(32));
    }



    protected static function hex2bin($hexData):string
    {
        $binData = "";
        for ($i = 0; $i < strlen($hexData); $i += 2) {
            $binData .= chr(hexdec(substr($hexData, $i, 2)));
        }
        return $binData;
    }


    private static function encrypt($data, $key) {
        // Remove the base64 encoding from our key
        $encryption_key = base64_decode($key);
        // Generate an initialization vector
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::PRIVATE_SSL_METHOD));
        // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
        $encrypted = openssl_encrypt($data, self::PRIVATE_SSL_METHOD, $encryption_key, 0, $iv);
        // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
        return base64_encode($encrypted . self::PRIVATE_DELIMITER . $iv);
    }



    private static function decrypt($data, $key) {
        // Remove the base64 encoding from our key
        $encryption_key = base64_decode($key);
        // To decrypt, split the encrypted data from our IV - our unique separator used was ":"
        list($encrypted_data, $iv) =
            explode(self::PRIVATE_DELIMITER, base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, self::PRIVATE_SSL_METHOD, $encryption_key, 0, $iv);
    }


    public static function url_encode(string $url) :string
    {
        return strtr(base64_encode($url), self::URL_FIND_CHAR, self::URL_REPLACE_CHAR);
    }

    public static function url_decode(string $url)
    {
        return base64_decode(strtr($url, self::URL_REPLACE_CHAR, self::URL_FIND_CHAR));
    }


    public static function _getEncryptedObjectUrl()
    {
        $encryptedObject = null;
        $request = Security::checkXss($_REQUEST);
        if(array_key_exists('q', $request)){
            $query = $request['q'];
            $jsonString = MCrypt::url_decode($query);
            if(Json::isJson($jsonString)) {
                $encryptedObject = json_decode($jsonString);
            }

        }
        return $encryptedObject;
    }

    public static function _getEncryptedObjectUrl_property(string $property){
        $returnProperty = null;
        $request = Security::checkXss($_REQUEST);
        if(array_key_exists('q', $request)){
            $query = $request['q'];
            $jsonString = MCrypt::url_decode($query);
            if(Json::isJson($jsonString)) {
                $encryptedObject = json_decode($jsonString);
                if(isset($encryptedObject->$property)){
                    return $encryptedObject->$property;
                }
            }

        }


        return $returnProperty;

    }

}
