<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 16-06-20
 * Time: 12:43
 */

namespace salesteck\DataCard;


use salesteck\utils\String_Helper;

class Field extends DataCard_Ext implements \JsonSerializable
{
    private
        $alias = null,
        $name = "",
        $_validator = [],
        $_options = [],
        $upload = null,
        $edit = true,
        $create = true,
        $remove = true
    ;







    /** @var mixed */
    private $_getFormatter = null;

    /** @var mixed */
    private $_getFormatterOpts = null;

    /** @var mixed */
    private $_setFormatter = null;

    /** @var mixed */
    private $_setFormatterOpts = null;

    /** @var mixed */
    private $_value = null;

    /**
     * @param string $fieldName
     * @param string $fieldAlias
     *
     * @return \salesteck\DataCard\Field
     * @throws \Exception
     */
    public static function _inst($fieldName, $fieldAlias = ""){

//        try{
            if (String_Helper::_isStringNotEmpty($fieldName)){
                return new self($fieldName, $fieldAlias);
            }else{
                throw new \Exception("Field data & name can't be both empty");
            }
//        }
//        return null;
    }

    /**
     * Field constructor.
     * @param string|null $name
     * @param string      $alias
     */
    private function __construct($name, $alias)
    {
        $this->name = $name;
        $this->alias = is_string($alias) ? $alias : "";
    }


    /**
     * Get / set a get value. If given, then this value is used to send to the
     * client-side, regardless of what value is held by the database.
     *
     *     getter
     * @return mixed $_value if used as a getter, or self if used
     *     as a setter.
     */
    public function getValue ()
    {
        return $this->_value;
    }


    /**
     * Get / set a set value. If given, then this value is used to write to the
     * database regardless of what data is sent from the client-side.
     *
     * @param callable|string|number $_ Value to set, or no value to use as a
     *     getter
     * @return callable|string|self Value if used as a getter, or self if used
     *     as a setter.
     */
    public function setValue ( $_=null )
    {
        $this->_value = $_;
        return $this;
    }


    /**
     * Get formatter for the field's data.
     *
     * When the data has been retrieved from the server, it can be passed through
     * a formatter here, which will manipulate (format) the data as required. This
     * can be useful when, for example, working with dates and a particular format
     * is required on the client-side.
     *
     * Editor has a number of formatter available with the {@link Format} class
     * which can be used directly with this method.
     *  @param callable|string $_ Value to set if using as a getter. Can be given as
     *    a closure function or a string with a reference to a function that will
     *    be called with call_user_func().
     *  @param mixed $opts Variable that is passed through to the get formatting
     *    function - can be useful for passing through extra information such as
     *    date formatting string, or a required flag. The actual options available
     *    depend upon the formatter used.
     *  @return self for chaining
     */
    public function getFormatter ( $_ = null, $opts=null ) : self
    {
        if( is_callable($_) ){
            $this->_getFormatter = $_;

            if ( $opts !== null ) {
                $this->_getFormatterOpts = $opts;
            }

        }
        return $this;
    }


    /**
     * @return bool
     */
    public function hasGetValueProcessor () : bool
    {
        return $this->_getFormatter !== null;
    }


    /**
     * @return bool
     */
    public function hasSetValueProcessor () : bool
    {
        return $this->_value !== null || $this->_setFormatter !== null;
    }





    /**
     * Set formatter for the field's data.
     *
     * When the data has been retrieved from the server, it can be passed through
     * a formatter here, which will manipulate (format) the data as required. This
     * can be useful when, for example, working with dates and a particular format
     * is required on the client-side.
     *
     * Editor has a number of formatter available with the {@link Format} class
     * which can be used directly with this method.
     *  @param callable|string $_ Value to set if using as a setter. Can be given as
     *    a closure function or a string with a reference to a function that will
     *    be called with call_user_func().
     *  @param mixed $opts Variable that is passed through to the get formatting
     *    function - can be useful for passing through extra information such as
     *    date formatting string, or a required flag. The actual options available
     *    depend upon the formatter used.
     *  @return callable|string|self The set formatter if no parameter is given, or
     *    self if used as a setter.
     */
    public function setFormatter ( $_=null, $opts=null )  : self
    {
        if( is_callable($_) ){
            $this->_setFormatter = $_;

            if ( $opts !== null ) {
                $this->_setFormatterOpts = $opts;
            }

        }
        return $this;
    }

    /**
     * Get the value from `_[g|s]etValue` - taking into account if it is callable
     * function or not
     *
     * @param  mixed $fieldValue Value to be evaluated
     * @return mixed      Value assigned, or returned from the function
     */
    private function _getAssignedValue ($fieldValue )
    {
        if ( $this->_value !== null ) {
            $assignedValue = $this->_value;
            $fieldValue = is_callable($assignedValue) && is_object($assignedValue) ?
                $assignedValue() : $assignedValue;
        }
        return $fieldValue;
    }

