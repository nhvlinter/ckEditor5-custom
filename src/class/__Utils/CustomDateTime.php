<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 12-11-19
 * Time: 03:51
 */

namespace salesteck\utils;


use DateTime;
use DateTimeZone;
use salesteck\admin\AdminI18;
use salesteck\admin\AdminI18_C;

class CustomDateTime
{
    const
        DEFAULT_TIME_ZONE = "Europe/Brussels",
        STAMP_TO_MILLIS = 1,
        SECOND = 1 * self::STAMP_TO_MILLIS,
        MINUTE = self::SECOND*60,
        HOUR = self::MINUTE*60,
        DAY = self::HOUR*24,
        YEAR = self::DAY*365,
        MONDAY = 1,
        TUESDAY = 2,
        WEDNESDAY = 3,
        THURSDAY = 4,
        FRIDAY = 5,
        SATURDAY = 6,
        SUNDAY = 7,

        F_DAY_2_DIGIT = "d",
        F_DAY_OF_WEEK = "w",
        F_DAY_OF_WEEK_MON_SUN = "N",
        F_MONTH = "mm",
        F_YEAR_2_DIGIT = "yy",
        F_YEAR_4_DIGIT = "YY",

        F_DATE = "d/m/Y",
        F_TIME_STAMP = "U",
        F_TIME = "H:i:s",
        F_HOUR_MINUTE = "H:i",
        F_DATE_TIME_FULL = self::F_DATE." ".self::F_TIME,
        F_DATE_TIME_NO_SECOND = self::F_DATE." ".self::F_HOUR_MINUTE,
        F_DATE_DAY_MONTH = "d/m",
        F_DAY_OF_WEEK_HOUR_MINUTE = "N H:i",
        F_DAY_HOUR_MINUTE = self::F_DAY_2_DIGIT." ".self::F_HOUR_MINUTE ,

        F_MONTH_OF_YEAR = "n",
        F_DAY_OF_MONTH_TWO_DIGIT = "d",

        JAVASCRIPT_DATE_FORMAT = "Y/m/d",
        JAVASCRIPT_DATE_TIME_FORMAT = "Y/m/d H:i"
    ;
    

    public static function _getTimeZone() : DateTimeZone
    {
        return new DateTimeZone(self::DEFAULT_TIME_ZONE);
    }

    public static function _inst(bool $timeZone = false) : DateTime
    {
        $dateTimeZone = null;
        if($timeZone){
            $dateTimeZone = self::_getTimeZone();
        }
        $date = new DateTime('now', $dateTimeZone);
        return $date;
    }

    public static function _getTimeStamp(bool $timeZone = true) : int
    {
        return self::_inst($timeZone)->getTimestamp();
    }

    public static function _getTimeStampMilli() : int
    {
        return round(microtime(true) * 1000);
    }

    public static function _microTimeStampToFormat(int $timeStamp, string $format, bool $timeZone = true) : string
    {
        $timeStamp = abs($timeStamp);
        return self::_timeStampToFormat(intval($timeStamp/1000), $format, $timeZone);
    }

    public static function _timeStampToFormat(int $timeStamp, string $format, bool $timeZone = true) : string
    {
        $date = DateTime::createFromFormat(self::F_TIME_STAMP, $timeStamp);
        if($timeZone){
            $date->setTimezone(self::_getTimeZone());
        }
        return $date->format($format);
    }

    public static function _formatToTimeStamp(string $date, string $format, bool $timeZone = true) : int
    {
        if(self::_isValidDate($date, $format)){
            $dateTimeZone = null;
            if($timeZone){
                $dateTimeZone = self::_getTimeZone();
            }
            $date = DateTime::createFromFormat($format, $date, $dateTimeZone);
            return intval($date->format(self::F_TIME_STAMP));
        }
        return 0;
    }

    public static function _getTimeStampBeginDay(string $date, bool $timeZone = true) : int
    {
        if(self::_isValidDate($date, self::F_DATE)){
            return self::_formatToTimeStamp($date . " 00:00:00", CustomDateTime::F_DATE_TIME_FULL, $timeZone);
        }else{
            return 0;
        }
    }


    public static function _dateInterval(int $year, int $month, int $day, int $hour, int $min, int $sec) : \DateInterval
    {
        return new \DateInterval("P" . $year . "Y" . $month . "M" . $day . "DT" . $hour . "H" . $min . "M" . $sec . "S");
    }


    public static function _isValidDate($date, string $format){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function _isValidDayOfWeek(int $dayOfWeek){
        return $dayOfWeek > 0 && $dayOfWeek < 8;
    }

    public static function _getDayTranslation(string $language, string $dayIndex) :string
    {
        $dayString = "";
        if($dayIndex !== "" && is_numeric($dayIndex)){
            $i18n = AdminI18_C::_getInstance($language);
            switch ($dayIndex){
                case strval(CustomDateTime::MONDAY) :
                    $dayString = AdminI18_C::_getValueFromKey(AdminI18::DAY_MONDAY, $i18n);
                    break;
                case strval(CustomDateTime::TUESDAY) :
                    $dayString = AdminI18_C::_getValueFromKey(AdminI18::DAY_TUESDAY, $i18n);
                    break;
                case strval(CustomDateTime::WEDNESDAY) :
                    $dayString = AdminI18_C::_getValueFromKey(AdminI18::DAY_WEDNESDAY, $i18n);
                    break;
                case strval(CustomDateTime::THURSDAY) :
                    $dayString = AdminI18_C::_getValueFromKey(AdminI18::DAY_THURSDAY, $i18n);
                    break;
                case strval(CustomDateTime::FRIDAY) :
                    $dayString = AdminI18_C::_getValueFromKey(AdminI18::DAY_FRIDAY, $i18n);
                    break;
                case strval(CustomDateTime::SATURDAY) :
                    $dayString = AdminI18_C::_getValueFromKey(AdminI18::DAY_SATURDAY, $i18n);
                    break;
                case strval(CustomDateTime::SUNDAY) :
                    $dayString = AdminI18_C::_getValueFromKey(AdminI18::DAY_SUNDAY, $i18n);
                    break;
            }
        }
        return $dayString;
    }


    public static function _intToString(int $int, bool $timeZone = true){
        return self::_timeStampToFormat($int, self::F_DAY_HOUR_MINUTE, $timeZone);
    }
}