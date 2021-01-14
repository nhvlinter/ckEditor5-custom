<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 04-11-20
 * Time: 21:07
 */

namespace salesteck\payconiq_v3;


class Payconiq_Cancel extends Payconiq_C implements \JsonSerializable
{
    private $apiKey, $paymentId;

    /**
     * Payconiq_Cancel constructor.
     * @param $apiKey
     * @param $paymentId
     */
    public function __construct($apiKey, $paymentId)
    {
        $this->apiKey = $apiKey;
        $this->paymentId = $paymentId;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @param mixed $paymentId
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
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
        return [
            CURLOPT_HTTPHEADER => $curlHeader,
            CURLOPT_URL => self::PAYCONIQ_EXT_URL."/".$this->apiKey,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => "DELETE"
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