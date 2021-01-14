<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 12-11-19
 * Time: 03:23
 */

namespace salesteck\customer;


class Customer implements \JsonSerializable
{

    public const
        OPERATION = "operation",
        OPE_CREATE = 0,
        OPE_AUTHENTICATE = 1,
        OPE_RECOVERY = 2,
        OPE_CHANGE = 3
    ;

    public static function _inst($row) : ? self
    {
        if(
            is_array($row) &&
            array_key_exists(Customer_C::_col_customer_id_code, $row) &&
            array_key_exists(Customer_C::_col_name, $row) &&
            array_key_exists(Customer_C::_col_last_name, $row) &&
            array_key_exists(Customer_C::_col_email, $row) &&
            array_key_exists(Customer_C::_col_phone, $row) &&
            array_key_exists(Customer_C::_col_address, $row) &&
            array_key_exists(Customer_C::_col_post_code, $row) &&
            array_key_exists(Customer_C::_col_password, $row) &&
            array_key_exists(Customer_C::_col_is_authenticated, $row)&&
            array_key_exists(Customer_C::_col_is_recover, $row)
        ){
            return new Customer(
                $row[Customer_C::_col_customer_id_code],
                $row[Customer_C::_col_name],
                $row[Customer_C::_col_last_name],
                $row[Customer_C::_col_email],
                $row[Customer_C::_col_phone],
                $row[Customer_C::_col_address],
                $row[Customer_C::_col_post_code],
                $row[Customer_C::_col_password],
                $row[Customer_C::_col_is_authenticated],
                $row[Customer_C::_col_is_recover]
            );
        }
        return null;
    }

    private
        $idCode, $firstName, $lastName, $email, $phone, $address, $postCode, $password, $authentication, $isRecover
    ;

    /**
     * Customer constructor.
     *
     * @param $idCode
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param $phone
     * @param $address
     * @param $postCode
     * @param $password
     * @param $authentication
     * @param $isRecover
     *
     * @internal param $name
     */
    public function __construct($idCode, $firstName, $lastName, $email, $phone, $address, $postCode, $password, $authentication, $isRecover)
    {
        $this->idCode = $idCode;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->postCode = $postCode;
        $this->password = $password;
        $this->authentication = boolval($authentication);
        $this->isRecover = boolval($isRecover);
    }

    /**
     * @return mixed
     */
    public function getIdCode()
    {
        return $this->idCode;
    }

    /**
     * @param mixed $idCode
     * @return $this
     */
    public function setIdCode($idCode)
    {
        $this->idCode = $idCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     *
     * @return Customer
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     *
     * @return Customer
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }




    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->firstName." ".$this->lastName;
    }


    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @param mixed $postCode
     * @return $this
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAuthentication() : bool
    {
        return $this->authentication;
    }

    /**
     * @param bool $authentication
     * @return $this
     */
    public function setAuthentication(bool $authentication)
    {
        $this->authentication = $authentication;
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
     * @param bool $isRecover
     * @return $this
     */
    public function setIsRecover(bool $isRecover)
    {
        $this->isRecover = $isRecover;
        return $this;
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


    public static function isUserValid($user) : bool
    {
        if($user !== null && $user instanceof Customer){
            if($user->getAuthentication()){
                return true;
            }
        }
        return false;
    }
}