    /**
     * Format the data row with
     *      $this->_getFormatter (if exist)
     * @param $fieldValue
     * @param $dataRow
     * @return mixed
     */
    public function _getFormat( $fieldValue, $dataRow )
    {
        $fieldValue = $this->_getAssignedValue($fieldValue);
        if( is_callable($this->_getFormatter) ){
            $formatter = $this->_getFormatter;
            $formattedValue = $formatter( $fieldValue, $dataRow, $this->_getFormatterOpts );

            return $formattedValue !== null ? $formattedValue : $fieldValue;
        }else{
            return $fieldValue;
        }
    }

    /**
     * Format the data row with
     *      $this->_setFormatter (if exist)
     * @param $fieldValue
     * @param $dataRow
     * @param $dataGroup
     * @return mixed
     */
    public function _setFormat( $fieldValue, $dataRow, $dataGroup )
    {
          //TODO
          // XSS removal / checker
//        if ( $this->_xssFormat ) {
//            $val = $this->xssSafety( $val );
//        }

        $fieldValue = $this->_getAssignedValue($fieldValue);
        if( is_callable($this->_setFormatter) ){
            $formatter = $this->_setFormatter;
            $formattedValue = $formatter( $fieldValue, $dataRow, $dataGroup, $this->_setFormatterOpts );

            return $formattedValue !== null ? $formattedValue : $fieldValue;
        }else{
            return $fieldValue;
        }
    }

    /**
     * Perform XSS prevention on an input.
     *
     * @param  mixed $val Value to be escaped
//     * @return string Safe value
     */
    //TODO
    private function xssSafety ( $val ) {
//        $xss = $this->_xss;
//
//        if ( is_array( $val ) ) {
//            $res = array();
//
//            foreach ( $val as $individual ) {
//                $res[] = $xss ?
//                    $xss( $individual ) :
//                    DataTables\Vendor\Htmlaw::filter( $individual );
//            }
//
//            return $res;
//        }
//
//        return $xss ?
//            $xss( $val ) :
//            DataTables\Vendor\Htmlaw::filter( $val );
    }



    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
        CLASS PROPERTY FUNCTION PART
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return bool
     */
    public function isEdit(): bool
    {
        return $this->edit;
    }

    /**
     * @param bool $edit
     *
     * @return Field
     */
    public function setEdit(bool $edit): Field
    {
        $this->edit = $edit;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCreate(): bool
    {
        return $this->create;
    }

    /**
     * @param bool $create
     *
     * @return Field
     */
    public function setCreate(bool $create): Field
    {
        $this->create = $create;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRemove(): bool
    {
        return $this->remove;
    }

    /**
     * @param bool $remove
     *
     * @return Field
     */
    public function setRemove(bool $remove): Field
    {
        $this->remove = $remove;
        return $this;
    }





    /**
     * @return array
     */
    public function getOptions() : array
    {
        return $this->_options;
    }

    /**
     * @param null|FieldOption $options
     * @return Field
     */
    public function options($options = null) : self
    {
        if($options instanceof FieldOption){
            array_push($this->_options, $options);
        }elseif (is_array($options)){
            $this->_options = $options;
        }
        return $this;
    }

    /**
     * @param FieldUpload $upload
     * @return $this
     */
    public function upload(FieldUpload $upload){
        $this->upload = ($upload);
        return $this;
    }

    /**
     * @return null|FieldUpload
     */
    public function getUpload() : ? FieldUpload
    {
        return $this->upload;
    }

    /**
     * @param null $upload
     */
    public function setUpload($upload)
    {
        $this->upload = $upload;
    }


    /**
     * @return bool
     */
    public function isUpload() : bool
    {
        return $this->getUpload() instanceof FieldUpload;
    }

    /**
     * @param callable|null $callable
     * @return $this
     */
    public function validator($callable = null){

        if(is_callable($callable)){
            array_push($this->_validator, $callable);
        }
        return $this;
    }


    /**
     * Check the validity of the field based on the data submitted. Note that
     * this validation is performed on the wire data - i.e. that which is
     * submitted, before any setFormatter is run
     *
     * @param $fieldValue
     * @param array $dataRow Data submitted from the client-side
     * @param DataCard $dataCard DataCard instance
     * @param mixed|null $id Row id that is being validated
     *
     * @return bool|string `true` if valid, string with error message if not
     * @internal param Data $value submitted from the client-side
     */
    public function validate ( $fieldValue, $dataRow, $dataCard, $id = null )
    {
        $validators = $this->_validator ;
        $count = count( $validators );
        if ( $count === 0 ) {
            return true;
        }

        for ( $i=0 ; $i<$count ; $i++ ) {
            $validator = $this->_validator[$i];
            if(is_callable($validator)){
                $res = $validator($fieldValue, $dataRow, $this, $dataCard, $id);
                // Check if there was a validation error and if so, return it
                if ( $res !== true ) {
                    return $res;
                }
            }

        }

        // Validation methods all run, must be valid
        return true;
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