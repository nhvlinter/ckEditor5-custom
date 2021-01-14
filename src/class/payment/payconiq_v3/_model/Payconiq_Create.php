<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 04-11-20
 * Time: 17:56
 */

namespace salesteck\payconiq_v3;


class Payconiq_Create extends Payconiq_C implements \JsonSerializable
{


    private $apiKey, $amount, $callbackUrl, $currency, $description, $reference, $bulkId, $returnUrl, $_response, $_httpCode;

    /**
     * Payconiq_Create constructor.
     * @param $apiKey
     * @param $amount
     * @param $description
     * @param $reference
     */
    public function __construct($apiKey, $amount, $description, $reference)
    {
        $this->apiKey = $apiKey;
        $this->amount = $amount;
        $this->callbackUrl = "";
        $this->currency = self::EUR;
        $this->description = $description;
        $this->reference = $reference;
        $this->bulkId = "";
        $this->returnUrl = "";
    }

    /**
     * @return string
     */
    public function getApiKey() : string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey(string $apiKey) :self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getAmount() : string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return $this
     */
    public function setAmount(string $amount):self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCallbackUrl() : string
    {
        return $this->callbackUrl;
    }

    /**
     * @param string $callbackUrl
     * @return $this
     */
    public function setCallbackUrl(string $callbackUrl) :self
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency() : string
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return $this
     */
    public function setDescription(string $description) : self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getReference() : string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     * @return $this
     */
    public function setReference(string $reference) :self
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return string
     */
    public function getReturnUrl() : string
    {
        return $this->returnUrl;
    }

    /**
     * @param mixed $returnUrl
     * @return $this
     */
    public function setReturnUrl(string $returnUrl) :self
    {
        $this->returnUrl = $returnUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getBulkId(): string
    {
        return $this->bulkId;
    }

    /**
     * @param string $bulkId
     */
    public function setBulkId(string $bulkId)
    {
        $this->bulkId = $bulkId;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response)
    {
        $this->_response = $response;
    }

    /**
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->_httpCode;
    }

    /**
     * @param mixed $httpCode
     */
    public function setHttpCode($httpCode)
    {
        $this->_httpCode = $httpCode;
    }




    public function exec()
    {
        $curl = curl_init();
        $curlData = $this->getPayData();
        curl_setopt_array($curl, $curlData);
//        $this->curl = curl_init();
        $status = curl_exec($curl);
        $this->_httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->_response = json_decode($status);
        curl_close($curl);
        return $this;
    }


    private function getPayData(){

        $curlHeader = self::_getCurlHeader($this->apiKey);
        $curlBody = self::getPostBody();
        return [
            CURLOPT_HTTPHEADER => $curlHeader,
            CURLOPT_URL => self::PAYCONIQ_EXT_URL,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($curlBody)
        ];
    }


    public function cancel()
    {
        $curl = curl_init();
        $curlData = $this->getCancelData();
        curl_setopt_array($curl, $curlData);
//        $this->curl = curl_init();
        $status = curl_exec($curl);
        curl_close($curl);
        return json_decode($status);
    }

    private function getCancelData(){

        $curlHeader = self::_getCurlHeader($this->apiKey);
        $curlBody = self::getPostBody();
        return [
            CURLOPT_HTTPHEADER => $curlHeader,
            CURLOPT_URL => self::PAYCONIQ_EXT_URL,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_POSTFIELDS => json_encode($curlBody)
        ];
    }





    private function getPostBody() : array
    {
        return $curlBody = [
            'amount'=> $this->amount,
            'currency'=> self::EUR,
            'description'=> $this->description,
            'reference'=> $this->reference,
            "callbackUrl"=> "https://demo.storkoo.be/",
            "returnUrl"=> "https://demo.storkoo.be/"
        ];
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