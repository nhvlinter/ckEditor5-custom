<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 03-11-20
 * Time: 00:24
 */

namespace salesteck\merchant;
use salesteck\_base\Language_C;
use salesteck\Db\Sql;
use salesteck\Db\SqlCondition;
use salesteck\utils\String_Helper;


/**
 * Class MerchantProfile
 * @package salesteck\merchant
 */
class MerchantProfile implements \JsonSerializable
{
    /**
     * @var $idCode string
     */
    /**
     * @var $merchantIdCode
     */
    /**
     * @var $takeAway bool
     */
    /**
     * @var $delivery bool
     */
    /**
     * @var $logo string
     */
    /**
     * @var $backgroundImage string
     */
    protected $idCode, $takeAway, $delivery, $logo, $backgroundImage;


    public static function _getProfile(string $merchantIdCode){

        if(String_Helper::_isStringNotEmpty($merchantIdCode)){

            $sql = MerchantProfile_C::_getSql();
            $sql->equal(
                MerchantProfile_C::TABLE, MerchantProfile_C::_col_merchant_id_code,
                $merchantIdCode, SqlCondition::_AND, true
            );
            if($sql->select()){
                $row = $sql->first();
                return self::_inst($row);
            }

        }
        return null;
    }


    public static function _inst(array $row)
    {
        if(
            array_key_exists(Merchant_C::_col_merchant_id_code, $row) &&
            array_key_exists(MerchantProfile_C::_col_take_away, $row) &&
            array_key_exists(MerchantProfile_C::_col_delivery, $row) &&
            array_key_exists(MerchantProfile_C::_col_logo, $row) &&
            array_key_exists(MerchantProfile_C::_col_background_image, $row)
        ){
            return new MerchantProfile(
                $row[Merchant_C::_col_merchant_id_code],
                $row[MerchantProfile_C::_col_take_away],
                $row[MerchantProfile_C::_col_delivery],
                $row[MerchantProfile_C::_col_logo],
                $row[MerchantProfile_C::_col_background_image]
            );
        }
        return null;
    }

    /**
     * MerchantProfile constructor.
     * @param string $merchantIdCode
     * @param string $takeAway
     * @param string $delivery
     * @param string $logo
     * @param string $backgroundImage
     */
    public function __construct(string $merchantIdCode, $takeAway, $delivery, string $logo, string $backgroundImage)
    {
        $this->idCode = $merchantIdCode;
        $this->takeAway = boolval($takeAway);
        $this->delivery = boolval($delivery);
        $this->logo = $logo;
        $this->backgroundImage = $backgroundImage;
    }

    /**
     * @return string
     */
    public function getIdCode() : string
    {
        return $this->idCode;
    }

    /**
     * @return bool
     */
    public function getTakeAway() : bool
    {
        return $this->takeAway;
    }

    /**
     * @param string $takeAway
     * @return $this
     */
    public function setTakeAway(string $takeAway) : self
    {
        $this->takeAway = $takeAway;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDelivery() : bool
    {
        return $this->delivery;
    }

    /**
     * @param bool $delivery
     * @return $this
     */
    public function setDelivery(bool $delivery) : self
    {
        $this->delivery = $delivery;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogo() : string
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     * @return $this
     */
    public function setLogo(string $logo) : self
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * @return string
     */
    public function getBackgroundImage() : string
    {
        return $this->backgroundImage;
    }

    /**
     * @param string $backgroundImage
     * @return $this
     */
    public function setBackgroundImage(string $backgroundImage)
    {
        $this->backgroundImage = $backgroundImage;
        return $this;
    }



    public function getConditionString( string $language){
        $condition = "";
        $language = Language_C::_getValidLanguage($language);
        $sql = Sql::_inst(MerchantProfile_C::TABLE_TRANSLATION);
        $sql
            ->equal(
                MerchantProfile_C::TABLE_TRANSLATION,
                MerchantProfile_C::_col_language,
                $language
            )
            ->equal(
                MerchantProfile_C::TABLE_TRANSLATION,
                Merchant_C::_col_merchant_id_code,
                $this->getIdCode()
            )
        ;
        if($sql->select()){
            $row = $sql->first();
            if(array_key_exists(MerchantProfile_C::_col_conditions, $row)){
                $condition = $row[MerchantProfile_C::_col_conditions];

                $condition = is_string($condition) ? $condition : "";
            }
        }
//        echo "<pre>". json_encode($sql, JSON_PRETTY_PRINT)."</pre>";

        return $condition;
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