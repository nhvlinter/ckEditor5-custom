<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 09-05-20
 * Time: 17:02
 */
namespace salesteck\takeAwayDelivery;


use JsonSerializable;
use salesteck\Db\Db;
use salesteck\utils\CustomDateTime;

class TimeOptions implements JsonSerializable
{

    private $label, $value, $timeStampStart, $timeStampEnd;

    /**
     * TimeOptions constructor.
     * @param string $label
     * @param string $value
     * @param int $timeStampStart
     * @param int $timeStampEnd
     */
    public function __construct(string $label, string $value, int $timeStampStart, int $timeStampEnd)
    {
        $this->label = $label;
        $this->value = $value;
        $this->timeStampStart = $timeStampStart;
        $this->timeStampEnd = $timeStampEnd;
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
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getTimeStampStart(): int
    {
        return $this->timeStampStart;
    }

    /**
     * @param int $timeStampStart
     */
    public function setTimeStampStart(int $timeStampStart)
    {
        $this->timeStampStart = $timeStampStart;
    }

    /**
     * @return int
     */
    public function getTimeStampEnd(): int
    {
        return $this->timeStampEnd;
    }

    /**
     * @param int $timeStampEnd
     */
    public function setTimeStampEnd(int $timeStampEnd)
    {
        $this->timeStampEnd = $timeStampEnd;
    }








    public static function inst(int $timeStampStart, int $timeStampEnd) : ? self
    {
        if( $timeStampStart > 0 && $timeStampEnd > 0 && $timeStampEnd > $timeStampStart ){
            $timeStart = CustomDateTime::_timeStampToFormat($timeStampStart, CustomDateTime::F_HOUR_MINUTE, true);
            $timeEnd = CustomDateTime::_timeStampToFormat($timeStampEnd, CustomDateTime::F_HOUR_MINUTE, true);
            return new self("$timeStart - $timeEnd", "$timeStampStart|$timeStampEnd", $timeStampStart, $timeStampEnd);
        }
        return null;
    }

    public static function instFromStr(string $time) : ? self
    {
        if(strpos($time, Db::ARRAY_DELIMITER)){
            $times = explode(Db::ARRAY_DELIMITER, $time);
            if(sizeof($times) > 1 ){
                $timeStampStart = $times[0];
                $timeStampStart = is_numeric($timeStampStart) ? intval($timeStampStart) : $timeStampStart;
                $timeStampEnd = $times[1];
                $timeStampEnd = is_numeric($timeStampEnd) ? intval($timeStampEnd) : $timeStampEnd;
                if(gettype($timeStampStart) ===  gettype(0) && gettype($timeStampEnd) === gettype(0) ){
                    return self::inst($timeStampStart, $timeStampEnd);
                }
            }
        }
        return null;
    }

    public static function _compare(self $obj, self $obj2){
        if($obj->timeStampStart === $obj2->timeStampStart){
            return ($obj->timeStampEnd <= $obj2->timeStampEnd ) ? -1 : 1;
        }else{
            return ($obj->timeStampStart < $obj2->timeStampStart ) ? -1 : 1;
        }
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
}