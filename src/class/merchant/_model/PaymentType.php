<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 05-11-20
 * Time: 00:08
 */

namespace salesteck\merchant;


/**
 * Class PaymentType
 * @package salesteck\merchant
 */
class PaymentType implements \JsonSerializable
{
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
    private $idCode, $name, $label, $webPath, $description;


    public static function _inst(array $row)
    {
        if(
            array_key_exists(PaymentType_C::_col_id_code, $row) &&
            array_key_exists(PaymentType_C::_col_name, $row) &&
            array_key_exists(PaymentType_C::_col_label, $row) &&
            array_key_exists(PaymentType_C::_col_web_path, $row)&&
            array_key_exists(PaymentType_C::_col_description, $row)
        ){
            return new self(
                $row[PaymentType_C::_col_id_code],
                $row[PaymentType_C::_col_name],
                $row[PaymentType_C::_col_label],
                $row[PaymentType_C::_col_web_path],
                $row[PaymentType_C::_col_description]
            );
        }
        return null;
    }

    /**
     * PaymentType constructor.
     * @param string $idCode
     * @param string $name
     * @param string $label
     * @param string $webPath
     */
    private function __construct($idCode, $name, $label, $webPath, $description)
    {
        $this->idCode = $idCode;
        $this->name = $name;
        $this->label = $label;
        $this->webPath = $webPath;
        $this->description = $description;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getWebPath(): string
    {
        return $this->webPath;
    }

    /**
     * @param string $webPath
     */
    public function setWebPath(string $webPath)
    {
        $this->webPath = $webPath;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return PaymentType
     */
    public function setDescription(string $description): PaymentType
    {
        $this->description = $description;
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
}