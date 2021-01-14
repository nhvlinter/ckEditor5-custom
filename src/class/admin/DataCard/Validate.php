<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 14-07-20
 * Time: 02:51
 */

namespace salesteck\DataCard;


use salesteck\utils\FileUpload;

class Validate
{
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * File validation methods
    */
    static function fileExtensions ( array $extensions, $msg = "This file type cannot be uploaded." ) {
        return function ( $file ) use ( $extensions, $msg ) {
            if($file instanceof FileUpload){
                $ext = $file->getExtension();
                for ( $i=0, $ien=count($extensions) ; $i<$ien ; $i++ ) {
                    if ( strtolower( $ext ) === strtolower( $extensions[$i] ) ) {
                        return true;
                    }
                }
            }
            return $msg;
        };
    }

    static function fileSize ( int $fileSize, $msg = "Uploaded file is too large." ) {
        return function ( $file ) use ( $fileSize, $msg ) {
            if($file instanceof FileUpload && $file->getSize() <= $fileSize){
                return true;
            }
            return $msg;
        };
    }

}