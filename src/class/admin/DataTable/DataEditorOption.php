<?php

namespace salesteck\DataTable;
use salesteck\admin\AdminI18;
use salesteck\admin\AdminI18_C;
use salesteck\takeAwayDelivery\DeliveryHours_C;
use salesteck\utils\CustomDateTime;


/**
 * Created by PhpStorm.
 * User: Son
 * Date: 05-12-19
 * Time: 07:08
 */

class DataEditorOption
{

    /**
     * TODO
     * All options value must be type string
     * @param string $language
     * @return array
     */

    static function _getDays(string $language = "") : array
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

        $options[$monday] = strval(CustomDateTime::MONDAY);
        $options[$tuesday] = strval(CustomDateTime::TUESDAY);
        $options[$wednesday] = strval(CustomDateTime::WEDNESDAY);
        $options[$thursday] = strval(CustomDateTime::THURSDAY);
        $options[$friday] = strval(CustomDateTime::FRIDAY);
        $options[$saturday] = strval(CustomDateTime::SATURDAY);
        $options[$sunday] = strval(CustomDateTime::SUNDAY);
        return $options;
    }

    static function _getMonth(string $language = "") : array
    {

        $options = [];

        $i18n = AdminI18_C::_getInstance($language);

        $january = AdminI18_C::_getValueFromKey(AdminI18::MONTH_JANUARY, $i18n);
        $february = AdminI18_C::_getValueFromKey(AdminI18::MONTH_FEBRUARY, $i18n);
        $march = AdminI18_C::_getValueFromKey(AdminI18::MONTH_MARCH, $i18n);
        $april = AdminI18_C::_getValueFromKey(AdminI18::MONTH_APRIL, $i18n);
        $may = AdminI18_C::_getValueFromKey(AdminI18::MONTH_MAY, $i18n);
        $june = AdminI18_C::_getValueFromKey(AdminI18::MONTH_JUNE, $i18n);
        $july = AdminI18_C::_getValueFromKey(AdminI18::MONTH_JULY, $i18n);
        $august = AdminI18_C::_getValueFromKey(AdminI18::MONTH_AUGUST, $i18n);
        $september = AdminI18_C::_getValueFromKey(AdminI18::MONTH_SEPTEMBER, $i18n);
        $october = AdminI18_C::_getValueFromKey(AdminI18::MONTH_OCTOBER, $i18n);
        $november = AdminI18_C::_getValueFromKey(AdminI18::MONTH_NOVEMBER, $i18n);
        $december = AdminI18_C::_getValueFromKey(AdminI18::MONTH_DECEMBER, $i18n);

        $options[$january] = strval(1);
        $options[$february] = strval(2);
        $options[$march] = strval(3);
        $options[$april] = strval(4);
        $options[$may] = strval(5);
        $options[$june] = strval(6);
        $options[$july] = strval(7);
        $options[$august] = strval(8);
        $options[$september] = strval(9);
        $options[$october] = strval(10);
        $options[$november] = strval(11);
        $options[$december] = strval(12);
        return $options;
    }

    static function _getDeliveryHoursType(string $language = "") : array
    {

        $i18n = AdminI18_C::_getInstance($language);
        $takeaway = AdminI18_C::_getValueFromKey(AdminI18::TYPE_TAKE_AWAY, $i18n);
        $delivery = AdminI18_C::_getValueFromKey(AdminI18::TYPE_DELIVERY, $i18n);

        return [
            $takeaway => strval(DeliveryHours_C::TYPE_TAKE_AWAY),
            $delivery => strval(DeliveryHours_C::TYPE_DELIVERY)
        ];
    }
}