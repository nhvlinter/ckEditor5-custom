<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 30-04-20
 * Time: 14:45
 */
namespace salesteck\takeAwayDelivery;

use DateTime;
use salesteck\_interface\DbController;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\merchant\Merchant_C;
use salesteck\utils\CustomDateTime;

class TakeAwayHours_C extends Db implements DbController
{
    public const
        TABLE = "_hours_take_away"
    ;

    public const DATE_FORMAT = CustomDateTime::F_DATE;

    public static function _isTableEmpty() : bool
    {
        $isEmpty = true;
        $sql = self::_getSql();
        if($sql->select()){
            $count = $sql->count();
            $isEmpty = $count < 1;
        }
        return $isEmpty;
    }

    static function _getSql(): Sql
    {
        $sql = Sql::_inst(self::TABLE);
        return $sql->idColumn(self::_col_id);
    }

    public static function _limitTimeToInt(string $limitTime){
        $debug = [];
        $returnVal = 0;
        $position = strpos($limitTime, '|');
        if($position !== false){
            $split = explode(Db::ARRAY_DELIMITER, $limitTime);
            if(sizeof($split) > 0){
                $debug['split'] = $split;
                $firstVal = is_numeric($split[0]) ?  intval($split[0]) : 0;
                $debug['firstVal'] = $firstVal;
                $secondVal = $split[1];
                $debug['secondVal'] = $secondVal;
                if($firstVal > 0 ){
                    $debug['split'] = $split;
                    if($secondVal === "min"){
                        $returnVal = $minToInt = $firstVal * CustomDateTime::MINUTE;
                        $debug['minToInt'] = $minToInt;
                    }elseif ($secondVal === "hour"){
                        $returnVal = $hourToInt = $firstVal * CustomDateTime::HOUR;
                        $debug['hourToInt'] = $hourToInt;
                    }elseif ($secondVal === "day"){
                        $returnVal = $dayToInt = $firstVal * CustomDateTime::DAY;
                        $debug['dayToInt'] = $dayToInt;
                    }else{
                        $returnVal = 0;
                    }
                }else{
                    $returnVal = 0;
                }
            }
        }
        $debug['returnVal'] = $returnVal;
//        echo json_encode($debug);
        return $returnVal;
    }

    public static function _stepTimeToInt(string $stepTime){
        return self::_limitTimeToInt($stepTime);
    }


    public static function _getTakeAwayHoursByDayOfWeek ($dayOfWeek, array $columnsValues = [
        self::_col_is_enable => 1
    ]) : array
    {
        $returnVal = [];
        $sql = self::_getSql();
        foreach ($columnsValues as $column => $value){
            if(is_string($column) && $column !== ""){
                $sql->equal(self::TABLE, $column, $value);
            }
        }
        $sql->contain(self::TABLE, self::_col_days, $dayOfWeek);
        if($sql->select()){
            $returnVal = $sql->result();
        }
        return $returnVal;
    }

    public static function _getDateOptions (int $maxDays, string $language, string $merchantId)
    {
        $maxDays = $maxDays < 0 ? 1 : $maxDays;
        $arrayResult = [];
        $debug = [];
        $nowTimeStamp = CustomDateTime::_getTimeStamp();
        $debug['nowTimeStamp'] = $nowTimeStamp;
        $debug['nowDateTime'] = CustomDateTime::_timeStampToFormat($nowTimeStamp, CustomDateTime::F_DATE_TIME_FULL);

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

            $hoursRow = self::_getTakeAwayHoursByDayOfWeek($processDayOfWeek, [
                self::_col_is_enable => intval(true),
                Merchant_C::_col_merchant_id_code => $merchantId
            ]);
            $debug['hoursRow'] = $hoursRow;
            if(sizeof($hoursRow) > 0){
                foreach ($hoursRow as $row){
                    if(
                        array_key_exists(self::_col_start_time, $row) &&
                        array_key_exists(self::_col_limit_time, $row)
                    ){
                        $startTime = intval($row[self::_col_start_time]);
                        $debug['startTime'] = $startTime;
                        $limitTime = self::_limitTimeToInt($row[self::_col_limit_time]);
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

        }
        return $arrayResult;
    }

    public static function _getHoursOptions (string $date)
    {
        $arrayResult = [];
        if(CustomDateTime::_isValidDate($date, CustomDateTime::F_DATE)) {
            $arrayOptions = [];
            $debug = [];
            $dateTimeStamp = CustomDateTime::_formatToTimeStamp($date, CustomDateTime::F_DATE);
            $debug['dateTimeStamp'] = $dateTimeStamp;
            $dayOfWeek = CustomDateTime::_timeStampToFormat($dateTimeStamp, CustomDateTime::F_DAY_OF_WEEK_MON_SUN);
            $debug['dayOfWeek'] = $dayOfWeek;

            $hoursRow = self::_getTakeAwayHoursByDayOfWeek($dayOfWeek);
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

                            $stepTime = self::_stepTimeToInt($row[self::_col_time_step]);
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
//            echo "<pre>" . json_encode($debug, JSON_PRETTY_PRINT) . "</pre>";
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