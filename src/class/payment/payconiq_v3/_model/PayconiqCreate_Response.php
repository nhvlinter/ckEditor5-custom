<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 04-11-20
 * Time: 20:41
 */

namespace salesteck\payconiq_v3;


use salesteck\_interface\JsonToClass;

class PayconiqCreate_Response implements \JsonSerializable, JsonToClass
{
    private
        $paymentId,
        $status,
        $createdAt,
        $expiresAt,
        $amount,
        $reference,
        $description,
        $currency,
        $creditor,
        $_links
    ;

    /**
     * PayconiqCreate_Response constructor.
     * @param $paymentId
     * @param $status
     * @param $createdAt
     * @param $expiresAt
     * @param $amount
     * @param $reference
     * @param $description
     * @param $currency
     * @param $creditor
     * @param $_links
     */
    public function __construct($paymentId, $status, $createdAt, $expiresAt, $amount, $reference, $description, $currency, $creditor, $_links)
    {
        $this->amount = $amount;
        $this->reference = $reference;
        $this->description = $description;
        $this->currency = $currency;
        $this->creditor = $creditor;
        $this->paymentId = $paymentId;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->expiresAt = $expiresAt;
        $this->_links = $_links;
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

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param mixed $expiresAt
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param mixed $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getCreditor()
    {
        return $this->creditor;
    }

    /**
     * @param mixed $creditor
     */
    public function setCreditor($creditor)
    {
        $this->creditor = $creditor;
    }

    /**
     * @return mixed
     */
    public function getLinks()
    {
        return $this->_links;
    }

    /**
     * @param mixed $links
     */
    public function setLinks($links)
    {
        $this->_links = $links;
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


    public function jsonToClass(array $json)
    {
        foreach($json as $key => $value){
            $this->{$key} = $value;
        }
    }

}