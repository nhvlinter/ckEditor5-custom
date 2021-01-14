<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 03-11-20
 * Time: 00:46
 */

namespace salesteck\merchant;
use salesteck\_base\Language_C;
use salesteck\Db\SqlCondition;
use salesteck\Db\SqlJoin;
use salesteck\utils\Debug;
use salesteck\utils\String_Helper;


/**
 * Class MerchantProfileTranslation
 * @package salesteck\merchant
 */
class MerchantProfileTranslation extends MerchantProfile implements \JsonSerializable
{

    /**
     * @var int $id
     *
     * @var string $language
     *
     * @var string $welcomeText
     *
     * @var string $conditions
     *
     * @var string $testimonial
     *
     * @var string $thanksText
     *
     * @var string $descriptionText
     */
    private
        $id,
        $language,
        $welcomeText,
        $conditions,
        $testimonial,
        $thanksText,
        $descriptionText
    ;

    public static function _getProfileTranslation(string $merchantIdCode, string $language) : ? self
    {
        $language = Language_C::_getValidLanguage($language);
        if(String_Helper::_isStringNotEmpty($merchantIdCode)){
            $colMerchantIdCode = MerchantProfile_C::_col_merchant_id_code;

            $sql = MerchantProfile_C::_getSql();
            $sql
                ->column([
                    $colMerchantIdCode,
                    MerchantProfile_C::_col_take_away,
                    MerchantProfile_C::_col_delivery,
                    MerchantProfile_C::_col_logo,
                    MerchantProfile_C::_col_background_image
                ], MerchantProfile_C::TABLE)
                ->equal(
                    MerchantProfile_C::TABLE, $colMerchantIdCode, $merchantIdCode, SqlCondition::_AND, true
                )
            ;

            $translationJoin = new SqlJoin(
                MerchantProfile_C::TABLE, $colMerchantIdCode,
                MerchantProfile_C::TABLE_TRANSLATION, $colMerchantIdCode,
                SqlJoin::LEFT
            );

            $sql
                ->addJoin($translationJoin)
                ->column([
                    MerchantProfile_C::_col_id,
                    MerchantProfile_C::_col_language,
                    MerchantProfile_C::_col_welcome_text,
                    MerchantProfile_C::_col_conditions,
                    MerchantProfile_C::_col_testimonials,
                    MerchantProfile_C::_col_thanks_text,
                    MerchantProfile_C::_col_description_text
                ], MerchantProfile_C::TABLE_TRANSLATION)
                ->equal(
                    MerchantProfile_C::TABLE_TRANSLATION, MerchantProfile_C::_col_language,
                    $language, SqlCondition::_AND, true
                )
            ;


            $imageJoin = new SqlJoin(
                MerchantProfile_C::TABLE, MerchantProfile_C::_col_logo,
                MerchantProfile_C::TABLE_IMAGES, MerchantProfile_C::_col_id, SqlJoin::LEFT
            );
            $sql
                ->addJoin($imageJoin)
                ->column([
                    MerchantProfile_C::_col_web_path,
                    MerchantProfile_C::_col_file_absolute_path
                ], MerchantProfile_C::TABLE_IMAGES)
                ->equal(
                    MerchantProfile_C::TABLE, $colMerchantIdCode, $merchantIdCode, SqlCondition::_AND, true
                )
            ;



            if($sql->select(true)){
                Debug::_prettyPrint($sql->getSelectQueryString(true));
                $row = $sql->first();
                return MerchantProfileTranslation::_inst($row);
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
            array_key_exists(MerchantProfile_C::_col_background_image, $row) &&


            array_key_exists(MerchantProfile_C::_col_id, $row) &&
            array_key_exists(Merchant_C::_col_language, $row) &&
            array_key_exists(MerchantProfile_C::_col_welcome_text, $row) &&
            array_key_exists(MerchantProfile_C::_col_conditions, $row) &&
            array_key_exists(MerchantProfile_C::_col_testimonials, $row) &&
            array_key_exists(MerchantProfile_C::_col_thanks_text, $row) &&
            array_key_exists(MerchantProfile_C::_col_description_text, $row)
        ){
            return new self(
                $row[Merchant_C::_col_merchant_id_code],
                $row[MerchantProfile_C::_col_take_away],
                $row[MerchantProfile_C::_col_delivery],
                $row[MerchantProfile_C::_col_logo],
                $row[MerchantProfile_C::_col_background_image],

                $row[MerchantProfile_C::_col_id],
                $row[MerchantProfile_C::_col_language],
                $row[MerchantProfile_C::_col_welcome_text],
                $row[MerchantProfile_C::_col_conditions],
                $row[MerchantProfile_C::_col_testimonials],
                $row[MerchantProfile_C::_col_thanks_text],
                $row[MerchantProfile_C::_col_description_text]
            );
        }
        return null;
    }

    /**
     * MerchantProfileTranslation constructor.
     * @param string $merchantIdCode
     * @param string $takeAway
     * @param string $delivery
     * @param string $logo
     * @param string $backgroundImage
     * @param int $id
     * @param string $language
     * @param string $welcomeText
     * @param string $conditions
     * @param string $testimonial
     * @param string $thanksText
     * @param string $descriptionText
     */
    public function __construct(
        string $merchantIdCode, $takeAway, $delivery, string $logo, string $backgroundImage, int $id,
        string $language, string $welcomeText, string $conditions, string $testimonial, string $thanksText, string $descriptionText
    )
    {
        parent::__construct($merchantIdCode, $takeAway, $delivery, $logo, $backgroundImage);
        $this->id = $id;
        $this->language = $language;
        $this->welcomeText = $welcomeText;
        $this->conditions = $conditions;
        $this->testimonial = $testimonial;
        $this->thanksText = $thanksText;
        $this->descriptionText = $descriptionText;
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id) : self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage() : string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return $this
     */
    public function setLanguage(string $language) : self
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return string
     */
    public function getWelcomeText() : string
    {
        return $this->welcomeText;
    }

    /**
     * @param string $welcomeText
     * @return $this
     */
    public function setWelcomeText(string $welcomeText) : self
    {
        $this->welcomeText = $welcomeText;
        return $this;
    }

    /**
     * @return string
     */
    public function getConditions() : string
    {
        return $this->conditions;
    }

    /**
     * @param string $conditions
     * @return $this
     */
    public function setConditions(string $conditions) : self
    {
        $this->conditions = $conditions;
        return $this;
    }

    /**
     * @return string
     */
    public function getTestimonial() : string
    {
        return $this->testimonial;
    }

    /**
     * @param string $testimonial
     * @return $this
     */
    public function setTestimonial(string $testimonial) : self
    {
        $this->testimonial = $testimonial;
        return $this;
    }

    /**
     * @return string
     */
    public function getThanksText() : string
    {
        return $this->thanksText;
    }

    /**
     * @param string $thanksText
     * @return $this
     */
    public function setThanksText(string $thanksText) : self
    {
        $this->thanksText = $thanksText;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescriptionText() : string
    {
        return $this->descriptionText;
    }

    /**
     * @param string $descriptionText
     * @return $this
     */
    public function setDescriptionText(string $descriptionText) : self
    {
        $this->descriptionText = $descriptionText;
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