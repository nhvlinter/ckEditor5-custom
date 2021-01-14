<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 28-04-20
 * Time: 18:11
 */
namespace salesteck\takeAwayDelivery;



use DateTime;
use salesteck\_interface\DbController;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\utils\CustomDateTime;

class DeliveryHours_C extends Db implements DbController
{
    public const
        TABLE = "_hours_delivery"
    ;

    public const
        TYPE_TAKE_AWAY = 0,
        TYPE_DELIVERY = 1
    ;

    static function _getSql(): Sql
    {
        $sql = Sql::_inst(self::TABLE);
        return $sql->idColumn(self::_col_id);
    }


    private static function _getDeliveryHoursByDayOfWeek (string $deliveryZoneId, string $dayOfWeek, bool $enable = true) : array
    {
        $returnVal = [];
        $sql = self::_getSql();
        if($enable){
            $sql->equal(self::TABLE, self::_col_is_enable, intval($enable));
        }
        $sql->equal(self::TABLE, self::_col_delivery_zone_id, $deliveryZoneId);
        $sql->contain(self::TABLE, self::_col_days, $dayOfWeek);
        if($sql->select()){
            $returnVal = $sql->result();
        }
        return $returnVal;
    }

    public static function _getDateOptions (string $deliveryZoneId, int $maxDays, string $language) : array
    {
        $maxDays = $maxDays < 0 ? 1 : $maxDays;
        $arrayResult = [];
        $debug = [];
        $debug['deliveryZoneId'] = $deliveryZoneId;
        $debug['maxDays'] = $maxDays;
        $debug['language'] = $language;

        $nowTimeStamp = CustomDateTime::_getTimeStamp();
        $debug['nowTimeStamp'] = $nowTimeStamp;

        $nowDateTime = CustomDateTime::_timeStampToFormat($nowTimeStamp, CustomDateTime::F_DATE_TIME_FULL);
        $debug['nowDateTime'] = $nowDateTime;

        $process = CustomDateTime::_inst();
        $dayOfWeekArray = DateOptions::_getDaysString($language);
        for ($i = 0; $i < $maxDays; $i ++){
            $isValid = false;
            $processTimeStamp = $process->getTimestamp();
            $debug['processTimeStamp'] = $processTimeStamp;

            $processDate = $process->format(CustomDateTime::F_DATE);
            $debug['processDate'] = $processDate;

            $processDayOfWeek = $process->format(CustomDateTime::F_DAY_OF_WEEK_MON_SUN);
            $debug['processDayOfWeek'] = $processDayOfWeek;

            $hoursRow = self::_getDeliveryHoursByDayOfWeek($deliveryZoneId, $processDayOfWeek);
            $debug['hoursRow'] = $hoursRow;

            if(sizeof($hoursRow) > 0){
                foreach ($hoursRow as $row){
                    if(
                        array_key_exists(self::_col_start_time, $row) &&
                        array_key_exists(self::_col_limit_time, $row)
                    ){
                        $startTime = intval($row[self::_col_start_time]);
                        $debug['startTime'] = $startTime;
                        $limitTime = TakeAwayHours_C::_limitTimeToInt($row[self::_col_limit_time]);
                        $debug['limitTime'] = $limitTime;

                        $beginDayTimeStamp = CustomDateTime::_getTimeStampBeginDay($processDate);
                        $debug['beginDayTimeStamp'] = $beginDayTimeStamp;



                        $beginDayDateTime = CustomDateTime::_timeStampToFormat($beginDayTimeStamp, CustomDateTime::F_DATE_TIME_FULL);
                        $debug['beginDayDateTime'] = $beginDayDateTime;

                        if($beginDayTimeStamp > 0){
                            $startTimeStamp = $beginDayTimeStamp+$startTime;
                            $debug['startTimeStamp'] = $startTimeStamp;
                            $startDateTime = CustomDateTime::_timeStampToFormat($startTimeStamp, CustomDateTime::F_DATE_TIME_FULL);
                            $debug['startDateTime'] = $startDateTime;

                            $limitTimeStamp = $startTimeStamp - $limitTime;
                            $debug['limitTimeStamp'] = $limitTimeStamp;
                            $limitDateTime = CustomDateTime::_timeStampToFormat($limitTimeStamp, CustomDateTime::F_DATE_TIME_FULL);
                            $debug['limitDateTime'] = $limitDateTime;

                            if($nowTimeStamp < $limitTimeStamp){
                                $isValid = true;
                            }

                        }
                    }
                }
            }
            if($isValid){
                $dayOfWeekString = array_key_exists($processDayOfWeek, $dayOfWeekArray) ?  $dayOfWeekArray[$processDayOfWeek] : "";

                array_push($arrayResult, new DateOptions("$dayOfWeekString - $processDate", $processDate));
            }
            $process->add(CustomDateTime::_dateInterval(0, 0, 1, 0, 0, 0));
//            echo "<pre>".json_encode($debug, JSON_PRETTY_PRINT)."</pre>";

        }
        return $arrayResult;
    }



