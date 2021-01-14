<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 09-05-20
 * Time: 16:20
 */
namespace salesteck\takeAwayDelivery;


use JsonSerializable;
use salesteck\admin\AdminI18;
use salesteck\admin\AdminI18_C;
use salesteck\utils\CustomDateTime;

class DateOptions implements JsonSerializable
{
    private $label, $value;

    /**
     * DateOptions constructor.
     * @param string $label
     * @param string $value
     */
    public function __construct(string $label, string $value)
    {
        $this->label = $label;
        $this->value = $value;
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

    public function __toString() : string
    {
        return json_encode($this->jsonSerialize());
    }

    public static function _getDaysString(string $language = "") : array
    {

        $options = [];

        $i18n = AdminI18_C::_getInstance($language);
        $monday = AdminI18_C::_getValueFromKey(AdminI18::DAY_MONDAY, $i18n);
        $tuesday = AdminI18_C::_getValueFromKey(AdminI18::DAY_TUESDAY, $i18n);
        $wednesday = AdminI18_C::_getValueFromKey(AdminI18::DAY_WEDNESDAY, $i18n);
        $thursday = AdminI18_C::_getValueFromKey(AdminI18::DAY_THURSDAY, $i18n);
        $friday = AdminI18_C::_getValueFromKey(AdminI18::DAY_FRIDAY, $i18n);
        $saturday = AdminI18_C::_getValueFromKey(AdminI18::DAY_SATURDAY, $i18n);
        $sunday = AdminI18_C::_getValueFromKey(AdminI18::DAY_SUNDAY, $i18n);

        $options[strval(CustomDateTime::MONDAY)] = $monday;
        $options[strval(CustomDateTime::TUESDAY)] = $tuesday;
        $options[strval(CustomDateTime::WEDNESDAY)] = $wednesday;
        $options[strval(CustomDateTime::THURSDAY)] = $thursday;
        $options[strval(CustomDateTime::FRIDAY)] = $friday;
        $options[strval(CustomDateTime::SATURDAY)] = $saturday;
        $options[strval(CustomDateTime::SUNDAY)] = $sunday;
        return $options;
    }
}