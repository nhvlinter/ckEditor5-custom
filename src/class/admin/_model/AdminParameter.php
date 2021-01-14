<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 19-03-20
 * Time: 16:00
 */

namespace salesteck\admin;


use salesteck\config\Config;
use salesteck\utils\File;

class AdminParameter
{

    const
        PARAM_MAX_IMAGE_SIZE =              "PARAM_MAX_IMAGE_SIZE",
        PARAM_CLEAN_CHECK_IN =              "PARAM_CLEAN_CHECK_IN"
    ;

    public static function _maxImageSize(){
        $maxUploadSize = Config::_getMaxUploadSize();
        $maxPostSize = Config::_getMaxPostSize();
        $maxImageSize = AdminParameter_C::getParameter(self::PARAM_MAX_IMAGE_SIZE);
        if($maxImageSize !== null){
            $maxImageSize = intval($maxImageSize);
        }else{
            $maxImageSize = 500;
        }
        $maxImageSize = intval($maxImageSize * File::KO);
        return min($maxPostSize, $maxUploadSize, $maxImageSize);
    }
}