    public static function _getHoursOptions (string $deliveryZoneId, string $date)
    {
        $arrayResult = [];
        if(CustomDateTime::_isValidDate($date, CustomDateTime::F_DATE)) {
            $arrayOptions = [];
            $debug = [];
            $dateTimeStamp = CustomDateTime::_formatToTimeStamp($date, CustomDateTime::F_DATE);
            $debug['dateTimeStamp'] = $dateTimeStamp;
            $dayOfWeek = CustomDateTime::_timeStampToFormat($dateTimeStamp, CustomDateTime::F_DAY_OF_WEEK_MON_SUN);
            $debug['dayOfWeek'] = $dayOfWeek;

            $hoursRow = self::_getDeliveryHoursByDayOfWeek($deliveryZoneId, $dayOfWeek);
            $debug['hoursRow'] = $hoursRow;
            if (sizeof($hoursRow) > 0) {
                foreach ($hoursRow as $row){
                    if (
                        array_key_exists(self::_col_start_time, $row) &&
                        array_key_exists(self::_col_limit_time, $row) &&
                        array_key_exists(self::_col_time_step, $row)
                    ) {

                        $beginDayTimeStamp = CustomDateTime::_getTimeStampBeginDay($date);
                        $debug['beginDayTimeStamp'] = $beginDayTimeStamp;

                        $beginDayDateTime = CustomDateTime::_timeStampToFormat($beginDayTimeStamp, CustomDateTime::F_DATE_TIME_FULL);
                        $debug['beginDayDateTime'] = $beginDayDateTime;
                        if ($beginDayTimeStamp > 0) {

                            $startTime = intval($row[self::_col_start_time]);
                            $debug['startTime'] = $startTime;

                            $startTimeStamp = $beginDayTimeStamp + $startTime;
                            $debug['startTimeStamp'] = $startTimeStamp;

                            $beginDate = (new DateTime())->setTimestamp($startTimeStamp);
                            $startDateTime = $beginDate->setTimezone(CustomDateTime::_getTimeZone())->format(CustomDateTime::F_DATE_TIME_FULL);
                            $debug['startDateTime'] = $startDateTime;


                            $endTime = intval($row[self::_col_end_time]);
                            $debug['endTime'] = $endTime;

                            $endTimeStamp = $beginDayTimeStamp + $endTime;
                            $debug['endTimeStamp'] = $endTimeStamp;

                            $endDate = (new DateTime())->setTimestamp($endTimeStamp);

                            if ($endTime < $startTime) {
                                $endDate->add(CustomDateTime::_dateInterval(0, 0, 1, 0, 0, 0));
                            }
                            $endDateTime = $endDate->setTimezone(CustomDateTime::_getTimeZone())->format(CustomDateTime::F_DATE_TIME_FULL);
                            $debug['endDateTime'] = $endDateTime;
                            $endTimeStamp = $endDate->getTimestamp();

                            $stepTime = TakeAwayHours_C::_stepTimeToInt($row[self::_col_time_step]);
                            $debug['stepTime'] = $stepTime;

                            if ($stepTime > 0) {
                                while ($startTimeStamp <= $endTimeStamp && ($endTimeStamp - $startTimeStamp) >= $stepTime) {
                                    $temp = [];
                                    $tempEndTimeStamp = $startTimeStamp + $stepTime;
                                    $temp['startTimeStamp'] = CustomDateTime::_timeStampToFormat($startTimeStamp, CustomDateTime::F_DATE_TIME_FULL);
                                    $temp['tempEndTime'] = CustomDateTime::_timeStampToFormat($tempEndTimeStamp, CustomDateTime::F_DATE_TIME_FULL);
                                    $temp['stepTime'] = $stepTime;
                                    $temp['endTimeStamp'] = CustomDateTime::_timeStampToFormat($endTimeStamp, CustomDateTime::F_DATE_TIME_FULL);

                                    $options = TimeOptions::inst($startTimeStamp, $tempEndTimeStamp);
                                    if($options instanceof TimeOptions){
                                        array_push($arrayOptions, $options);
                                    }
                                    $startTimeStamp = $startTimeStamp + $stepTime;
//
//                                echo "<pre>" . json_encode($temp, JSON_PRETTY_PRINT) . "</pre>";
                                }
                            } else {
                                $temp = [];
                                $tempEndTimeStamp = $endTimeStamp;
                                $temp['startTimeStamp'] = CustomDateTime::_timeStampToFormat($startTimeStamp, CustomDateTime::F_DATE_TIME_FULL);
                                $temp['tempEndTime'] = CustomDateTime::_timeStampToFormat($tempEndTimeStamp, CustomDateTime::F_DATE_TIME_FULL);
                                $temp['stepTime'] = $stepTime;
                                $temp['endTimeStamp'] = CustomDateTime::_timeStampToFormat($endTimeStamp, CustomDateTime::F_DATE_TIME_FULL);

                                $options = TimeOptions::inst($startTimeStamp, $tempEndTimeStamp);
                                if($options instanceof TimeOptions){
                                    array_push($arrayOptions, $options);
                                }
//                            echo "<pre>" . json_encode($temp, JSON_PRETTY_PRINT) . "</pre>";
                            }

                        }

                    }
                }
                $arrayOptions = array_unique($arrayOptions);
                usort($arrayOptions, 'self::_compare');
            }
            $arrayResult = $arrayOptions;
        }
        return $arrayResult;
    }


    public static function _compare(TimeOptions $left, TimeOptions $right){

        if($left->getTimeStampStart() === $right->getTimeStampStart()){
            return ($left->getTimeStampEnd() <= $right->getTimeStampEnd() ) ? -1 : 1;
        }else{
            return ($left->getTimeStampStart() < $right->getTimeStampStart() ) ? -1 : 1;
        }
    }



}