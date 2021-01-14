<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 29-12-20
 * Time: 02:31
 */

class ContentEditor
{

    private const
        ELEMENT_NAME = '${elementName}$',
        START_STRING = "<!--{".self::ELEMENT_NAME."}-->",
        END_STRING = "<!--/{".self::ELEMENT_NAME."}-->"
    ;

    public static function _tagExist(string $content, string $key){

        $firstString = str_replace(self::ELEMENT_NAME, $key, self::START_STRING);
        $secondString = str_replace(self::ELEMENT_NAME, $key, self::END_STRING);
        $position1 = strpos($content, $firstString);
        $position2 = strpos($content, $secondString);
        return $position1 !== false && $position2 !== false && $position1 !== $position2 && $position1 < $position2;
    }

    /**
     * return the html email template as string after inserting the appropriate part
     * + insert $value between <!--$key--> & <!--/$key-->
     * @param string $content
     * @param array $element
     * @return string
     */
    public static function _insertBetweenTag(string $content, array $element = []) : string
    {
        if($content !== ""){
            foreach ($element as $key => $value){
                $firstString = str_replace(self::ELEMENT_NAME, $key, self::START_STRING);
                $secondString = str_replace(self::ELEMENT_NAME, $key, self::END_STRING);
                $position1 = strpos($content, $firstString);
                $position2 = strpos($content, $secondString);
                if ($position1 !== false && $position2 !== false && $position1 !== $position2 && $position1 < $position2){

                    $position1 +=  strlen($firstString);

                    $content =  substr_replace($content, $value, $position1, 0);

                }
            }
        }
        return $content;
    }

    /**
     * @param string $emailContent
     * @param array $tagElements
     * @return string
     */
    public static function _removeBetweenTag(string $emailContent, array $tagElements = []) : string
    {
        if($emailContent !== ""){
            foreach ($tagElements as $key){
                $firstString = str_replace(self::ELEMENT_NAME, $key, self::START_STRING);
                $secondString = str_replace(self::ELEMENT_NAME, $key, self::END_STRING);
                $position1 = strpos($emailContent, $firstString);
                $position2 = strpos($emailContent, $secondString);
                if ($position1 !== false && $position2 !== false && $position1 !== $position2 && $position1 < $position2){
                    $position1 +=  strlen($firstString);

                    $start = substr($emailContent, 0, $position1);

                    $end = substr($emailContent, $position2);

                    $emailContent =  $start.$end;

                }
            }
        }
        return $emailContent;
    }

    /**
     * return the html email template as string after inserting the appropriate part
     * + insert $value between <!--$key--> & <!--/$key-->
     * @param string $emailContent
     * @param array $element
     * @return string
     */
    public static function _replaceBetweenTag(string $emailContent, array $element) : string
    {
        if($emailContent !== ""){
            $arrayKey = array_keys($element);
            $emailContent = self::_removeBetweenTag($emailContent, $arrayKey);
            $emailContent = self::_insertBetweenTag($emailContent, $element);
        }
        return $emailContent;
    }

    /**
     * return the html email template as string after inserting the appropriate part
     * + insert $value between <!--$key--> & <!--/$key-->
     *
     * @param string $content
     * @param string $key
     *
     * @param string $value
     *
     * @return string
     * @internal param array $element
     */
    public static function _insertBetweenTag2(string $content, string $key, string $value) : string
    {
        if($content !== ""){
            $firstString = str_replace(self::ELEMENT_NAME, $key, self::START_STRING);
            $secondString = str_replace(self::ELEMENT_NAME, $key, self::END_STRING);
            $position1 = strpos($content, $firstString);
            $position2 = strpos($content, $secondString);
            if ($position1 !== false && $position2 !== false && $position1 !== $position2 && $position1 < $position2){

                $position1 +=  strlen($firstString);

                $content =  substr_replace($content, $value, $position1, 0);

            }
        }
        return $content;
    }
}