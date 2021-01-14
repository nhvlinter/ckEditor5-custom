<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 30-10-19
 * Time: 18:48
 */

namespace salesteck\Db;


class SqlConditionGroup implements \JsonSerializable
{


    private $arrayConditionGroup, $arrayCondition, $and_or;

    public static function _inst(array $arrayCondition = [], string $and_or = SqlCondition::_AND){
        return new self($arrayCondition,  $and_or);
    }

    /**
     * SqlConditionGroup constructor.
     * @param array $arrayCondition
     * @param $and_or
     */
    public function __construct(array $arrayCondition = [], string $and_or = SqlCondition::_AND)
    {
        $this->arrayCondition = $arrayCondition;
        if(!SqlCondition::_isAddOperatorValid($and_or)){
            $and_or = SqlCondition::_AND;
        }
        $this->and_or = $and_or;
        $this->arrayConditionGroup = [];
    }

    public function addCondition (SqlCondition $sqlCondition){
//        if($sqlCondition !== null && $sqlCondition instanceof SqlCondition){
//            array_push($this->arrayCondition, $sqlCondition);
//        }
//        return $this;
        if($sqlCondition !== null && $sqlCondition instanceof SqlCondition){

            if(sizeof($this->arrayCondition)===0){
                array_push($this->arrayCondition, $sqlCondition);
            }else {
                $conditionExist = false;
                foreach ($this->arrayCondition as $condition) {
                    if ($condition !== null && $condition instanceof SqlCondition) {

                        $compare = SqlCondition::_compare($condition, $sqlCondition);
                        if($compare){
                            $conditionExist = true;
                        }
                    }
                }
                if (!$conditionExist) {
                    array_push($this->arrayCondition, $sqlCondition);
                }
            }
        }
        return $this;
    }

    public function addConditionGroup (SqlConditionGroup $sqlConditionGroup){
        if($sqlConditionGroup !== null && $sqlConditionGroup instanceof SqlConditionGroup){

            if(sizeof($this->arrayConditionGroup)===0){
                array_push($this->arrayConditionGroup, $sqlConditionGroup);
            }else {
                $conditionExist = false;
                foreach ($this->arrayConditionGroup as $conditionGroup) {
                    if ($conditionGroup !== null && $conditionGroup instanceof SqlConditionGroup) {

                        $compare = self::_compare($conditionGroup, $sqlConditionGroup);
                        if($compare){
                            $conditionExist = true;
                        }
                    }
                }
                if (!$conditionExist) {
                    array_push($this->arrayConditionGroup, $sqlConditionGroup);
                }
            }
        }
        return $this;
    }

    public function setOperator (string $and_or){
        if(!SqlCondition::_isAddOperatorValid($and_or)){
            $and_or = SqlCondition::_AND;
        }
        $this->and_or = $and_or;
        return $this;
    }

    /**
     * @return string
     */
    public function getAndOr() :string
    {
        return $this->and_or;
    }





    public function getConditionString() : string
    {

        $conditionString = "";
        $arrayCondition = $this->arrayCondition;
        if(sizeof($arrayCondition) > 0 ){
            $conditionArrayToString = "";
            for ($i=0; $i < sizeof($arrayCondition); $i++){
                $condition = $arrayCondition[$i];
                if($condition !== null && $condition instanceof SqlCondition){
                    $and_or = $condition->getAndOr();
                    $stringCondition = $condition->getConditionString();
                    if($i>0){
                        $stringCondition = " $and_or $stringCondition";
                    }
                    $conditionArrayToString .= $stringCondition;
                }
            }
            $conditionString .= "$conditionArrayToString";
        }
        $arrayConditionGroup = $this->arrayConditionGroup;
        if(sizeof($arrayConditionGroup) > 0 ){
            $conditionGroupString = "";
            for ($i=0; $i < sizeof($arrayConditionGroup); $i++){
                $conditionGroup = $arrayConditionGroup[$i];
                if($conditionGroup !== null && $conditionGroup instanceof SqlConditionGroup){
                    $stringConditionGroup = $conditionGroup->getConditionString();
                    $and_or_group = $conditionGroup->getAndOr();
                    if($i>0 || $conditionString !== ""){
                        $stringConditionGroup = " $and_or_group $stringConditionGroup";
                    }
                    $conditionGroupString .= $stringConditionGroup;
                }
            }
            $conditionString .= "$conditionGroupString";
        }
        $conditionString = "( $conditionString )";
        return $conditionString;
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
        $obj = (object) $this;
        $obj->queryString = $this->getConditionString();
        return get_object_vars($obj);
    }


    public static function _compare(self $sqlConditionGroup, self $sqlConditionGroupCompare) : bool
    {
        $serialize = serialize($sqlConditionGroup);
        $serializeCompare = serialize($sqlConditionGroupCompare);
        return $serialize === $serializeCompare;
    }


}