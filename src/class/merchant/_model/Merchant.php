<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 19-10-20
 * Time: 19:50
 */
namespace salesteck\merchant;
use salesteck\security\MCrypt;
use salesteck\utils\String_Helper;
use stdClass;


/**
 * Class Merchant
 * @package salesteck\merchant
 */
class Merchant implements \JsonSerializable
{


    public const
        OPERATION = "operation",
        OPE_CREATE = 0,
        OPE_AUTHENTICATE = 1,
        OPE_RECOVERY = 2,
        OPE_CHANGE = 3
    ;


    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var bool|string
     */
    /**
     * @var bool|string
     */
    /**
     * @var bool|string
     */
    /**
     * @var string
     */
    protected
        $idCode, $commercialName, $companyName, $tvaNumber, $address, $postCode, $phone, $mobile, $contactNumber,
        $contactName, $email, $preferredLanguage, $isEnable, $authenticated, $isValid, $isRecover, $password
    ;


    /**
     * Merchant constructor.
     *
     * @param $idCode
     * @param $commercialName
     * @param $companyName
     * @param $tvaNumber
     * @param $address
     * @param $postCode
     * @param $phone
     * @param $mobile
     * @param $contactNumber
     * @param $contactName
     * @param $email
     * @param $preferredLanguage
     * @param $isEnable
     * @param $authenticated
     * @param $isValid
     * @param $isRecover
     * @param $password
     */
    private function __construct(
        $idCode, $commercialName, $companyName, $tvaNumber, $address, $postCode, $phone, $mobile, $contactNumber,
        $contactName, $email, $preferredLanguage, $isEnable, $authenticated, $isValid, $isRecover, $password
    )
    {
        $this->idCode = $idCode;
        $this->commercialName = $commercialName;
        $this->companyName = $companyName;
        $this->tvaNumber = $tvaNumber;
        $this->address = $address;
        $this->postCode = $postCode;
        $this->phone = $phone;
        $this->mobile = $mobile;
        $this->contactNumber = $contactNumber;
        $this->contactName = $contactName;
        $this->email = $email;
        $this->preferredLanguage = $preferredLanguage;
        $this->isEnable = boolval($isEnable);
        $this->authenticated = boolval($authenticated);
        $this->isValid = boolval($isValid);
        $this->isRecover = boolval($isRecover);
        $this->password = $password;
    }

