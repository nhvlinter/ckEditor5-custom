<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 14-08-20
 * Time: 14:41
 */

namespace salesteck\DataCard;


class DataCard_Ext
{

    /**
     * Read a value from a data structure, using Javascript dotted object
     * notation. This is the inverse of the `_writeProp` method and provides
     * the same support, matching DataTables' ability to read nested JSON
     * data objects.
     *
     * @param  string $fieldName  Javascript dotted object name to write to
     * @param  array  $dataRow  Data source array to read from
     * @return mixed         The read value, or null if no value found.
     * @private
     */
    protected static function _readProp ( string $fieldName, array $dataRow )
    {
        // check if field's contains '.'
        if ( strpos($fieldName, '.') === false ) {
            // if field's name doesn't contains '.'
            // return $dataRow[ $fieldName ] | null
            return isset( $dataRow[ $fieldName ] ) ?
                $dataRow[ $fieldName ] :
                null;
        }

        // if field's contains '.'
        $arrayNames = explode( '.', $fieldName );
        // cast $dataRow to $inner variable for looping
        $inner = $dataRow;

        // loop through $inner variable and i
        for ( $i=0 ; $i < count($arrayNames)-1 ; $i++ ) {
            // if can't find the v
            if ( ! isset( $inner[ $arrayNames[$i] ] ) ) {
                return null;
            }

            $inner = $inner[ $arrayNames[$i] ];
        }

        if ( isset( $names[count($arrayNames)-1] ) ) {
            $idIndex = $arrayNames[count($arrayNames)-1];

            return isset( $inner[ $idIndex ] ) ?
                $inner[ $idIndex ] :
                null;
        }

        return null;
    }

    /**
     * Write the field's value to an array structure, using Javascript dotted
     * object notation to indicate JSON data structure. For example `name.first`
     * gives the data structure: `name: { first: ... }`. This matches DataTables
     * own ability to do this on the client-side, although this doesn't
     * implement implement quite such a complex structure (no array / function
     * support).
     *
     * @param  array  &$out   Array to write the data to
     * @param  string  $name  Javascript dotted object name to write to
     * @param  mixed   $value Value to write
     * @throws \Exception Information about duplicate properties
     * @private
     */
    protected function _writeProp( &$out, $name, $value )
    {
        if ( strpos($name, '.') === false ) {
            $out[ $name ] = $value;
            return;
        }

        $names = explode( '.', $name );
        $inner = &$out;
        for ( $i=0 ; $i<count($names)-1 ; $i++ ) {
            $loopName = $names[$i];

            if ( ! isset( $inner[ $loopName ] ) ) {
                $inner[ $loopName ] = array();
            }
            else if ( ! is_array( $inner[ $loopName ] ) ) {
                throw new \Exception(
                    'A property with the name `'.$name.'` already exists. This '.
                    'can occur if you have properties which share a prefix - '.
                    'for example `name` and `name.first`.'
                );
            }

            $inner = &$inner[ $loopName ];
        }

        if ( isset( $inner[ $names[count($names)-1] ] ) ) {
            throw new \Exception(
                'Duplicate field detected - a field with the name `'.$name.'` '.
                'already exists.'
            );
        }

        $inner[ $names[count($names)-1] ] = $value;
    }
}