<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 08-10-19
 * Time: 02:55
 */

namespace salesteck\utils;


class Bootstrap
{
    private const GRID_SYSTEM = 12;

    private static function getCompatibleItemPerRow(int $itemPerRow) : int
    {
        $arrayDebug = [];
        $arrayDebug["itemPerRow"] = $itemPerRow;

        $modulo = self::GRID_SYSTEM % $itemPerRow;
        $arrayDebug["modulo"] = $modulo;

        $result = self::GRID_SYSTEM / $itemPerRow;
        $arrayDebug["result"] = $result;

        $alternative = self::GRID_SYSTEM / intval(round($result));
        $arrayDebug["alternative"] = $alternative;

        $compatibleItemPerRow = $modulo === 0 ? $itemPerRow : $alternative;
        $arrayDebug["compatibleItemPerRow"] = $compatibleItemPerRow;

        Debug::_exposeVariable($arrayDebug);
        return $compatibleItemPerRow;
    }

    private static function _getColumnDisplayGrid(int $elementCount, int $itemPerRow){
        $arrayDebug = [];
        $arrayDebug["elementCount"] = $elementCount;
        $arrayDebug["itemPerRow"] = $itemPerRow;

        $itemPerRow = self::getCompatibleItemPerRow($itemPerRow);
        $arrayDebug["itemPerRowResult"] = $itemPerRow;

        $rowCount = $elementCount > $itemPerRow ?  intval(floor($elementCount /$itemPerRow))  : 0;
        $arrayDebug["itemPerRowResult"] = $itemPerRow;

        $elementLeft = $elementCount - ($rowCount*$itemPerRow);
        $arrayDebug["elementLeft"] = $elementLeft;

        $columnDisplayGrid = intval(floor(self::GRID_SYSTEM / $elementLeft));
        $arrayDebug["columnDisplayGrid"] = $columnDisplayGrid;

        Debug::_exposeVariable($arrayDebug);
        return $columnDisplayGrid;
    }


    private static function _getStartIndexModify(int $elementCount, $itemPerRow){
        $arrayDebug = [];
        $arrayDebug["elementCount"] = $elementCount;

        $itemPerRow = self::getCompatibleItemPerRow($itemPerRow);
        $arrayDebug["itemPerRow"] = $itemPerRow;
        $rowCount = $elementCount > $itemPerRow ? intval(floor($elementCount /$itemPerRow))  : 0;
        $arrayDebug["rowCount"] = $rowCount;
        $elementLeft = $elementCount - ($rowCount*$itemPerRow);
        $arrayDebug["elementLeft"] = $elementLeft;
        $startIndex = $rowCount > 0 ? $elementCount - ($elementLeft) : 0;
        $arrayDebug["startIndex"] = $startIndex;
        Debug::_exposeVariable($arrayDebug);
        return $startIndex;
    }

    public static function _getColDisplayFromIndex(int $position, int $elementCount, int $itemPerRow){
        $arrayDebug = [];
        $arrayDebug["position"] = $position;
        $arrayDebug["elementCount"] = $elementCount;
        $arrayDebug["itemPerRow"] = $itemPerRow;
        $columnDisplayFromIndex = self::GRID_SYSTEM  / $itemPerRow;
        $arrayDebug["columnDisplayFromIndex"] = $columnDisplayFromIndex;
        $startIndex =  self::_getStartIndexModify($elementCount, $itemPerRow);
        $arrayDebug["startIndex"] = $startIndex;
        if($position >= $startIndex){
            $columnDisplayFromIndex=  self::_getColumnDisplayGrid($elementCount, $itemPerRow);
            $arrayDebug["columnDisplayFromIndex : "] = $columnDisplayFromIndex;
        }
        Debug::_exposeVariable($arrayDebug);
        return$columnDisplayFromIndex;
    }


}