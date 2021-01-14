<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 21-04-20
 * Time: 17:09
 */

namespace salesteck\_base;


use salesteck\Db\Db;


/**
 * Class Image_c
 * @package salesteck\_base
 */
class Image_c extends Db
{

    public const PDF_EXT = ["pdf"];

    public const ALLOWED_IMAGE_EXT = [
        'png', 'jpg', 'jpeg', 'gif', 'webp'
    ];

    public const SRC = "/upload/image/";
}