    public static function _inst(array $sqlRow) : ? self
    {
        if(
            $sqlRow !== null && gettype($sqlRow) === gettype([]) &&
            array_key_exists(Merchant_C::_col_merchant_id_code, $sqlRow) &&
            array_key_exists(Merchant_C::_col_commercial_name, $sqlRow) &&
            array_key_exists(Merchant_C::_col_company_name, $sqlRow) &&
            array_key_exists(Merchant_C::_col_tva_id, $sqlRow) &&
            array_key_exists(Merchant_C::_col_address, $sqlRow) &&
            array_key_exists(Merchant_C::_col_post_code, $sqlRow) &&
            array_key_exists(Merchant_C::_col_phone, $sqlRow) &&
            array_key_exists(Merchant_C::_col_mobile, $sqlRow) &&
            array_key_exists(Merchant_C::_col_contact_number, $sqlRow) &&
            array_key_exists(Merchant_C::_col_contact_name, $sqlRow) &&
            array_key_exists(Merchant_C::_col_email, $sqlRow) &&
            array_key_exists(Merchant_C::_col_preferred_language, $sqlRow) &&
            array_key_exists(Merchant_C::_col_is_enable, $sqlRow) &&
            array_key_exists(Merchant_C::_col_is_authenticated, $sqlRow) &&
            array_key_exists(Merchant_C::_col_is_valid, $sqlRow) &&
            array_key_exists(Merchant_C::_col_password, $sqlRow)
        ){
            $idCode = $sqlRow[Merchant_C::_col_merchant_id_code];
            if(String_Helper::_isStringNotEmpty($idCode)){
                return new self(
                    $sqlRow[Merchant_C::_col_merchant_id_code],
                    $sqlRow[Merchant_C::_col_commercial_name],
                    $sqlRow[Merchant_C::_col_company_name],
                    $sqlRow[Merchant_C::_col_tva_id],
                    $sqlRow[Merchant_C::_col_address],
                    $sqlRow[Merchant_C::_col_post_code],
                    $sqlRow[Merchant_C::_col_phone],
                    $sqlRow[Merchant_C::_col_mobile],
                    $sqlRow[Merchant_C::_col_contact_number],
                    $sqlRow[Merchant_C::_col_contact_name],
                    $sqlRow[Merchant_C::_col_email],
                    $sqlRow[Merchant_C::_col_preferred_language],
                    $sqlRow[Merchant_C::_col_is_enable],
                    $sqlRow[Merchant_C::_col_is_authenticated],
                    $sqlRow[Merchant_C::_col_is_valid],
                    $sqlRow[Merchant_C::_col_is_recover],
                    $sqlRow[Merchant_C::_col_password]
                );
            }
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
     * @return $this
     */
    public function setIdCode(string $idCode) :self
    {
        $this->idCode = $idCode;
        return $this;
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
     * @return $this
     */
    public function setCommercialName(string $commercialName) :self
    {
        $this->commercialName = $commercialName;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     * @return $this
     */
    public function setCompanyName(string $companyName) :self
    {
        $this->companyName = $companyName;
        return $this;
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
     * @return $this
     */
    public function setTvaNumber(string $tvaNumber) :self
    {
        $this->tvaNumber = $tvaNumber;
        return $this;
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
     * @return $this
     */
    public function setAddress(string $address) :self
    {
        $this->address = $address;
        return $this;
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
     * @return $this
     */
    public function setPostCode(string $postCode) :self
    {
        $this->postCode = $postCode;
        return $this;
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
     * @return $this
     */
    public function setPhone(string $phone) :self
    {
        $this->phone = $phone;
        return $this;
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
     * @return $this
     */
    public function setMobile(string $mobile) :self
    {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * @return string
     */
    public function getContactNumber(): string
    {
        return $this->contactNumber;
    }

    /**
     * @param string $contactNumber
     * @return $this
     */
    public function setContactNumber(string $contactNumber) :self
    {
        $this->contactNumber = $contactNumber;
        return $this;
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
     * @return $this
     */
    public function setContactName(string $contactName) :self
    {
        $this->contactName = $contactName;
        return $this;
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
     * @return $this
     */
    public function setEmail(string $email) :self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPreferredLanguage(): string
    {
        return $this->preferredLanguage;
    }

    /**
     * @param string $preferredLanguage
     * @return $this
     */
    public function setPreferredLanguage(string $preferredLanguage) :self
    {
        $this->preferredLanguage = $preferredLanguage;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->isEnable;
    }

    /**
     * @param bool $isEnable
     * @return $this
     */
    public function setIsEnable(bool $isEnable) :self
    {
        $this->isEnable = $isEnable;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }

    /**
     * @param bool $authenticated
     * @return $this
     */
    public function setAuthenticated(bool $authenticated) :self
    {
        $this->authenticated = $authenticated;
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * @param bool $isValid
     * @return $this
     */
    public function setIsValid(bool $isValid) :self
    {
        $this->isValid = $isValid;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRecover(): bool
    {
        return $this->isRecover;
    }

    /**
     * @param string $isRecover
     *
     * @return Merchant
     */
    public function setIsRecover(string $isRecover): Merchant
    {
        $this->isRecover = $isRecover;
        return $this;
    }






    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
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
        $obj = $this;
        unset($obj->password);
        return get_object_vars($obj);
    }


    public static function _merchantToEncryptedUrd(Merchant $merchant){

        $object = new stdClass();
        $object->className = self::class;
        $object->idCode = $merchant->getIdCode();
        $jsonString = json_encode($object);
        return MCrypt::url_encode($jsonString);
    }
}