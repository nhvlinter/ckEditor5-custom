<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 07-07-20
 * Time: 16:38
 */

namespace salesteck\DataCard;


class Options implements \JsonSerializable
{

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Private parameters
     */

    /** @var string Table to get the information from */
    private $_table = "";

    /** @var string Column name containing the value */
    private $_value = "";

    /** @var string[] Column names for the label(s) */
    private $_label = array();

    /** @var integer Row limit */
    private $_limit = null;

    /** @var callable Callback function to do rendering of labels */
    private $_renderer = null;

    /** @var callback Callback function to add where conditions */
    private $_where = null;

    /** @var string ORDER BY clause */
    private $_order = null;

    private $_manualAdd = array();

    /**
     * Options constructor.
     */
    private function __construct()
    {

    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Public methods
     */

    public static function _inst() : self
    {
        return new self();
    }

    public function table(string $tableName = ""){
        if($tableName !== ""){
            $this->_table = $tableName;
        }
        return $this;
    }

    /**
     * Get / set the column(s) to use as the label value of the options
     *
     * @param  null|string|string[] $_ null to get the current value, string or
     *   array to get.
     * @return Options|string[] Self if setting for chaining, array of values if
     *   getting.
     */
    public function label ( $_=null )
    {
        if ( $_ === null ) {
            return $this;
        }
        else if ( is_string($_) ) {
            $this->_label = array( $_ );
        }
        else {
            $this->_label = $_;
        }

        return $this;
    }
    public function value(string $columnValue = ""){

        if($columnValue !== ""){
            $this->_value = $columnValue;
        }
        return $this;
    }
    private function exec(){

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