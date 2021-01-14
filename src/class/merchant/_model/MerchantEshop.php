<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 23-10-20
 * Time: 02:40
 */

namespace salesteck\merchant;

class MerchantEshop implements \JsonSerializable
{
    private
        $idCode, $commercialName, $commercialUrl, $tvaNumber, $address,
        $postCode, $phone, $mobile, $contactName, $email
    ;

    /**
     * MerchantEshop constructor.
     * @param string $idCode
     * @param string $commercialName
     * @param string $commercialUrl
     * @param string $tvaNumber
     * @param string $address
     * @param string $postCode
     * @param string $phone
     * @param string $mobile
     * @param string $contactName
     * @param string $email
     */
    private function __construct(
        $idCode, $commercialName, $commercialUrl, $tvaNumber,
        $address, $postCode, $phone, $mobile, $contactName, $email
    )
    {
        $this->idCode = $idCode;
        $this->commercialName = $commercialName;
        $this->commercialUrl = $commercialUrl;
        $this->tvaNumber = $tvaNumber;
        $this->address = $address;
        $this->postCode = $postCode;
        $this->phone = $phone;
        $this->mobile = $mobile;
        $this->contactName = $contactName;
        $this->email = $email;
    }

    public static function _inst(array $sqlRow) : ? self
    {
        if(
            $sqlRow !== null && gettype($sqlRow) === gettype([]) &&
            array_key_exists(Merchant_C::_col_merchant_id_code, $sqlRow) &&
            array_key_exists(Merchant_C::_col_commercial_name, $sqlRow) &&
            array_key_exists(Merchant_C::_col_commercial_url, $sqlRow) &&
            array_key_exists(Merchant_C::_col_tva_id, $sqlRow) &&
            array_key_exists(Merchant_C::_col_address, $sqlRow) &&
            array_key_exists(Merchant_C::_col_post_code, $sqlRow) &&
            array_key_exists(Merchant_C::_col_phone, $sqlRow) &&
            array_key_exists(Merchant_C::_col_mobile, $sqlRow) &&
            array_key_exists(Merchant_C::_col_contact_name, $sqlRow) &&
            array_key_exists(Merchant_C::_col_email, $sqlRow)
        ){
            return new self(
                $sqlRow[Merchant_C::_col_merchant_id_code],
                $sqlRow[Merchant_C::_col_commercial_name],
                $sqlRow[Merchant_C::_col_commercial_url],
                $sqlRow[Merchant_C::_col_tva_id],
                $sqlRow[Merchant_C::_col_address],
                $sqlRow[Merchant_C::_col_post_code],
                $sqlRow[Merchant_C::_col_phone],
                $sqlRow[Merchant_C::_col_mobile],
                $sqlRow[Merchant_C::_col_contact_name],
                $sqlRow[Merchant_C::_col_email]
            );
        }
        return null;

    }

    /**
     * @return string
     */
    public function getIdCode(): string
    {
        return $this->idCode;
    }

    /**
     * @param string $idCode
     */
    public function setIdCode(string $idCode)
    {
        $this->idCode = $idCode;
    }

    /**
     * @return string
     */
    public function getCommercialName(): string
    {
        return $this->commercialName;
    }

    /**
     * @param string $commercialName
     */
    public function setCommercialName(string $commercialName)
    {
        $this->commercialName = $commercialName;
    }

    /**
     * @return string
     */
    public function getCommercialUrl(): string
    {
        return $this->commercialUrl;
    }

    /**
     * @param string $commercialUrl
     */
    public function setCommercialUrl(string $commercialUrl)
    {
        $this->commercialUrl = $commercialUrl;
    }



    /**
     * @return string
     */
    public function getTvaNumber(): string
    {
        return $this->tvaNumber;
    }

    /**
     * @param string $tvaNumber
     */
    public function setTvaNumber(string $tvaNumber)
    {
        $this->tvaNumber = $tvaNumber;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getPostCode(): string
    {
        return $this->postCode;
    }

    /**
     * @param string $postCode
     */
    public function setPostCode(string $postCode)
    {
        $this->postCode = $postCode;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getMobile(): string
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile(string $mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getContactName(): string
    {
        return $this->contactName;
    }

    /**
     * @param string $contactName
     */
    public function setContactName(string $contactName)
    {
        $this->contactName = $contactName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
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